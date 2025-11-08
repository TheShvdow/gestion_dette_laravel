<?php

namespace App\Repository;

use App\Models\Role;
use App\Repository\Interface\RoleRepositoryInterface;

class RoleRepository implements RoleRepositoryInterface{

    public function getRoleLibelleById($id){
        $role = Role::find($id);
        return $role->libelle ?? null;
    }   

    public function getRoleIdByLibelle($libelle){
        $role = Role::where('libelle', $libelle)->first();
        return $role->id ?? null;
    }

}
