<?php

namespace App\Repository;

use App\Models\Dette;
use App\Repository\Interface\DetteRepositoryInterface;

class DetteRepository implements DetteRepositoryInterface
{

    public function all()
    {
        return Dette::all();
    }

    public function state($state)
    {
        return Dette::where(function($query) use ($state) {
            if (in_array('Solde', $state)) {
                $query->orWhere('montantRestant', '=', 0);
            }
            if (in_array('NonSolde', $state)) {
                $query->orWhere('montantRestant', '>', 0);
            }
        })->get();
    }
}
