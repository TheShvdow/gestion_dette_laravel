<?php

namespace App\Repository;

use App\Models\Dette;
use App\Repository\Interface\DetteRepositoryInterface;

class DetteRepository implements DetteRepositoryInterface
{

    public function all()
    {
        return Dette::with('client:id,surname,telephone')
            ->select('id', 'date', 'montant', 'montantDu', 'montantRestant', 'client_id', 'status')
            ->get();
    }

    public function state($state)
    {
        return Dette::with('client:id,surname,telephone')
            ->select('id', 'date', 'montant', 'montantDu', 'montantRestant', 'client_id', 'status')
            ->where(function ($query) use ($state) {
                if (in_array('Solde', $state)) {
                    $query->orWhere('montantRestant', '=', 0);
                }
                if (in_array('NonSolde', $state)) {
                    $query->orWhere('montantRestant', '>', 0);
                }
            })
            ->get();
    }
    public function create(array $data)
    {
        return Dette::create($data);
    }
    public function find(int $id)
    {
        return Dette::with('client:id,surname,telephone')
            ->select('id', 'date', 'montant', 'montantDu', 'montantRestant', 'client_id', 'article_details', 'status')
            ->findOrFail($id);
    }
}
