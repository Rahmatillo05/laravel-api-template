<?php

namespace Modules\FileManager\app\Providers;

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
