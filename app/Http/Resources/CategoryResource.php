<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $languageId = $request->header('Language-ID', 'en');
        $translation = $this->categoryTranslations()->where('language_id', $languageId)->first();

        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'name' => $translation?->name ?? null,
            'slug' => $translation?->slug ?? null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
