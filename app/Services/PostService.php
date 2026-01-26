<?php

namespace App\Services;

use App\Models\Post;

class PostService
{
    public function getPostsByLanguage($languageId): \Illuminate\Pagination\LengthAwarePaginator
    {
        return Post::where('language_id', $languageId)->paginate(10);
    }

    public function createPost(array $data): Post
    {
        return Post::create($data);
    }

    public function updatePost(Post $post, array $data): bool
    {
        return $post->update($data);
    }

    public function deletePost(Post $post): ?bool
    {
        return $post->delete();
    }
}