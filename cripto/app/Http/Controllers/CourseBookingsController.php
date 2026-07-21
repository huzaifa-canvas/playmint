<?php

namespace App\Http\Controllers;

use App\Models\CourseBooking;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseBookingController extends Controller {

    public function store(Request $r, $courseId)
    {
        $r->validate([
            'booking_date'=>'nullable|date'
        ]);

        $course = Course::findOrFail($courseId);
        $booking = CourseBooking::create([
            'course_id'=>$course->id,
            'user_id'=>Auth::id(),
            'payment_status'=>'pending',
            'booking_date'=>$r->booking_date
        ]);

        return back()->with('success','Booked');
    }

    public function index(){
        $bookings = CourseBooking::with('course','user')->latest()->paginate(15);
        return view('bookings.index', compact('bookings'));
    }
}


