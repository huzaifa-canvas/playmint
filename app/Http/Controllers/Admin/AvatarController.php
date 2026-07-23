<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Avatar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AvatarController extends Controller
{
    /**
     * Show the avatar gallery page.
     */
    public function index()
    {
        $avatars = Avatar::latest()->get();
        return view('content.admin.avatars.index', compact('avatars'));
    }

    /**
     * Store newly uploaded avatar(s) — AJAX.
     */
    public function store(Request $request)
    {
        $request->validate([
            'images'   => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $uploaded = [];

        foreach ($request->file('images') as $file) {
            $path   = $file->store('avatars', 'public');
            $avatar = Avatar::create(['image' => $path]);

            $uploaded[] = [
                'id'        => $avatar->id,
                'image_url' => $avatar->image_url,
            ];
        }

        return response()->json([
            'success' => true,
            'avatars' => $uploaded,
        ]);
    }

    /**
     * Delete an avatar — AJAX.
     */
    public function destroy(Avatar $avatar)
    {
        // Delete file from storage
        if (Storage::disk('public')->exists($avatar->image)) {
            Storage::disk('public')->delete($avatar->image);
        }

        $avatar->delete();

        return response()->json(['success' => true]);
    }
}
