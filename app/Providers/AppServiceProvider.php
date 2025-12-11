<?php

namespace App\Providers;

use App\Services\CloudinaryFileStorageService;
use App\Services\Interfaces\FileStorageServiceInterface;
use Illuminate\Support\ServiceProvider;
use App\Services\DetteService;
use App\Services\Interfaces\DetteServiceInterface;
use Laravel\Passport\Passport;

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
        // Passport token expiration is configured in AuthServiceProvider

        // Charger les clés Passport depuis les variables d'environnement pour Laravel Cloud
        $this->setupPassportKeys();
    }

    /**
     * Configure Passport keys from environment variables
     */
    protected function setupPassportKeys(): void
    {
        $privateKey = config('passport.private_key');
        $publicKey = config('passport.public_key');

        // Si les clés sont dans l'environnement (encodées en base64)
        if ($privateKey && $publicKey) {
            // Décoder et sauvegarder temporairement les clés
            $privateKeyPath = storage_path('oauth-private.key');
            $publicKeyPath = storage_path('oauth-public.key');

            // Créer les fichiers de clés s'ils n'existent pas
            if (!file_exists($privateKeyPath) || !file_exists($publicKeyPath)) {
                file_put_contents($privateKeyPath, base64_decode($privateKey));
                file_put_contents($publicKeyPath, base64_decode($publicKey));

                // Définir les permissions appropriées
                chmod($privateKeyPath, 0600);
                chmod($publicKeyPath, 0600);
            }

            Passport::loadKeysFrom(storage_path());
        }
    }
}
