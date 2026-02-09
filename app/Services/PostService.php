<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Media;
use App\PostStatus;
use Illuminate\Http\UploadedFile;

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

    /**
     * Filter posts by multiple criteria.
     *
     * @param array $filters
     * @param int $perPage
     * @param int|null $defaultLanguageId
     * @param PostStatus|null $status
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getFilteredPosts(array $filters, int $perPage = 10, int|null $defaultLanguageId = null, PostStatus|null $status = null)
    {
        $query = Post::with(['categories', 'postView'])
            ->when($status !== null, function ($q) use ($status) {
                $q->where('status', $status->value);
            });

        $languageIds = $filters['languages'] ?? [];
        if (!empty($languageIds)) {
            $query->whereIn('language_id', $languageIds);
        } elseif ($defaultLanguageId !== null) {
            $query->where('language_id', $defaultLanguageId);
        }

        $categoryIds = $filters['categories'] ?? [];
        if (!empty($categoryIds)) {
            $query->whereHas('categories', function ($q) use ($categoryIds) {
                $q->whereIn('categories.id', $categoryIds);
            });
        }

        $search = $filters['search'] ?? null;
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('content_md', 'like', '%' . $search . '%');
            });
        }

        $dateFrom = $filters['date_from'] ?? null;
        if (!empty($dateFrom)) {
            $query->whereDate('published_at', '>=', $dateFrom);
        }

        $dateTo = $filters['date_to'] ?? null;
        if (!empty($dateTo)) {
            $query->whereDate('published_at', '<=', $dateTo);
        }

        return $query
            ->orderBy('published_at', 'desc')
            ->paginate($perPage);
    }


    public function createPost(array $data): Post
    {
        // Extract main_photo if present
        $mainPhoto = $data['main_photo'] ?? null;
        $categories = $data['categories'] ?? [];
        unset($data['main_photo']);
        unset($data['categories']);
        $post = Post::create($data);
        if (!empty($categories)) {
            $post->categories()->sync($categories);
        }
        if ($mainPhoto) {
            $this->saveMainPhoto($post, $mainPhoto);
        }
        return $post;
    }


    public function updatePost(Post $post, array $data): bool
    {
        $mainPhoto = $data['main_photo'] ?? null;
        $categories = $data['categories'] ?? [];
        unset($data['main_photo']);
        unset($data['categories']);
        $updated = $post->update($data);
        if (!empty($categories)) {
            $post->categories()->sync($categories);
        }
        if ($mainPhoto) {
            $this->saveMainPhoto($post, $mainPhoto, true);
        }
        return $updated;
    }

    /**
     * Save or update the main photo for a post.
     */
    protected function saveMainPhoto(Post $post, UploadedFile $mainPhoto, bool $replace = false): void
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