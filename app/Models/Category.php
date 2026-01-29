<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'parent_id',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function posts(): BelongsToMany
    {
        $pivotTable = Schema::hasTable('category_post') ? 'category_post' : (Schema::hasTable('caategory_post') ? 'caategory_post' : 'category_post');
        return $this->belongsToMany(Post::class, $pivotTable);
    }

    public function categoryTranslations(): HasMany
    {
        return $this->hasMany(CategoryTranslation::class);
    }
}
