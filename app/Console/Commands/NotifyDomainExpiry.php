<?php

namespace App\Console\Commands;

use App\Models\Domain;
use App\Models\Branch;
use App\Models\Status;
use App\Mail\DomainExpiryReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class NotifyDomainExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:notify-domain-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify managers and employees about domains expiring in 30, 15, and 7 days, and update domain statuses.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();
        
        // Fetch statuses
        $activeStatus = Status::where('name', 'Active')->first();
        $expiringStatus = Status::where('name', 'Expiring')->first();
        $expiredStatus = Status::where('name', 'Expire')->first();

        // 1. Mark Expired Domains
        Domain::where('expiry_date', '<', $today->toDateString())
            ->update(['status_id' => $expiredStatus->id]);

        $intervals = [30, 15, 7];

        foreach ($intervals as $days) {
            $expiryDate = $today->copy()->addDays($days)->toDateString();
            
            $domains = Domain::where('expiry_date', $expiryDate)->get();

            if ($domains->isEmpty()) {
                $this->info("No domains expiring in {$days} days.");
                continue;
            }

            foreach ($domains as $domain) {
                // Update status to Expiring
                $domain->update(['status_id' => $expiringStatus->id]);

                $branch = $domain->branch;
                if (!$branch) continue;

                $recipients = collect();
                $branch->managers->each(fn($m) => $recipients->push($m->email));
                $branch->employees->each(fn($e) => $recipients->push($e->email));

                $recipientList = $recipients->unique()->filter()->toArray();

                if (!empty($recipientList)) {
                    Mail::to($recipientList)->send(new DomainExpiryReminder($domain, $days));
                    $this->info("Sent {$days}-day expiry reminder and updated status for {$domain->name}");
                }
            }
        }

        // 2. Ensure everything else is Active (if not Expiring or Expired)
        // This is a simple cleanup for domains that were previously Expiring but are no longer in the 30/15/7 window 
        // OR domains that were renewed.
        // For simplicity, we just mark everything >= today and not in expiring window as Active.
        $expiringDates = collect($intervals)->map(fn($d) => $today->copy()->addDays($d)->toDateString())->toArray();
        
        Domain::where('expiry_date', '>=', $today->toDateString())
            ->whereNotIn('expiry_date', $expiringDates)
            ->update(['status_id' => $activeStatus->id]);

        $this->info('Expiry notifications and status updates completed.');
    }
}
