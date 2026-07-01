<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'domain_id',
        'amount',
        'payment_date',
        'payment_status_id',
    ];

    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }

    public function status()
    {
        return $this->belongsTo(PaymentStatus::class, 'payment_status_id');
    }

    protected static function booted()
    {
        static::created(function ($payment) {
            try {
                $payment->load('domain');
                $adminEmails = User::getAdminEmails();
                $managerEmails = User::getManagerEmailsByBranch($payment->domain->branch_id ?? null);
                $employeeEmails = User::getEmployeeEmailById($payment->domain->sales_person_id ?? null);

                $emails = array_unique(array_merge($adminEmails, $managerEmails, $employeeEmails));

                if (!empty($emails)) {
                    \Illuminate\Support\Facades\Mail::to($emails)->send(new \App\Mail\PaymentAddedMail($payment));
                    \Illuminate\Support\Facades\Log::info('PaymentAddedMail successfully sent to: ' . implode(', ', $emails));
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send PaymentAddedMail: ' . $e->getMessage());
            }
        });
    }
}
