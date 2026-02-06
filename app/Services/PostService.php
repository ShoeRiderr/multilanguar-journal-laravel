<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Media;
use App\PostStatus;

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
    public function getPostsByLanguage(int $languageId, int $perPage = 10, int|null $latest = null, PostStatus|null $status = null)
    {
        $query = Post::with(['categories', 'postView'])
            ->where('language_id', $languageId)
            ->when($status !== null, function ($q) use ($status) {
                $q->where('status', $status->value);
            });

        if ($latest !== null) {
            return $query->orderBy('published_at', 'desc')->take($latest)->get();
        }

        return $query->paginate($perPage);
    }


    public function createPost(array $data): Post
    {
        // Extract main_photo if present
        $mainPhoto = $data['main_photo'] ?? null;
        unset($data['main_photo']);
        $post = Post::create($data);
        if ($mainPhoto) {
            $this->saveMainPhoto($post, $mainPhoto);
        }
        return $post;
    }


    public function updatePost(Post $post, array $data): bool
    {
        $mainPhoto = $data['main_photo'] ?? null;
        unset($data['main_photo']);
        $updated = $post->update($data);
        if ($mainPhoto) {
            $this->saveMainPhoto($post, $mainPhoto, true);
        }
        return $updated;
    }

    /**
     * Save or update the main photo for a post.
     */
    protected function saveMainPhoto(Post $post, $mainPhoto, bool $replace = false): void
    {
        if ($replace && $post->mainPhoto) {
            $post->mainPhoto->delete();
        }
        $path = $mainPhoto->store('media', 'public');
        $media = new Media([
            'file_path' => $path,
            'file_name' => $mainPhoto->getClientOriginalName(),
            'mime_type' => $mainPhoto->getClientMimeType(),
            'size' => $mainPhoto->getSize(),
        ]);
        $post->mainPhoto()->save($media);
    }

    public function deletePost(Post $post): ?bool
    {
        return $post->delete();
    }
}