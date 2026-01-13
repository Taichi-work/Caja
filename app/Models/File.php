<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes; // 追加

class File extends Model
{
    use HasFactory, SoftDeletes; // SoftDeletes を追加

    protected $fillable = [
        'user_id',
        'original_name',
        'storage_path',
        'size',
        'mime_type',
        'share_token',
        'shared_expires_at',
    ];

    protected $casts = [
        'shared_expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}