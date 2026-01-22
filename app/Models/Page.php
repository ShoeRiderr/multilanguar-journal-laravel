<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Page extends Model
{
    /** @use HasFactory<\Database\Factories\PageFactory> */
    use HasFactory;

    public $fillable = [
        'language_id',
        'key',
        'content_md',
        'is_active',
    ];

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
