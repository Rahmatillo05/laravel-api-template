<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizationDetail extends Model
{
    protected $table = 'organization_details';

    protected $fillable = [
        'id',
        'organization_id',
        'description',
        'address',
        'phone',
        'email',
        'website',
        'facebook',
        'twitter',
        'linkedin',
        'instagram',
        'youtube',
        'pinterest',
        'created_at',
        'updated_at'
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

}
