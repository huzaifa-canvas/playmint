<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseBooking extends Model
{
    use HasFactory;

    protected $fillable = ['course_id','user_id','course_status', 'payment_status','progress','booking_date','meta'];
    protected $casts = ['meta' => 'array'];

    public function course(){ return $this->belongsTo(Course::class); }
    public function user(){ return $this->belongsTo(\App\Models\User::class); }
    public function payment(){ return $this->hasOne(Payments::class); }
}
