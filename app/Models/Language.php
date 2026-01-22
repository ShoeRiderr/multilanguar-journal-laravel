<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Language extends Model
{
    /** @use HasFactory<\Database\Factories\LanguageFactory> */
    use HasFactory;

    public $fillable = [
        'code',
        'name',
        'native_name',
        'is_active',
        'is_default',
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function categoryTranslations(): HasMany
    {
        return $this->hasMany(CategoryTranslation::class);
    }

    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }
}
