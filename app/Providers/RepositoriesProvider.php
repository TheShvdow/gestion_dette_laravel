<?php

namespace App\Providers;

use App\Repository\RoleRepository;
use Illuminate\Support\ServiceProvider;
use App\Repository\Interface\RoleRepositoryInterface;
     

class RepositoriesProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            RoleRepositoryInterface::class,RoleRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
