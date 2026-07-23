<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Avatar;

class AvatarController extends Controller
{
    /**
     * Return all available avatars for the mobile app to display.
     */
    public function index()
    {
        $avatars = Avatar::select('id', 'image')->latest()->get()->map(function ($a) {
            return [
                'id'        => $a->id,
                'image_url' => $a->image_url,
            ];
        });

        return response()->json([
            'status'  => true,
            'avatars' => $avatars,
        ]);
    }
}
