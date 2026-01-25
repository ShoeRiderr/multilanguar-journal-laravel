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
            'content_md' => Str::markdown($this->content_md, [
                'html_input' => 'strip',
                'allow_unsafe_links' => false,
            ]),
            'status' => $this->status,
            'published_at' => $this->published_at,
        ];
    }
}
