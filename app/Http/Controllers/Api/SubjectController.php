<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubjectController extends Controller
{
    /**
     * Display a listing of the subjects.
     */
    public function index(Request $request)
    {
        if ($request->has('child_id')) {
            $child = \Illuminate\Support\Facades\Auth::user()->children()->find($request->child_id);
            if (!$child) {
                return response()->json(['status' => false, 'message' => 'Child not found or does not belong to you.'], 404);
            }
            $subjects = $child->subjects;
        } else {
            $subjects = Subject::all();
        }

        return response()->json(['status' => true, 'data' => $subjects]);
    }



    /**
     * Display the specified subject.
     */
    public function show($id)
    {
        $subject = Subject::find($id);

        if (!$subject) {
            return response()->json(['status' => false, 'message' => 'Subject not found.'], 404);
        }

        return response()->json(['status' => true, 'data' => $subject]);
    }
}
