<?php

namespace App\Services;

use App\Services\Interfaces\DetteServiceInterface;

class DetteService implements DetteServiceInterface
{

    protected $detteService;
    public function __construct(DetteServiceInterface $detteService) {
        $this->detteService = $detteService;
    }
    public function all()
    {
        return $this->detteService->all();
    }

    public function state($state)
    {
        return $this->detteService->state($state);
    }
}
