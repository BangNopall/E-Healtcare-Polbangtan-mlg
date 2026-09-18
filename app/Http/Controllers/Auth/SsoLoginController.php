<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\CDMI;
use App\Models\SsoTicket;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

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
    public function receive(Request $request): Response
    {
        $request->validate([
            'expires_at' => ['required', 'integer'],
            'nonce' => ['required', 'string'],
            'signature' => ['required', 'string'],
        ]);

        $identifier = (string) ($request->input('identifier') ?? $request->input('nim'));
        if (blank($identifier)) {
            return $this->renderError(
                title: 'Parameter SSO Tidak Lengkap',
                message: 'Parameter identitas Single Sign-On tidak ditemukan pada permintaan. Silakan akses kembali melalui portal E-Management.',
                errorCode: 'ERR_SSO_MISSING_PARAMETER'
            );
        }

        if (! $this->hasValidSignature($request, $identifier)) {
            return $this->renderError(
                title: 'Validasi Keamanan Gagal',
                message: 'Tanda tangan digital (HMAC) tidak valid atau tautan telah dimodifikasi. Akses ditolak untuk menjaga keamanan sistem.',
                errorCode: 'ERR_SSO_INVALID_SIGNATURE',
                identifier: $identifier,
                role: $request->input('role')
            );
        }

        if ($this->isExpired($request)) {
            return $this->renderError(
                title: 'Tiket Akses Kedaluwarsa',
                message: 'Sesi Single Sign-On Anda telah berakhir (masa berlaku tiket adalah 60 detik). Silakan kembali ke aplikasi Asrama dan klik ulang tautan menu.',
                errorCode: 'ERR_SSO_TICKET_EXPIRED',
                identifier: $identifier,
                role: $request->input('role')
            );
        }

        if (! $this->consumeNonce($request, $identifier)) {
            return $this->renderError(
                title: 'Tiket Sudah Pernah Digunakan',
                message: 'Tiket Single Sign-On ini sudah pernah digunakan sebelumnya. Setiap tiket hanya berlaku untuk satu kali masuk demi mencegah penyalahgunaan.',
                errorCode: 'ERR_SSO_NONCE_REPLAYED',
                identifier: $identifier,
                role: $request->input('role')
            );
        }

        $role = (string) $request->input('role');

        // 1. Handoff Admin: Login sebagai Admin dengan hak akses penuh (read-write)
        if ($role === 'admin') {
            $admin = User::where('email', $identifier)->where('role', 'Admin')->first()
                ?? User::where('role', 'Admin')->first();

            if (! $admin) {
                Log::critical('SSO: Akun Admin tidak ditemukan di E-Klinik untuk identitas: '.$identifier);

                return $this->renderError(
                    title: 'Akun Administrator Belum Terdaftar',
                    message: "Akun Administrator dengan email {$identifier} belum ditemukan pada sistem basis data E-Klinik. Silakan hubungi bagian pengelola sistem untuk inisialisasi akun admin klinik.",
                    errorCode: 'ERR_SSO_ADMIN_NOT_FOUND',
                    identifier: $identifier,
                    role: 'Administrator'
                );
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

                $pejabatName = $request->input('name') ?? 'Pejabat Polbangtan';

                return $this->renderError(
                    title: 'Layanan Pejabat Belum Dapat Diakses',
                    message: "Akun induk Administrator untuk penautan akses Read-Only Pejabat ({$pejabatName}) belum tersedia di klinik. Silakan inisialisasi akun admin klinik terlebih dahulu.",
                    errorCode: 'ERR_SSO_PEJABAT_HOST_NOT_FOUND',
                    identifier: $identifier,
                    role: 'Pejabat'
                );
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

            return $this->renderError(
                title: 'Data Mahasiswa Belum Terdaftar',
                message: "Data mahasiswa dengan NIM {$identifier} belum terdaftar di basis data E-Klinik Polbangtan. Silakan pastikan data Anda telah disinkronkan oleh pengelola asrama/klinik.",
                errorCode: 'ERR_SSO_STUDENT_NOT_FOUND',
                identifier: $identifier,
                role: 'Mahasiswa'
            );
        }

        $request->session()->forget(['sso_readonly', 'sso_role', 'sso_pejabat_name', 'sso_pejabat_email']);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('user.konseling.dashboard');
    }

    /**
     * Render tampilan halaman error SSO yang terformat rapi dengan HTTP status 403.
     */
    private function renderError(
        string $title,
        string $message,
        string $errorCode,
        ?string $identifier = null,
        ?string $role = null
    ): Response {
        return response()->view('auth.sso-error', [
            'title' => $title,
            'message' => $message,
            'errorCode' => $errorCode,
            'identifier' => $identifier,
            'role' => $role,
        ], 403);
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
