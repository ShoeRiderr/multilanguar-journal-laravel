<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostView;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PostViewController extends Controller
{
    public function view(Request $request, $locale, Post $post)
    {
        $now = now();
        $postView = $post->postView;
        if (!$postView) {
            $postView = PostView::create([
                'post_id' => $post->id,
                'view_count' => 1,
                'last_viewed_at' => $now,
            ]);
        } else {
            $lastViewed = $postView->last_viewed_at ? Carbon::parse($postView->last_viewed_at) : null;
            if (!$lastViewed || $lastViewed->diffInMinutes($now) >= 5) {
                $postView->increment('view_count');
                $postView->last_viewed_at = $now;
                $postView->save();
            }
        }
        // You can return a response or redirect as needed
        return response()->json(['success' => true, 'view_count' => $postView->view_count]);
    }
}
