<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class PageResource extends JsonResource
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
            'key' => $this->key,
            'content_md' => Str::markdown($this->content_md, [
                'html_input' => 'strip',
                'allow_unsafe_links' => false,
            ]),
            'is_active' => $this->is_active,
        ];
    }
}
