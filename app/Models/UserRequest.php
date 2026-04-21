<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'query_id',
        'used_cached',
        'tokens_used',
    ];

    protected $casts = [
        'used_cached' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function aiQuery()
    {
        return $this->belongsTo(Query::class, 'query_id');
    }
}