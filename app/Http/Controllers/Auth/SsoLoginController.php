<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SsoTicket;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SsoLoginController extends Controller
{
    /**
     * Terima handoff SSO dari E-Management dan login-kan mahasiswa terkait.
     *
     * Langkah validasi (fail-fast, berurutan):
     * 1. Verifikasi tanda tangan HMAC-SHA256 (hash_equals)
     * 2. Periksa kedaluwarsa
     * 3. Periksa & catat nonce (anti-replay)
     * 4. Cari user berdasarkan nim (tidak membuat user baru)
     * 5. Auth::login + redirect ke dashboard konseling
     */
    public function receive(Request $request): RedirectResponse
    {
        $request->validate([
            'nim' => ['required', 'string'],
            'expires_at' => ['required', 'integer'],
            'nonce' => ['required', 'string'],
            'signature' => ['required', 'string'],
        ]);

        if (! $this->hasValidSignature($request)) {
            abort(403);
        }

        if ($this->isExpired($request)) {
            abort(403);
        }

        if (! $this->consumeNonce($request)) {
            abort(403);
        }

        $user = User::where('nim', $request->string('nim'))->first();

        if (! $user) {
            // Fallback: cek tabel CDMI jika kolom nim di users masih null
            $cdmi = \App\Models\CDMI::where('nim', $request->string('nim'))->first();
            if ($cdmi && $cdmi->user_id) {
                $user = User::find($cdmi->user_id);
                if ($user) {
                    $user->nim = $request->string('nim');
                    $user->save();
                }
            }
        }

        if (! $user) {
            // JANGAN buat user baru — SSO ini hanya menerima handoff untuk
            // mahasiswa yang datanya sudah ada di sistem ini (via sinkronisasi
            // CDMI). Nim tak ditemukan dicatat untuk audit/investigasi.
            Log::warning('SSO: nim tidak ditemukan - '.$request->string('nim'));
            abort(403);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('user.konseling.dashboard');
    }

    /**
     * Tiket kedaluwarsa jika waktu saat ini sudah melewati expires_at.
     */
    private function isExpired(Request $request): bool
    {
        return now()->timestamp > $request->integer('expires_at');
    }

    /**
     * Simpan nonce sebagai "sudah dipakai". Insert-lalu-tangkap-error
     * (bukan cek-lalu-insert terpisah) untuk menghindari race condition
     * ketika dua request dengan nonce sama datang nyaris bersamaan.
     *
     * @return bool true jika nonce baru (berhasil disimpan), false jika
     *              nonce sudah pernah dipakai sebelumnya.
     */
    private function consumeNonce(Request $request): bool
    {
        try {
            SsoTicket::create([
                'nonce' => $request->string('nonce'),
                'nim' => $request->string('nim'),
                'used_at' => now(),
            ]);

            return true;
        } catch (QueryException $e) {
            // 23000 = integrity constraint violation (unique nonce sudah ada)
            if ($e->getCode() === '23000') {
                return false;
            }

            throw $e;
        }
    }

    /**
     * Bandingkan signature memakai hash_equals (timing-safe) — WAJIB, tidak
     * boleh diganti operator "===" karena rentan timing attack.
     */
    private function hasValidSignature(Request $request): bool
    {
        $canonical = sprintf(
            '%s|%s|%s',
            $request->string('nim'),
            $request->integer('expires_at'),
            $request->string('nonce')
        );

        $expectedSignature = hash_hmac('sha256', $canonical, (string) config('sso.secret'));

        return hash_equals($expectedSignature, (string) $request->string('signature'));
    }
}
