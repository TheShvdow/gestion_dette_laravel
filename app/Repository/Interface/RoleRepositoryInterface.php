<?php

namespace App\Repository\Interface;

interface RoleRepositoryInterface
{
    public function getRoleLibelleById($id);   

    public function getRoleIdByLibelle($libelle);
}
