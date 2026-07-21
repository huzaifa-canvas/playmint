<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseSession extends Model
{
    use HasFactory;

    protected $fillable = ['course_id','title','description','file_path','mime_type'];
    public function course(){ return $this->belongsTo(Course::class); }
    public function progress(){ return $this->hasOne(CourseProgress::class, 'session_id'); }
}
