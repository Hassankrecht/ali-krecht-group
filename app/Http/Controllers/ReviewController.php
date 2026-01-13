<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Review;
use App\Services\FileUploadService;
use App\Services\ReviewService;
use App\Services\NotificationService;

class ReviewController extends Controller
{
    public function __construct(
        private FileUploadService $files,
        private ReviewService $reviews,
        private NotificationService $notifications
    ) {
    }

    /**
     * Store a new visitor review (kept pending until approved).
     */
    public function store(StoreReviewRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $path = $this->files->storePublic($request->file('photo'), 'reviews');
            if ($path) {
                $data['photo'] = $path;
            }
        }

        $data['is_approved'] = false;

        // Use ReviewService to store review
        $review = $this->reviews->store($data);

        // Notify admin of new pending review
        $this->notifications->notifyAllAdmins(
            new \App\Notifications\ReviewSubmittedNotification($review)
        );

        return back()->with('review_success', __('messages.home.testimonials_submitted') ?? 'Thank you! Your review was submitted.');
    }
}
