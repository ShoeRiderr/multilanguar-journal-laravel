<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Query extends Model
{
    use HasFactory;

    protected $fillable = [
        'query_text',
        'normalized_query',
        'query_hash',
    ];

    public function responses(): HasMany
    {
        return $this->hasMany(AiResponse::class);
    }

    public function latestResponse(): HasOne
    {
        return $this->hasOne(AiResponse::class)->latestOfMany();
    }
}