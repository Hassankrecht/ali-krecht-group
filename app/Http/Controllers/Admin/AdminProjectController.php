<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminProjectController extends Controller
{
    /**
     * 🟢 عرض كل المشاريع
     */
    public function index()
    {
        $projects = Project::with('images')->latest()->paginate(10);
        return view('admins.projects.index', compact('projects'));
    }

    /**
     * 🟡 صفحة إنشاء مشروع جديد
     */
    public function create()
    {
        return view('admins.projects.create');
    }

    /**
     * 🟢 حفظ مشروع جديد
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'location'    => 'nullable|string|max:255',
            'date'        => 'nullable|date',
            'status'      => 'required|integer|in:1,2,3,4', // <== هنا أرقام فقط
            'main_image'  => 'nullable|image|mimes:jpg,png,jpeg|max:4096',
            'gallery.*'   => 'nullable|image|mimes:jpg,png,jpeg|max:4096',
        ]);

        // رفع الصورة الرئيسية
        if ($request->hasFile('main_image')) {
            $data['main_image'] = $request->file('main_image')->store('projects', 'public');
        }

        $project = Project::create($data);

        // رفع صور الجاليري
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $image) {
                $path = $image->store('project_images', 'public');
                ProjectImage::create([
                    'project_id' => $project->id,
                    'image_path' => $path,
                ]);
            }
        }

        return redirect()->route('admin.projects.index')->with('success', 'Project created successfully!');
    }

    /**
     * ✏️ صفحة التعديل
     */
    public function edit(Project $project)
    {
        $project->load('images');
        return view('admins.projects.edit', compact('project'));
    }


    /**
     * 🔄 تحديث مشروع
     */
    public function update(Request $request, Project $project)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'location'    => 'nullable|string|max:255',
            'date'        => 'nullable|date',

            // ❗ أهم نقطة — status لازم يكون رقم
            'status'      => 'required|integer|in:1,2,3,4',

            'main_image'  => 'nullable|image|mimes:jpg,png,jpeg|max:4096',
            'gallery.*'   => 'nullable|image|mimes:jpg,png,jpeg|max:4096',
        ]);

        // تحديث الصورة الرئيسية لو تم رفع واحدة جديدة
        if ($request->hasFile('main_image')) {
            if ($project->main_image && Storage::disk('public')->exists($project->main_image)) {
                Storage::disk('public')->delete($project->main_image);
            }
            $data['main_image'] = $request->file('main_image')->store('projects', 'public');
        }

        $project->update($data);

        // إضافة صور جديدة
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $img) {
                $path = $img->store('project_images', 'public');
                ProjectImage::create([
                    'project_id' => $project->id,
                    'image_path' => $path,
                ]);
            }
        }

        return redirect()
            ->route('admin.projects.index')
            ->with('success', 'Project updated successfully!');
    }


    /**
     * ❌ حذف صورة واحدة من الجاليري
     */
    public function deleteImage(ProjectImage $image)
    {
        if (Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }

        $image->delete();

        return back()->with('success', 'Gallery image deleted!');
    }


    /**
     * ❌ حذف الصورة الرئيسية فقط
     */
    public function deleteMainImage(Project $project)
    {
        if ($project->main_image && Storage::disk('public')->exists($project->main_image)) {
            Storage::disk('public')->delete($project->main_image);
        }

        $project->update(['main_image' => null]);

        return back()->with('success', 'Main image deleted!');
    }


    /**
     * 🗑️ حذف مشروع كامل
     */
    public function destroy(Project $project)
    {
        // حذف الصورة الرئيسية
        if ($project->main_image && Storage::disk('public')->exists($project->main_image)) {
            Storage::disk('public')->delete($project->main_image);
        }

        // حذف الجاليري
        foreach ($project->images as $img) {
            if (Storage::disk('public')->exists($img->image_path)) {
                Storage::disk('public')->delete($img->image_path);
            }
            $img->delete();
        }

        $project->delete();

        return redirect()->route('admin.projects.index')->with('success', 'Project deleted!');
    }

    
    public function addImages(Request $request, Project $project)
    {
        $request->validate([
            'gallery.*' => 'image|mimes:jpg,png,jpeg|max:4096'
        ]);

        if ($request->hasFile('gallery')) {

            foreach ($request->file('gallery') as $image) {
                $path = $image->store('project_images', 'public');

                ProjectImage::create([
                    'project_id' => $project->id,
                    'image_path' => $path,
                ]);
            }
        }

        return back()->with('success', '✨ New images uploaded successfully!');
    }
}
