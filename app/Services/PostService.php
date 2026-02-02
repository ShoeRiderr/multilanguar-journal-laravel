<?php

namespace App\Services;

use App\Models\Post;

class PostService
{
    /**
     * Get posts by language, optionally return last $latest posts (not paginated).
     *
     * @param int $languageId
     * @param int $perPage
     * @param int|null $latest If set, returns last $latest posts (not paginated)
     * @return \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public function getPostsByLanguage($languageId, $perPage = 10, int|null $latest = null)
    {
        $query = Post::with(['categories', 'postView'])
            ->where('language_id', $languageId);

        if ($latest !== null) {
            return $query->orderByDesc('created_at')->take($latest)->get();
        }

        return $query->paginate($perPage);
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