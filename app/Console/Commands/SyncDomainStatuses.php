<?php

namespace App\Console\Commands;

use App\Models\Domain;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SyncDomainStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domains:sync-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync statuses for all existing domains based on their expiry date.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $today = Carbon::today();

        $activeStatus   = Status::where('name', 'Active')->first();
        $expiringStatus = Status::where('name', 'Expiring')->first();
        $expiredStatus  = Status::where('name', 'Expire')->first();

        if (!$activeStatus || !$expiringStatus || !$expiredStatus) {
            $this->error('One or more statuses (Active, Expiring, Expire) not found in the database.');
            return;
        }

        // 1. Mark Expired: expiry_date < today
        $expired = Domain::where('expiry_date', '<', $today->toDateString())
            ->update(['status_id' => $expiredStatus->id]);

        // 2. Mark Expiring: today <= expiry_date <= today + 30 days
        $expiring = Domain::whereBetween('expiry_date', [
            $today->toDateString(),
            $today->copy()->addDays(30)->toDateString(),
        ])->update(['status_id' => $expiringStatus->id]);

        // 3. Mark Active: expiry_date > today + 30 days
        $active = Domain::where('expiry_date', '>', $today->copy()->addDays(30)->toDateString())
            ->update(['status_id' => $activeStatus->id]);

        $total = $expired + $expiring + $active;

        $this->info("Domain status sync complete:");
        $this->line("  • Expired   : {$expired}");
        $this->line("  • Expiring  : {$expiring}");
        $this->line("  • Active    : {$active}");
        $this->line("  • Total     : {$total}");
    }
}
