<?php

namespace App\Repository\Interface;

interface DetteRepositoryInterface
{

    public function all();
    public function state($state);
    public function create(array $data);
    public function find(int $id);
}
