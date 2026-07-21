<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\CourseBooking;
use App\Models\CourseCategorie;
use App\Models\CourseProgress;
use App\Models\CourseRating;
use App\Models\CourseSession;
use App\Models\Payments;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;



class CourseController extends Controller
{
    // 1: Active Categories
    public function activeCategories()
    {
        $categories = CourseCategorie::where('status', true)->latest()->paginate(20);
        return response()->json([
            'status' => true,
            'categories' => $categories
        ]);
    }

    // 2: Active Courses
    // 2: Active Courses
    public function activeCourses(Request $request)
    {
        $query = Course::with('category')->where('status', true);

        // Filter by Category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by Price Range
        if ($request->has('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        if ($request->has('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        // Search Filter
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $courses = $query->latest()->paginate(20);

        return response()->json([
            'status' => true,
            'courses' => $courses
        ]);
    }

    // 2.1: Single Active Course
    public function activeCourse(Request $request, $id)
    {
        $course = Course::with(['category'])
        ->where('status', true)
        ->find($id);

        if (!$course) {
            return response()->json([
                'status' => false,
                'message' => 'Course not found or inactive.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'course' => $course
        ]);
    }

    // 3: Featured Courses
    public function featuredCourses()
    {
        $featuredCourses = Course::with('category')->where('status', true)
            ->where('featured', true)
            ->get();
        return response()->json([
            'status' => true,
            'featured_courses' => $featuredCourses
        ]);
    }

    // 4: Add Course Rate
    function addCourseRate(Request $request, $id) {

        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'status' => false], 422);
        }

        try {
            $course = Course::findOrFail($id);

            // Check agar user ne pehle rating di hai
            $rating = CourseRating::updateOrCreate(
                [
                    'course_id' => $course->id,
                    'user_id'   => auth()->id()
                ],
                [
                    'rating' => $request->rating,
                    'review' => $request->review
                ]
            );

            // Average rating calculate karke course table update karo
            $averageRating = CourseRating::where('course_id', $course->id)->avg('rating');
            $course->rating = round($averageRating, 2);
            $course->save();

            return response()->json([
                'success' => true,
                'message' => 'Rating saved successfully',
                'average_rating' => $course->rating
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => false, 'errors' => [$e->getMessage()]], 500);
        }
    }

     // 4: Course Rating
    public function getRatings($id)
    {
        $course = Course::with('ratings') // user ka naam load karne ke liye
            ->findOrFail($id);

        $ratings = $course->ratings()->with('user:id,first_name,last_name')->latest()->paginate(20);

        $averageRating = round($course->ratings()->avg('rating'), 2);

        return response()->json([
            'status' => true,
            'average_rating' => $averageRating,
            'total_ratings' => $ratings->count(),
            'ratings' => $ratings
        ]);
    }

    public function bookCourse(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'stripe_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'status' => false], 422);
        }

        $course = Course::findOrFail($id);

        // Check if already enrolled
        $alreadyEnrolled = CourseBooking::where('course_id', $id)->where('user_id', auth()->id())->where('payment_status', '!=', 'cancelled')->exists();

