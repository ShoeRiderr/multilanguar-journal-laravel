<?php

namespace App\Services;

use App\Models\Language;
use Illuminate\Pagination\LengthAwarePaginator;

class LanguageService
{
    public function getLanguages(): LengthAwarePaginator
    {
        return Language::paginate(10);
    }

    public function createLanguage(array $data): Language
    {
        return Language::create($data);
    }

    public function updateLanguage(Language $language, array $data): bool
    {
        return $language->update($data);
    }

    public function deleteLanguage(Language $language): ?bool
    {
        return $language->delete();
    }
}