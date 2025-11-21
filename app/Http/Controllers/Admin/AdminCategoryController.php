<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class AdminCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('id','desc')->get();
        return view('admins.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        Category::create([
            'name' => $request->name
        ]);

        return back()->with('success', 'Category added successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $cat = Category::findOrFail($id);
        $cat->name = $request->name;
        $cat->save();

        return back()->with('success', 'Category updated.');
    }

    public function destroy($id)
    {
        Category::destroy($id);

        return back()->with('success', 'Category deleted.');
    }
}
