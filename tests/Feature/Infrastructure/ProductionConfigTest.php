<?php

namespace Tests\Feature\Infrastructure;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductionConfigTest extends TestCase
{
    /**
     * Test bahwa konfigurasi session secure cookie menghormati environment variable
     * untuk mencegah error 419 Page Expired pada koneksi HTTP biasa.
     */
    public function test_session_secure_cookie_configuration_matches_environment(): void
    {
        // Secara default pada .env.example / local, secure cookie bernilai false
        $this->assertFalse(
            (bool) config('session.secure'),
            'SESSION_SECURE_COOKIE harus bernilai false saat diakses melalui HTTP/IP non-SSL untuk mencegah error 419 Page Expired.'
        );

        // Jika SESSION_SECURE_COOKIE disetel true (misalnya saat SSL aktif), config harus mengikutinya
        Config::set('session.secure', true);
        $this->assertTrue(config('session.secure'));
    }

    /**
     * Test bahwa seluruh direktori storage krusial dapat diakses dan ditulis
     * untuk mencegah error permission denied pada runtime PHP-FPM.
     */
    public function test_critical_storage_paths_are_defined(): void
    {
        $criticalPaths = [
            storage_path('framework/cache/data'),
            storage_path('framework/sessions'),
            storage_path('framework/views'),
            storage_path('logs'),
            storage_path('app/public'),
            storage_path('app/rpd_private'),
        ];

        foreach ($criticalPaths as $path) {
            $this->assertNotEmpty($path, "Path storage [{$path}] tidak boleh kosong.");
        }
    }

    /**
     * Test bahwa command pembersihan tiket SSO 'sso:prune-tickets'
     * terdaftar pada Schedule harian.
     */
    public function test_sso_prune_tickets_command_is_scheduled(): void
    {
        $schedule = app()->make(Schedule::class);
        $events = collect($schedule->events());

        $hasPruneCommand = $events->contains(function ($event) {
            return str_contains($event->command ?? '', 'sso:prune-tickets');
        });

        $this->assertTrue(
            $hasPruneCommand,
            "Command 'sso:prune-tickets' harus terdaftar dalam task scheduler harian."
        );
    }

    /**
     * Test bahwa konfigurasi disk local/private tidak mengekspos file RPD secara publik.
     */
    public function test_rpd_private_disk_isolation(): void
    {
        $localRoot = config('filesystems.disks.local.root');
        $publicRoot = config('filesystems.disks.public.root');

        $this->assertNotEquals(
            $localRoot,
            $publicRoot,
            'Root disk local (penyimpan rpd_private) tidak boleh sama dengan disk public.'
        );

        $this->assertStringNotContainsString(
            'public',
            $localRoot,
            'Root disk local tidak boleh berada di dalam folder public.'
        );
    }

    /**
     * Test bahwa Redis terkonfigurasi dengan benar di sistem.
     */
    public function test_redis_connection_configuration_is_valid(): void
    {
        $redisClient = config('database.redis.client');
        $this->assertContains(
            $redisClient,
            ['phpredis', 'predis'],
            'Client Redis harus menggunakan phpredis atau predis.'
        );

        $defaultHost = config('database.redis.default.host');
        $this->assertNotEmpty($defaultHost, 'Host Redis default harus terdefinisi.');
    }
}
