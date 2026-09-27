<?php

namespace App\Console\Commands;

use App\Models\SsoTicket;
use Illuminate\Console\Command;

class PruneExpiredSsoTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sso:prune-tickets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune SSO tickets older than 24 hours from database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $cutoff = now()->subHours(24);
        $deleted = SsoTicket::where('created_at', '<', $cutoff)
            ->orWhere('used_at', '<', $cutoff)
            ->delete();

        $this->info("Selesai membersihkan tiket SSO: {$deleted} tiket kedaluwarsa dihapus.");

        return self::SUCCESS;
    }
}
