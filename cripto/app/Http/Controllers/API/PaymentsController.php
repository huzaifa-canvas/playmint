<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;


class PaymentsController extends Controller
{
public function processPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount'        => 'required|numeric|min:1',
            'currency'      => 'required|string|size:3',
            'payment_method'=> 'required|string',
            'customer_id'   => 'nullable|string',
            'description'   => 'nullable|string|max:22', 
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

      
    }
}
