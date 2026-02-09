<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $languageId = $request->header('Language-ID', app()->getLocale());
        $translation = $this->pageTranslations()->where('language_id', $languageId)->first();

        return [
            'id' => $this->id,
            'language_id' => $translation->language_id,
            'title' => $translation->title,
            'slug' => $translation->slug,
            'content_md' => $translation->content_md,
            'is_active' => $this->is_active,
        ];
    }
}
