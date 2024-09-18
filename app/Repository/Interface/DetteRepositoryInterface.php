<?php

namespace App\Repository\Interface;

interface DetteRepositoryInterface
{

    public function all();

    public function state($state);
}
