<?php

namespace App\Providers;

use App\Repository\RoleRepository;
use App\Repository\ArticleRepository;
use Illuminate\Support\ServiceProvider;
use App\Repository\Interface\RoleRepositoryInterface;
use App\Repository\Interface\ArticleRepositoryInterface;
     

class RepositoriesProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            RoleRepositoryInterface::class,RoleRepository::class,
        );
        $this->app->bind(
            ArticleRepositoryInterface::class,ArticleRepository::class,
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
