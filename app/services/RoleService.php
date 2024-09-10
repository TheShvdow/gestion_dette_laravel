<?php

namespace App\services;

use App\services\interfaces\RoleServiceInterface;
use App\Repository\Interface\RoleRepositoryInterface;


class RoleService implements RoleServiceInterface
{
    private $roleRepository;
    
    public function __construct(RoleRepositoryInterface $roleRepository){
        $this->roleRepository = $roleRepository;
    }
    public function getRoleById($id)
    {
        return $this->roleRepository->getRoleLibelleById($id);
    }

    public function getRoleByLibelle($libelle)
    {
       return $this->roleRepository->getRoleIdByLibelle($libelle);
    }
    
}

