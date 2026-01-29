<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CategoryService
{
    public function getCategories(): LengthAwarePaginator
    {
        return Category::paginate(10);
    }

    public function createCategory(array $data): Category
    {
        // Extract translations and parent_id
        $translations = $data['translations'] ?? [];
        $parentId = $data['parent_id'] ?? null;
        $category = Category::create(['parent_id' => $parentId]);
        foreach ($translations as $translation) {
            $category->categoryTranslations()->create($translation);
        }
        return $category;
    }

    public function updateCategory(Category $category, array $data): bool
    {
        $translations = $data['translations'] ?? [];
        $parentId = $data['parent_id'] ?? null;
        $category->update(['parent_id' => $parentId]);
        foreach ($translations as $langId => $translation) {
            $category->categoryTranslations()->updateOrCreate(
                ['language_id' => $langId],
                $translation
            );
        }
        return true;
    }

    public function deleteCategory(Category $category): ?bool
    {
        // Delete related category_post or caategory_post records
        $pivotTable = Schema::hasTable('category_post') ? 'category_post' : (Schema::hasTable('caategory_post') ? 'caategory_post' : null);
        if ($pivotTable) {
            DB::table($pivotTable)->where('category_id', $category->id)->delete();
        }
        // Delete translations
        $category->categoryTranslations()->delete();
        return $category->delete();
    }
}