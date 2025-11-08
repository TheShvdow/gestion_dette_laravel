<?php

namespace App\Services\Interfaces;

interface AuthentificationServiceInterface
{

    public function authentificate(array $credentials);

    public function logout($user);
}