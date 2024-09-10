<?php

namespace App\services\interfaces;

interface RoleServiceInterface
{
    public function getRoleById($id);

    public function getRoleByLibelle($libelle);
}
