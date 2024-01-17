<?php

namespace App\Providers;

use App\Http\Interfaces\OrganizationInterface;
use App\Http\Interfaces\UserInterface;
use App\Http\Repositories\OrganizationRepository;
use App\Http\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;
use Modules\FileManager\app\Http\Interfaces\FileInterface;
use Modules\FileManager\app\Http\Repositories\FileRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(FileInterface::class, FileRepository::class);
        $this->app->bind(UserInterface::class, UserRepository::class);
        $this->app->bind(OrganizationInterface::class, OrganizationRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
