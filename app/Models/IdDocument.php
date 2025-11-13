<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IdDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'file_path',
        'file_public_id',
        'status',
    ];

    /**
     * The user that owns the ID document.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

