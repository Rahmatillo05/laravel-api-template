<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Passport::personalAccessTokensExpireIn(now()->addDay());

        Passport::tokensCan([
            User::ROLE_USER => 'User',
            User::ROLE_ADMIN => 'Admin',
            User::ROLE_ORGANIZATION => 'Organization',
        ]);

        Passport::setDefaultScope([
            User::ROLE_USER,
        ]);
    }
}
