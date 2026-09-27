<?php

use App\Models\User;
use App\Models\RPD;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('local');
    Storage::fake('public');
});

test('dompdf konfigurasi menonaktifkan remote dan javascript untuk mencegah SSRF dan LFI', function () {
    expect(config('dompdf.options.enable_remote'))->toBeFalse();
    expect(config('dompdf.options.enable_javascript'))->toBeFalse();
});

test('rpd disimpan ke private storage dan pengguna dapat mengunduh miliknya sendiri', function () {
    $user = User::factory()->create([
        'role' => 'Mahasiswa',
        'cdmi_complete' => 1,
        'dmti_complete' => 1,
        'is_email_changed' => 1,
        'is_password_changed' => 1,
    ]);

    $file = UploadedFile::fake()->create('rekam_medis.pdf', 100, 'application/pdf');

    $response = $this->actingAs($user)->post(route('profile.create-rpd', $user->id), [
        'file_RPD' => $file,
    ]);

    $response->assertRedirect();
    $rpd = $user->RPD()->first();
    expect($rpd)->not->toBeNull();

    // Pastikan tidak ada di public disk
    Storage::disk('public')->assertMissing('RPD/' . $rpd->file_name);

    // Pastikan tersimpan di local private disk
    Storage::disk('local')->assertExists('rpd_private/' . $rpd->file_name);

    // Test download sendiri
    $downloadResponse = $this->actingAs($user)->get(route('profile.rpd.download', $rpd->id));
    $downloadResponse->assertOk();

    // Test mahasiswa lain mencoba mengunduh RPD ini -> 403 Forbidden
    $otherUser = User::factory()->create([
        'role' => 'Mahasiswa',
        'cdmi_complete' => 1,
        'dmti_complete' => 1,
        'is_email_changed' => 1,
        'is_password_changed' => 1,
    ]);

    $unauthorizedResponse = $this->actingAs($otherUser)->get(route('profile.rpd.download', $rpd->id));
    $unauthorizedResponse->assertForbidden();
});
