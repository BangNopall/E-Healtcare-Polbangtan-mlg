<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\CDMI;
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
            'expires_at' => ['required', 'integer'],
            'nonce' => ['required', 'string'],
            'signature' => ['required', 'string'],
        ]);

        $identifier = (string) ($request->input('identifier') ?? $request->input('nim'));
        if (blank($identifier)) {
            abort(403);
        }

        if (! $this->hasValidSignature($request, $identifier)) {
            abort(403);
        }

        if ($this->isExpired($request)) {
            abort(403);
        }

        if (! $this->consumeNonce($request, $identifier)) {
            abort(403);
        }

        $role = (string) $request->input('role');

        // 1. Handoff Admin: Login sebagai Admin dengan hak akses penuh (read-write)
        if ($role === 'admin') {
            $admin = User::where('email', $identifier)->where('role', 'Admin')->first()
                ?? User::where('role', 'Admin')->first();

            if (! $admin) {
                Log::critical('SSO: Akun Admin tidak ditemukan di E-Klinik untuk identitas: '.$identifier);
                abort(403);
            }

            $request->session()->forget(['sso_readonly', 'sso_role', 'sso_pejabat_name', 'sso_pejabat_email']);
            Auth::login($admin);
            $request->session()->regenerate();

            return redirect()->route('konseling.dashboard');
        }

        // 2. Handoff Pejabat: Login sebagai Admin namun dengan flag READ-ONLY di session
        if ($role === 'pejabat') {
            $admin = User::where('role', 'Admin')->first();

            if (! $admin) {
                Log::critical('SSO: Akun Admin tidak ditemukan di E-Klinik untuk handoff Pejabat: '.$identifier);
                abort(403);
            }

            Auth::login($admin);
            $request->session()->regenerate();

            session([
                'sso_readonly' => true,
                'sso_role' => 'Pejabat',
                'sso_pejabat_name' => $request->input('name') ?? 'Pejabat Polbangtan',
                'sso_pejabat_email' => $identifier,
            ]);

            return redirect()->route('konseling.dashboard');
        }

        // 3. Handoff Mahasiswa: Cari berdasarkan NIM, login, redirect ke dashboard konseling mahasiswa
        $user = User::where('nim', $identifier)->first();

        if (! $user) {
            // Fallback: cek tabel CDMI jika kolom nim di users masih null
            $cdmi = CDMI::where('nim', $identifier)->first();
            if ($cdmi && $cdmi->user_id) {
                $user = User::find($cdmi->user_id);
                if ($user) {
                    $user->nim = $identifier;
                    $user->save();
                }
            }
        }

        if (! $user) {
            // JANGAN buat user baru — SSO ini hanya menerima handoff untuk
            // mahasiswa yang datanya sudah ada di sistem ini (via sinkronisasi
            // CDMI). Nim tak ditemukan dicatat untuk audit/investigasi.
            Log::warning('SSO: nim tidak ditemukan - '.$identifier);
            abort(403);
        }

        $request->session()->forget(['sso_readonly', 'sso_role', 'sso_pejabat_name', 'sso_pejabat_email']);
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
    private function consumeNonce(Request $request, string $identifier): bool
    {
        try {
            SsoTicket::create([
                'nonce' => $request->string('nonce'),
                'nim' => $identifier,
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
    private function hasValidSignature(Request $request, string $identifier): bool
    {
        $secret = (string) config('sso.secret');
        $role = (string) $request->input('role');
        $expiresAt = $request->integer('expires_at');
        $nonce = (string) $request->input('nonce');
        $signature = (string) $request->input('signature');

        // Signature v2 untuk Admin & Pejabat: "{identifier}|{role}|{expires_at}|{nonce}"
        if (in_array($role, ['admin', 'pejabat'])) {
            $canonical = sprintf('%s|%s|%s|%s', $identifier, $role, $expiresAt, $nonce);
            $expectedSignature = hash_hmac('sha256', $canonical, $secret);

            return hash_equals($expectedSignature, $signature);
        }

        // Signature v1 Legacy (Mahasiswa): "{nim}|{expires_at}|{nonce}"
        $canonicalV1 = sprintf('%s|%s|%s', $identifier, $expiresAt, $nonce);
        $expectedV1 = hash_hmac('sha256', $canonicalV1, $secret);
        if (hash_equals($expectedV1, $signature)) {
            return true;
        }

        // Signature v2 Mahasiswa (jika menyertakan role): "{identifier}|mahasiswa|{expires_at}|{nonce}"
        if ($role === 'mahasiswa') {
            $canonicalV2 = sprintf('%s|%s|%s|%s', $identifier, $role, $expiresAt, $nonce);
            $expectedV2 = hash_hmac('sha256', $canonicalV2, $secret);

            return hash_equals($expectedV2, $signature);
        }

        return false;
    }
}
