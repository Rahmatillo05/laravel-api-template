<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * This is the model class for table "organizations".
 */
class Organization extends Model
{
    protected $table = 'organizations';

    protected $fillable = [
        "id",
        "status",
        "created_at",
        "updated_at",
        "name"
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id', 'organization_id');
    }
}
