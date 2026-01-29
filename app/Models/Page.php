<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    /** @use HasFactory<\Database\Factories\PageFactory> */
    use HasFactory;

    public $fillable = [
        'is_active',
    ];

    public function pageTranslations(): HasMany
    {
        return $this->hasMany(PageTranslation::class);
    }
}
