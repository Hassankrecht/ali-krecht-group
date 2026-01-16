<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminReviewController extends Controller
{
    public function index(Request $request)
    {
        // Default to "all" so records stay visible after approval
        $status = $request->query('status', 'all');
        $q      = $request->query('q');

        $baseQuery = Review::query();

        $pendingCount  = (clone $baseQuery)->where('is_approved', false)->count();
        $approvedCount = (clone $baseQuery)->where('is_approved', true)->count();
        $totalCount    = (clone $baseQuery)->count();

        $reviews = Review::query()
            ->when($status === 'approved', fn($q) => $q->where('is_approved', true))
            ->when($status === 'pending', fn($q) => $q->where('is_approved', false))
            ->when($q, function ($qq) use ($q) {
                $qq->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('profession', 'like', "%{$q}%")
                        ->orWhere('review', 'like', "%{$q}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->appends($request->query());

        return view('admins.reviews.index', [
            'reviews' => $reviews,
            'status' => $status,
            'q' => $q,
            'counts' => [
                'pending' => $pendingCount,
                'approved' => $approvedCount,
                'total' => $totalCount,
            ],
        ]);
    }

    public function approve(Review $review)
    {
        $review->update(['is_approved' => true]);

        return back()->with('success', 'Review approved and will appear on the site.');
    }

    public function reject(Review $review)
    {
        $review->update(['is_approved' => false]);

        return back()->with('success', 'Review marked as hidden.');
    }

    public function destroy(Review $review)
    {
        $this->deletePhoto($review->photo);

        $review->delete();

        return back()->with('success', 'Review deleted.');
    }

    /**
     * حذف صورة الريفيو أينما كانت محفوظة (public/assets أو storage/public).
     */
    private function deletePhoto(?string $path): void
    {
        if (!$path) {
            return;
        }

        // مسار يبدأ بـ public/assets/...
        if (str_starts_with($path, 'public/')) {
            $full = public_path(substr($path, strlen('public/')));
            if (file_exists($full)) {
                @unlink($full);
            }
            return;
        }

        // مسار يبدأ بـ assets/...
        if (str_starts_with($path, 'assets/')) {
            $full = public_path($path);
            if (file_exists($full)) {
                @unlink($full);
            }
            return;
        }

        // مسارات قديمة داخل تخزين public
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
