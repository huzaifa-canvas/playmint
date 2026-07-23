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
    public function index()
    {
        $subjects = Subject::all();
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
