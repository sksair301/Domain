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

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }
}
