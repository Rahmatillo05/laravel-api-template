<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

/**
 * This is the model class for table "users".
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const ROLE_ADMIN = 'admin';

    const ROLE_USER = 'user';

    const ROLE_ORGANIZATION = 'organization';

    const STATUS_ACTIVE = 1;

    const STATUS_INACTIVE = 0;

    const STATUS_CREATING_PASSWORD = 3;

    const STATUS_WAIT_VERIFICATION = 2;

    protected $table = 'users';

    protected $fillable = [
        'updated_at',
        'email_verified_at',
        'created_at',
        'id',
        'remember_token',
        'password',
        'username',
        'phone',
        'email',
        'status',
        'organization_id',
    ];
    protected $appends = [
        'role'
    ];

    public function getRoleAttribute()
    {
        return $this->roles()->first()?->role ?? null;
    }

    public function roles(): HasMany
    {
        return $this->hasMany(Role::class, 'user_id', 'id');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }

    public function confirm_codes(): HasMany
    {
        return $this->hasMany(ConfirmCode::class, 'user_id', 'id');
    }

    public function getNameAttribute(): string
    {
        return $this->phone;
    }
}
