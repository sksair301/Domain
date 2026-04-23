<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    //
    protected $fillable = [
        'name',
        'company_name',
        'booking_date',
        'expiry_date',
        'sales_person_name',
        'branch_id',
        'status_id',
    ];

    protected static function booted()
    {
        static::saving(function ($domain) {
            // If the status is being manually updated, don't overwrite it
            if ($domain->isDirty('status_id')) {
                return;
            }

            if ($domain->expiry_date) {
                $expiry = \Carbon\Carbon::parse($domain->expiry_date)->startOfDay();
                $now = \Carbon\Carbon::now()->startOfDay();
                $diff = $now->diffInDays($expiry, false);

                if ($diff < 0) {
                    $statusName = 'Expired';
                } elseif ($diff <= 7) {
                    $statusName = 'Renewal in Progress';
                } elseif ($diff <= 15) {
                    $statusName = 'Pending Renewal';
                } elseif ($diff <= 30) {
                    $statusName = 'Expiring soon';
                } else {
                    $statusName = 'Active';
                }

                $status = \App\Models\Status::where('name', $statusName)->first();
                if ($status) {
                    $domain->status_id = $status->id;
                }
            }
        });
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
