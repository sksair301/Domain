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
}
