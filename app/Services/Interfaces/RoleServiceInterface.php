<?php

namespace App\Services\Interfaces;

interface RoleServiceInterface
{
    public function getRoleById($id);

    public function getRoleByLibelle($libelle);
}
