<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Query extends Model
{
    use HasUuids;

    protected $fillable = [
        'query_text',
        'normalized_query',
        'query_hash',
    ];

    public function responses(): HasMany
    {
        return $this->hasMany(AIResponse::class);
    }

    public function latestResponse(): HasOne
    {
        return $this->hasOne(AIResponse::class)->latestOfMany();
    }
}