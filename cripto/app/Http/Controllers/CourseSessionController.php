<?php
namespace App\Http\Controllers;

use App\Models\CourseSession;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseSessionController extends Controller {

    public function store(Request $r, $courseId){
        $r->validate(['title'=>'required','start_at'=>'nullable|date','end_at'=>'nullable|date']);
        $course = Course::findOrFail($courseId);
        $course->sessions()->create($r->only('title','description','start_at','end_at'));
        return back()->with('success','Session added');
    }

    public function update(Request $r, CourseSession $session){
        $r->validate(['title'=>'required']);
        $session->update($r->only('title','description','start_at','end_at'));
        return back()->with('success','Updated');
    }

    public function destroy(CourseSession $session){ $session->delete(); return back()->with('success','Deleted'); }
}
