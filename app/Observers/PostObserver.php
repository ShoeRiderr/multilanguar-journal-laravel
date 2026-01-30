<?php

namespace App\Observers;

use App\Models\Post;

class PostObserver
{
    public function deleting(Post $post)
    {
        // Detach all categories (removes from category_post table)
        $post->categories()->detach();
        // Delete post view (if exists)
        $post->postView()->delete();
    }
}
