<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    use HasFactory;
    protected $table = 'payments';


    protected $fillable = [
        'user_id',
        'course_booking_id',
        'amount',
        'status',
        'charge_id',
        'currency',
    ];
}
