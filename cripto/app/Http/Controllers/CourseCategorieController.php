<?php
namespace App\Http\Controllers;

use App\Models\CourseCategorie;
use Illuminate\Http\Request;

class CourseCategorieController extends Controller {

    public function index()
    {
        $categories = CourseCategorie::latest()->paginate(15);
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $r)
    {
        $r->validate([
            'title'=>'required|string|max:191',
            'slug'=>'required|unique:course_categories,slug'
        ]);

        $path = null;

        if ($r->hasFile('image')) $path = $r->file('image')->store('categories','public');

        CourseCategorie::create([
            'title'=>$r->title,
            'slug'=>$r->slug,
            'description'=>$r->description,
            'image_path'=>$path
        ]);

        return redirect()->route('categories.index')->with('success','Category created');
    }

    public function edit(CourseCategorie $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $r, CourseCategorie $category)
    {
        $r->validate([
            'title'=>'required',
            'slug'=>'required|unique:course_categories,slug,'.$category->id
        ]);

        if ($r->hasFile('image')) $category->image_path = $r->file('image')->store('categories','public');

        $category->update($r->only('title','slug','description') + ['image_path'=>$category->image_path]);

        return redirect()->route('categories.index')->with('success','Updated');
    }

    public function destroy(CourseCategorie $category)
    {
        $category->delete();
        return back()->with('success','Deleted');
    }
}
