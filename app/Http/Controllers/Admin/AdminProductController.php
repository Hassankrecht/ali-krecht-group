<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminProductController extends Controller
{
    /* ============================================================
        INDEX
    ============================================================ */
    public function index()
{
    $products = Product::latest()->paginate(12);
    $categories = Category::orderBy('name')->get();

    return view('admins.products.index', compact('products', 'categories'));
}

    /* ============================================================
        CREATE
    ============================================================ */
    public function create()
    {
        $categories = Category::all();
        return view('admins.products.create', compact('categories'));
    }

    /* ============================================================
        STORE
    ============================================================ */
   public function store(Request $request)
{
    $validated = $request->validate([
        'category_id' => 'required|exists:categories,id',
        'title'       => 'required|string|max:255',
        'description' => 'nullable|string',
        'price'       => 'required|numeric',
        'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        'gallery.*'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
    ]);

    // Create product without image first
    $product = Product::create([
        'category_id' => $request->category_id,
        'title'       => $request->title,
        'description' => $request->description,
        'price'       => $request->price,
        'image'       => null, // initially empty
    ]);

    /* -------------------- SAVE MAIN IMAGE -------------------- */
    if ($request->hasFile('image')) {
        $filename = time() . '_' . uniqid() . "." . $request->image->extension();
        $request->image->storeAs('products', $filename, 'public');

        $product->image = "products/$filename";
        $product->save();
    }

    /* -------------------- SAVE GALLERY -------------------- */
    if ($request->hasFile('gallery')) {
        foreach ($request->gallery as $file) {
            $gname = time() . '_' . uniqid() . "." . $file->extension();
            $file->storeAs('product_gallery', $gname, 'public');

            ProductImage::create([
                'product_id' => $product->id,
                'image'      => "product_gallery/$gname",
            ]);
        }
    }

    return redirect()->route('admin.products.index')
                     ->with('success', 'Product created successfully!');
}


    /* ============================================================
        EDIT
    ============================================================ */
    public function edit(Product $product)
    {
        $product->load('images');
        $categories = Category::all();
        return view('admins.products.edit', compact('product', 'categories'));
    }

    /* ============================================================
        UPDATE
    ============================================================ */
    public function update(Request $request, Product $product)
{
    // Validate WITHOUT touching image directly
    $validated = $request->validate([
        'category_id' => 'required|exists:categories,id',
        'title'       => 'required|max:255',
        'description' => 'nullable|string',
        'price'       => 'required|numeric',
        'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
    ]);

    // Update normal fields manually (to avoid overwriting image)
    $product->category_id = $validated['category_id'];
    $product->title       = $validated['title'];
    $product->description = $validated['description'] ?? null;
    $product->price       = $validated['price'];
    $product->save();

    /* ---------------- UPDATE MAIN IMAGE ---------------- */
    if ($request->hasFile('image')) {

        // Delete old main image (if exists)
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        // Upload new image
        $filename = time() . '_' . uniqid() . "." . $request->image->extension();
        $request->image->storeAs('products', $filename, 'public');

        // Save new image path
        $product->image = "products/$filename";
        $product->save();
    }

    return redirect()
        ->route('admin.products.edit', $product->id)
        ->with('success', 'Product updated successfully!');
}

    /* ============================================================
        DELETE MAIN IMAGE
    ============================================================ */
    public function deleteMainImage(Product $product)
    {
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->update(['image' => null]);

        return back()->with('success', 'Main image deleted!');
    }

    /* ============================================================
        DELETE ONE GALLERY IMAGE
    ============================================================ */
    public function deleteGalleryImage(ProductImage $image)
    {
        if (Storage::disk('public')->exists($image->image)) {
            Storage::disk('public')->delete($image->image);
        }

        $image->delete();

        return back()->with('success', 'Gallery image deleted!');
    }

    /* ============================================================
        ADD IMAGES TO GALLERY
    ============================================================ */
    public function addImages(Request $request, Product $product)
    {
        $request->validate([
            'gallery.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        foreach ($request->gallery as $file) {

            $galleryName = time() . '_' . uniqid() . "." . $file->extension();
            $file->storeAs('product_gallery', $galleryName, 'public');

            ProductImage::create([
                'product_id' => $product->id,
                'image'      => "product_gallery/$galleryName",
            ]);
        }

        return back()->with('success', 'Images uploaded successfully!');
    }

    /* ============================================================
        DESTROY
    ============================================================ */
    public function destroy(Product $product)
    {
        // Delete main image
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        // Delete gallery
        foreach ($product->images as $img) {
            if (Storage::disk('public')->exists($img->image)) {
                Storage::disk('public')->delete($img->image);
            }
            $img->delete();
        }

        $product->delete();

        return redirect()->route('admin.products.index')
                         ->with('success', 'Product deleted successfully!');
    }
    public function filterByCategory($id)
{
    $categories = Category::all();
    $products = Product::where('category_id', $id)->paginate(12);
    $selectedCategory = Category::findOrFail($id);

    return view('admins.products.index', compact('products', 'categories', 'selectedCategory'));
}

}
