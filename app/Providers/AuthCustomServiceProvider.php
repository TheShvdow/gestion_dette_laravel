<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;
use App\Services\AuthentificationServiceSanctum;
use App\Services\AuthentificationServicePassport;
use App\Services\Interfaces\AuthentificationServiceInterface;

class AuthCustomServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            //AuthentificationServiceInterface::class, AuthentificationServicePassport::class,
            AuthentificationServiceInterface::class,AuthentificationServiceSanctum::class
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