        if ($alreadyEnrolled) {
            return response()->json([
                'status' => false,
                'message' => 'You are already enrolled in this course.'
            ], 400);
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $user = auth()->user();

            $charge = \Stripe\Charge::create([
                'amount' => (int) ($course->price * 100),
                'currency' => 'usd',
                'source' => $request->stripe_token,
                'description' => $course->name,
                'metadata' => [
                    'user_id' => $user->id,
                    'course_id' => $id,
                    'user_name' => $user->first_name . ' ' . $user->last_name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? '',
                ]
            ]);

            if($charge->status === 'succeeded') {
                DB::beginTransaction();
                $booking = CourseBooking::create([
                    'course_id' => $id,
                    'user_id' => auth()->id(),
                    'payment_status' => 'paid',
                    'booking_date' => now(),
                    'course_status' => 'not_started', 
                    'progress' => 0
                ]);

                $paymentData = Payments::create([
                    'user_id' => auth()->id(),
                    'course_booking_id' => $booking->id,
                    'amount' => $charge->amount / 100,
                    'currency' => $charge->currency,
                    'charge_id' => $charge->id,
                    'status' => $charge->status,
                ]);
                

                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => 'Course booked successfully.',
                    'data' => [
                        'booking' => $booking,
                        'charge_id' => $charge->id,
                        'amount' => $charge->amount / 100,
                        'currency' => $charge->currency,
                        'status' => $charge->status,
                    ],
                ], 200);
               
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Payment failed.',
                ], 400);
            }

        } catch (ApiErrorException $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
                // 'stripe_code' => $e->getStripeCode(),
            ], 400);
        }
    }

    public function updatePaymentStatus(Request $request, $id)
    {
         $validator = Validator::make($request->all(), [
            'payment_status' => 'required|in:pending,paid,cancelled'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'status' => false], 422);
        }

        $booking = CourseBooking::findOrFail($id);
        $booking->payment_status = $request->payment_status;
        $booking->save();

        return response()->json([
            'status' => true,
            'message' => 'Payment status updated successfully.',
            'booking' => $booking
        ]);
    }

    public function myCourses(Request $request)
    {
        $userId = auth()->id();
        $query = CourseBooking::with(['course.sessions'])->where('user_id', $userId);

        if ($request->filled('course_status')) {
           $query->where('course_status', $request->course_status);
        }

        if ($request->filled('payment_status')) {
           $query->where('payment_status', $request->payment_status);
        }

        $courses = $query->latest()->paginate(20);

        return response()->json([
            'status' => true,
            'booking' => $courses
        ]);
    }

    public function myCourse(Request $request, $id)
    {
        $userId = auth()->id();

        // Join courses table to select course data
        $booking = CourseBooking::where('id', $id)
            ->where('user_id', $userId)
            ->with(['payment', 'course.category', 'course.sessions' => function($query) use ($userId) {
                $query->with(['progress' => function($q) use ($userId) {
                     $q->where('user_id', $userId);
                }]);
            }])
            ->first();

        if (!$booking) {
            return response()->json([
                'status' => false,
                'message' => 'Booking not found.'
            ], 404);
        }

        // Add completed flag to sessions
        if ($booking->course && $booking->course->sessions) {
            $booking->course->sessions->map(function ($session) {
                $session->completed = $session->progress ? true : false;
                return $session;
            });
        }

        return response()->json([
            'status' => true,
            'data' => $booking
        ]);
    }

    //  Mark a session as complete
    public function markSessionComplete(Request $request, $booking_id, $session_id)
    {

        $booking = CourseBooking::findOrFail($booking_id);
        $session = CourseSession::findOrFail($session_id);

        $userId = auth()->id();

        // Check if already completed

        $exists = CourseProgress::where('session_id', $session_id)->where('user_id', $userId)->exists();

        if ($exists) {
            return response()->json([
                'status' => false,
                'message' => 'This session is already marked as complete.'
            ], 409);
        }

        CourseProgress::create([
            'course_id' => $session->course->id,
            'session_id' => $session_id,
            'user_id' => $userId
        ]);

        $totalSessions = $session->course->sessions->count();
        $completedSessions = CourseProgress::where('course_id', $session->course->id)->where('user_id', $userId)->count();
        $progress = round(($completedSessions / $totalSessions) * 100);

        // $booking = CourseBooking::where('course_id', $session->course->id)->where('user_id', $userId)->where('payment_status', 'paid');

        $booking->progress = $progress;

        if ($completedSessions == $totalSessions) {
           $booking->course_status = 'completed';
        } else{
            $booking->course_status = 'in_progress';
        }
        $booking->save();

        return response()->json([
            'status' => true,
            'message' => 'Session marked as complete successfully.'
        ]);
    }

    public function getStripeKey()
    {
        return response()->json([
            'status' => true,
            'key' => config('services.stripe.key')
        ]);
    }

    public function courseCounts(Request $request)
    {
        $userId = auth()->id();

        if ($request->has('status')) {
            $count = CourseBooking::where('user_id', $userId)
                ->where('course_status', $request->status)
                ->count();

            return response()->json([
                'status' => true,
                'count' => $count
            ]);
        }

        $counts = CourseBooking::where('user_id', $userId)
            ->selectRaw('course_status, count(*) as count')
            ->groupBy('course_status')
            ->pluck('count', 'course_status');

        return response()->json([
            'status' => true,
            'completed' => $counts['completed'] ?? 0,
            'in_progress' => $counts['in_progress'] ?? 0
        ]);
    }
}
