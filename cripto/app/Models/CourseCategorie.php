<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseCategorie extends Model
{
    use HasFactory;

    protected $fillable = ['title','slug','description','image_path', 'status'];

    public function courses() {
        return $this->hasMany(Course::class, 'category_id');
    }
}
