<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Schema;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory;

    public $fillable = [
        'language_id',
        'title',
        'slug',
        'content_md',
        'status',
        'published_at',
    ];

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function postView(): HasOne
    {
        return $this->hasOne(PostView::class);
    }

    public function categories(): BelongsToMany
    {
        $pivotTable = Schema::hasTable('category_post') ? 'category_post' : (Schema::hasTable('caategory_post') ? 'caategory_post' : 'category_post');
        return $this->belongsToMany(Category::class, $pivotTable);
    }
}
