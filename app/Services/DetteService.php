<?php

namespace App\Services;

use App\Repository\Interface\DetteRepositoryInterface;
use App\Services\Interfaces\DetteServiceInterface;

class DetteService implements DetteServiceInterface
{

    protected $detteRepository;
    public function __construct(DetteRepositoryInterface $detteRepository) {
        $this->detteRepository = $detteRepository;
    }
    public function all()
    {
        return $this->detteRepository->all();
    }

    public function state($state)
    {
        return $this->detteRepository->state($state);
    }

    public function create(array $data)
    {
        return $this->detteRepository->create($data);
    }
    public function find(int $id)
    {
        return $this->detteRepository->find($id);
    }
}
