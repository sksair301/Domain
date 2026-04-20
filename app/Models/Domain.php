<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    //
    protected $fillable = [
        'name',
        'booking_date',
        'expiry_date',
        'sales_person_name',
        'branch_id',
    ];
}
