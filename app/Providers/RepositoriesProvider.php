<?php

namespace App\Providers;

use App\Repository\RoleRepository;
use App\Repository\ArticleRepository;
use App\Repository\DetteRepository;
use Illuminate\Support\ServiceProvider;
use App\Repository\Interface\RoleRepositoryInterface;
use App\Repository\Interface\ArticleRepositoryInterface;
use App\Repository\Interface\DetteRepositoryInterface;
     

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
        $this->app->bind(
            DetteRepositoryInterface::class,DetteRepository::class,
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
