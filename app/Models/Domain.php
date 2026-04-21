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
            if ($domain->expiry_date) {
                $expiry = \Carbon\Carbon::parse($domain->expiry_date)->startOfDay();
                $now = \Carbon\Carbon::now()->startOfDay();

                if ($expiry->isBefore($now)) {
                    $statusName = 'Expire';
                } elseif ($now->diffInDays($expiry, false) <= 30 && $now->diffInDays($expiry, false) >= 0) {
                    $statusName = 'Expiring';
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
}
