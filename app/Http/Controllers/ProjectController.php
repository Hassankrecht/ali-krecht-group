<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * عرض صفحة مشروع واحد
     */
    public function show($id)
    {
        $project = Project::with('images')->findOrFail($id);
        return view('projects.show', compact('project'));
    }

    /**
     * عرض جميع المشاريع (أو أحدث 6 مشاريع)
     */
    public function index()
    {
        $projects = Project::with('images')
            ->orderBy('id', 'desc')
            ->take(6)
            ->get()
            ->map(function ($project) {
                $imagePath = $project->main_image ?? ($project->images->first()->image_path ?? null);

                if ($imagePath && file_exists(public_path('storage/' . $imagePath))) {
                    $mainImage = asset('storage/' . $imagePath);
                } elseif ($imagePath && file_exists(public_path('assets/img/' . $imagePath))) {
                    $mainImage = asset('assets/img/' . $imagePath);
                } elseif ($imagePath && file_exists(public_path('img/' . $imagePath))) {
                    $mainImage = asset('img/' . $imagePath);
                } else {
                    $mainImage = asset('assets/img/default.jpg');
                }

                $gallery = $project->images
                    ->pluck('image_path')
                    ->filter()
                    ->map(function ($img) {
                        return asset('storage/' . $img);
                    })
                    ->values();

                $project->setAttribute('main_image_url', $mainImage);
                $project->setAttribute('gallery_urls', $gallery);

                return $project;
            });

        $reviews = Review::orderBy('id', 'desc')->take(4)->get();

        return view('projects.index', compact('projects', 'reviews'));
    }

    /**
     * عرض صفحة إنشاء مراجعة
     */
    public function createReview()
    {
        return view('reviews.create');
    }

    /**
     * حفظ المراجعة الجديدة
     */
    public function storeReview(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'review' => 'required',
        ]);

        // إذا كان المستخدم مسجل الدخول، نربط المراجعة بحسابه
        if(Auth::check()){
            $validated['user_id'] = Auth::id();
        }

        Review::create($validated);

        return redirect()->back()->with('success','Review submitted successfully.');
    }
}
