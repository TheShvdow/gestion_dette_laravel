<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class RoleServiceFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'roleService';
    }
}
