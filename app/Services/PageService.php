<?php

namespace App\Services;

use App\Models\Page;

class PageService
{
    public function getPagesByLanguage($languageId): \Illuminate\Pagination\LengthAwarePaginator
    {
        return Page::with('pageTranslations')->whereHas('pageTranslations', function ($query) use ($languageId) {
            $query->where('language_id', $languageId);
        })->paginate(10);

    }

    public function createPage(array $data): Page
    {
        $translations = $data['translations'] ?? [];
        unset($data['translations']);
        $page = Page::create([
            'is_active' => $data['is_active'] ?? true,
        ]);
        foreach ($translations as $translation) {
            $page->pageTranslations()->create($translation);
        }
        return $page;
    }

    public function updatePage(Page $page, array $data): bool
    {
        $translations = $data['translations'] ?? [];
        unset($data['translations']);
        // Remove old translations not present in update
        $langIds = array_column($translations, 'language_id');
        $page->update(
            [
                'is_active' => $data['is_active'] ?? $page->is_active,
            ]
        );
        $page->pageTranslations()->whereNotIn('language_id', $langIds)->delete();
        foreach ($translations as $translation) {
            $page->pageTranslations()->updateOrCreate(
                [
                    'language_id' => $translation['language_id'],
                ],
                $translation
            );
        }
        return true;
    }

    public function deletePage(Page $page): ?bool
    {
        $page->pageTranslations()->delete();
        return $page->delete();
    }
}