<?php

namespace App\Providers;

use App\services\RoleService;
use Illuminate\Support\ServiceProvider;
use App\services\interfaces\RoleServiceInterface;

class ServicesProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(RoleServiceInterface::class, RoleService::class);/*  */
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
       
    }
}
