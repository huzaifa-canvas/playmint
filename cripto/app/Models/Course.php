<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id','name','slug','author','price','description',
        'featured','status','payment_status','feature_image','rating'
    ];

    public function category(){ return $this->belongsTo(CourseCategorie::class); }
    public function sessions(){ return $this->hasMany(CourseSession::class); }
    public function ratings(){ return $this->hasMany(CourseRating::class); }
    public function bookings(){ return $this->hasMany(CourseBooking::class); }
}
