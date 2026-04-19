<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AIResponse extends Model
{
    use HasUuids;

    protected $fillable = [
        'query_id',
        'response_json',
        'schema_version',
        'model_used',
        'tokens_used',
        'prompt_version',
        'expires_at',
    ];

    protected $casts = [
        'response_json' => 'array',
        'expires_at' => 'datetime',
    ];

    public function aiQuery(): BelongsTo
    {
        return $this->belongsTo(Query::class, 'query_id');
    }
}
