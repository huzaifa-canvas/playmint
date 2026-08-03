<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Milestone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MilestoneController extends Controller
{
    public function index()
    {
        $milestones = Milestone::orderBy('start_range', 'asc')->paginate(10);
        return view('content.admin.milestones.index', compact('milestones'));
    }

    public function create()
    {
        return view('content.admin.milestones.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_range' => 'required|integer|min:1',
            'end_range' => 'required|integer|gt:start_range',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $maxEnd = Milestone::max('end_range') ?? 0;
        if ($request->start_range <= $maxEnd) {
            return back()->withErrors(['start_range' => 'Start range must be greater than the previous end range (' . $maxEnd . ').'])->withInput();
        }

        $data = $request->all();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('milestones', 'public');
            $data['image'] = 'storage/' . $path;
        }

        Milestone::create($data);

        return redirect()->route('admin.milestones.index')->with('success', 'Milestone badge created successfully.');
    }

    public function edit(Milestone $milestone)
    {
        return view('content.admin.milestones.edit', compact('milestone'));
    }

    public function update(Request $request, Milestone $milestone)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_range' => 'required|integer|min:1',
            'end_range' => 'required|integer|gt:start_range',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $maxEnd = Milestone::where('id', '!=', $milestone->id)->max('end_range') ?? 0;
        
        // This is a simplified check. It assumes milestones are always added sequentially.
        // If they edit the last milestone, we ensure its start_range is greater than the 2nd to last milestone's end_range.
        // If they edit an earlier milestone, it might still overlap with the next one if we aren't careful, 
        // but this satisfies the basic condition requested.
        
        // A more robust overlap check for updating any milestone:
        $overlap = Milestone::where('id', '!=', $milestone->id)
            ->where(function($query) use ($request) {
                $query->whereBetween('start_range', [$request->start_range, $request->end_range])
                      ->orWhereBetween('end_range', [$request->start_range, $request->end_range])
                      ->orWhere(function($q) use ($request) {
                          $q->where('start_range', '<=', $request->start_range)
                            ->where('end_range', '>=', $request->end_range);
                      });
            })->exists();

        if ($overlap) {
             return back()->withErrors(['start_range' => 'This range overlaps with an existing milestone.'])->withInput();
        }

        $data = $request->all();

        if ($request->hasFile('image')) {
            if ($milestone->image) {
                $oldPath = str_replace(['public/', 'storage/'], '', $milestone->image);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            $path = $request->file('image')->store('milestones', 'public');
            $data['image'] = 'storage/' . $path;
        }

        $milestone->update($data);

        return redirect()->route('admin.milestones.index')->with('success', 'Milestone badge updated successfully.');
    }

    public function destroy(Milestone $milestone)
    {
        if ($milestone->image) {
            $oldPath = str_replace(['public/', 'storage/'], '', $milestone->image);
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }
        $milestone->delete();

        return redirect()->route('admin.milestones.index')->with('success', 'Milestone badge deleted successfully.');
    }
}
