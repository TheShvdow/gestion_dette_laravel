<?php

namespace App\Providers;

use App\Services\CloudinaryFileStorageService;
use App\Services\Interfaces\FileStorageServiceInterface;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use App\Services\DetteService;
use App\Services\Interfaces\DetteServiceInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(FileStorageServiceInterface::class, CloudinaryFileStorageService::class);
        $this->app->bind(DetteServiceInterface::class, DetteService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
       


        Passport::tokensExpireIn(now()->addMinutes(60));  // Access token expire dans 60 minutes
        Passport::refreshTokensExpireIn(now()->addDays(30));  // Refresh token expire dans 30 jours
    }
}
