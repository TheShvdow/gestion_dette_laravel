<?php

namespace App\Repository;

use App\Models\Article;
use App\Repository\Interface\ArticleRepositoryInterface;

class ArticleRepository implements ArticleRepositoryInterface
{
    protected $articleRepository;
    public function __construct(Article $article)
    {
        $this->articleRepository = $article;
    }

    public function all()
    {
        return $this->articleRepository->all();
    }

    public function create(array $data)
    {
        return $this->articleRepository->create($data);
    }

    public function find($id)
    {
        return $this->articleRepository->findOrFail($id);
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
        return $this->articleRepository->where('libelle', $libelle)->get();
    }

    public function findByEtat($etat)
    {
        return $this->articleRepository->where('etat', $etat)->get();
    }
}
