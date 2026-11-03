<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dataset extends Model
{
    use HasFactory;

    // user_id is deliberately not fillable: always create through $user->datasets()
    protected $fillable = [
        'name',
        'original_filename',
        'path',
        'size_bytes',
        'row_count',
        'column_count',
        'status',
        'health_score',
        'options',
        'profile',
    ];

    protected function casts(): array
    {
        return [
            'size_bytes'   => 'integer',
            'row_count'    => 'integer',
            'column_count' => 'integer',
            'health_score' => 'integer',
            'options'      => 'array',   // JSONB <-> PHP array
            'profile'      => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}