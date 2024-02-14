<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationFile extends Model
{
    protected $table = 'organization_files';

    protected $fillable = [
        'id',
        'organization_id',
        'file_id',
        'created_at',
        'updated_at',
        'slug'
    ];

}
