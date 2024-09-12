<?php

namespace App\Repository\Interface;

use Illuminate\Support\Arr;

interface ArticleRepositoryInterface
{
    public function all();
    public function create(array $data);
    public function find($id);
    public function delete($id);
    public function update($id, array $data);
    public function findByLibelle($libelle);
    public function findByEtat($etat);
}
