<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GradeController extends Controller
{
    /**
     * Display a listing of the grades.
     */
    public function index()
    {
        $grades = Grade::orderBy('order', 'asc')->get();
        return response()->json(['status' => true, 'data' => $grades]);
    }

    /**
     * Display the specified grade.
     */
    public function show($id)
    {
        $grade = Grade::find($id);

        if (!$grade) {
            return response()->json(['status' => false, 'message' => 'Grade not found.'], 404);
        }

        return response()->json(['status' => true, 'data' => $grade]);
    }
}
