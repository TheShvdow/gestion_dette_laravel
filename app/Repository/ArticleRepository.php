<?php

namespace App\Repository;

use App\Models\Article;
use App\Repository\Interface\ArticleRepositoryInterface;

class ArticleRepository implements ArticleRepositoryInterface
{
    protected $article;
    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    public function all()
    {
        return $this->article->all();
    }

    public function create(array $data)
    {
        return $this->article->create($data);
    }

    public function find($id)
    {
        return $this->article->findOrFail($id);
    }

    public function update($id, array $data)
    {
        $article = $this->find($id);
        $article->update($data);
        return $article;
    }

    public function delete($id)
    {
        $article = $this->find($id);
        return $article->delete();
    }

    public function findByLibelle($libelle)
    {
        return $this->article->where('libelle', $libelle)->get();
    }

    public function findByEtat($etat)
    {
        return $this->article->where('etat', $etat)->get();
    }
}
