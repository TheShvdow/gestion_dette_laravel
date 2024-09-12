<?php

namespace App\Providers;

use App\Repository\ArticleRepository;
use App\Repository\Interface\ArticleRepositoryInterface;
use App\Services\RoleService;
use App\Services\ArticleService;
use Illuminate\Support\ServiceProvider;
use App\Services\Interfaces\RoleServiceInterface;
use App\Services\Interfaces\ArticleServiceInterface;

class ServicesProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(RoleServiceInterface::class, RoleService::class);
        $this->app->bind(
            ArticleServiceInterface::class,ArticleService::class
        );
        $this->app->bind(
            ArticleRepositoryInterface::class,ArticleRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
       
    }
}
