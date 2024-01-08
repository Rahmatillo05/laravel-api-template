<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * This is the model class for table "confirm_codes".
 */
class ConfirmCode extends Model
{
    protected $table = 'confirm_codes';

    protected $fillable = [
        'id',
        'code',
        'key',
        'user_id',
        'is_used',
        'expires_at',
        'created_at',
        'updated_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
