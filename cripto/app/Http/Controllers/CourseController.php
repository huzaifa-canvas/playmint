<?php
namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseCategorie;
use Illuminate\Http\Request;
use Str;

class CourseController extends Controller {

    public function index(){ $courses = Course::with('category')->latest()->paginate(15); return view('courses.index', compact('courses')); }

    public function create(){ $cats = CourseCategorie::all(); return view('courses.create', compact('cats')); }

    public function store(Request $r){
        $r->validate([
            'name'=>'required|string|max:191','category_id'=>'nullable|exists:course_categories,id',
            'price'=>'required|numeric','slug'=>'required|unique:courses,slug'
        ]);
        $img = null;
        if ($r->hasFile('feature_image')) $img = $r->file('feature_image')->store('courses','public');
        $course = Course::create([
            'category_id'=>$r->category_id,'name'=>$r->name,'slug'=>$r->slug,'author'=>$r->author,
            'price'=>$r->price,'description'=>$r->description,'featured'=>boolval($r->featured),
            'status'=>$r->status ?? 'draft','payment_status'=>$r->payment_status ?? 'free','feature_image'=>$img
        ]);
        return redirect()->route('courses.index')->with('success','Course created');
    }

    public function edit(Course $course){ $cats = CourseCategorie::all(); return view('courses.edit', compact('course','cats')); }

    public function update(Request $r, Course $course){
        $r->validate(['name'=>'required','slug'=>'required|unique:courses,slug,'.$course->id,'price'=>'required|numeric']);
        if ($r->hasFile('feature_image')) $course->feature_image = $r->file('feature_image')->store('courses','public');
        $course->update($r->only('category_id','name','slug','author','price','description','featured','status','payment_status') + ['feature_image'=>$course->feature_image]);
        return redirect()->route('courses.index')->with('success','Updated');
    }

    public function destroy(Course $course){ $course->delete(); return back()->with('success','Deleted'); }
}

