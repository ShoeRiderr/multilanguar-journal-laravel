<?php

namespace App\Services;

use App\Models\Page;

class PageService
{
    public function getPagesByLanguage($languageId): \Illuminate\Pagination\LengthAwarePaginator
    {
        return Page::where('language_id', $languageId)->paginate(10);
    }

    public function createPage(array $data): Page
    {
        return Page::create($data);
    }

    public function updatePage(Page $page, array $data): bool
    {
        return $page->update($data);
    }

    public function deletePage(Page $page): ?bool
    {
        return $page->delete();
    }
}