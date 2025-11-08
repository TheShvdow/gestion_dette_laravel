<?php

namespace App\Services;

use App\Repository\Interface\ArticleRepositoryInterface;

class ArticleService implements ArticleRepositoryInterface
{
    protected $articleservices;

    public function __construct(ArticleRepositoryInterface $article) {
        $this->articleservices = $article;
    }

    public function all()
    {
        return $this->articleservices->all();
    }
    public function create(array $data)
    {
        return $this->articleservices->create($data);
    }
    public function find($id)
    {
        return $this->articleservices->find($id);
    }

    public function update($id, array $data)
    {
        return $this->articleservices->update($id, $data);
    }
    public function delete($id)
    {
        return $this->articleservices->delete($id);
    }

    public function findByLibelle($libelle)
    {
        return $this->articleservices->findByLibelle($libelle);
    }
    public function findByEtat($etat)
    {
        return $this->articleservices->findByEtat($etat);
    }

}

 