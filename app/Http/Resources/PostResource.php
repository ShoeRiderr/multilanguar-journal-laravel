<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'language_id' => $this->language_id,
            'title' => $this->title,
            'slug' => $this->slug,
            'content_md' => $this->content_md,
            'status' => $this->status,
            'published_at' => $this->published_at,
            'main_photo' => $this->mainPhoto ? [
                'url' => $this->mainPhoto->file_path ? asset('storage/' . $this->mainPhoto->file_path) : null,
                'file_name' => $this->mainPhoto->file_name,
                'mime_type' => $this->mainPhoto->mime_type,
                'size' => $this->mainPhoto->size,
            ] : null,
            'categories' => \App\Http\Resources\CategoryResource::collection($this->whenLoaded('categories')),
            'post_view' => $this->whenLoaded('postView'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
