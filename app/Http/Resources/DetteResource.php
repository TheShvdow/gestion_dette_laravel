<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\ClientResource;
use Illuminate\Http\Resources\Json\JsonResource;

class DetteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'date' => $this->date,
            'montant' => $this->montant,    
            'montantDu' => $this->montantDu,
            'montantRestant' => $this->montantRestant,
            'clientId' => new ClientResource($this->whenLoaded('client')),
            'article_details' => $this->article_details
        ];
    }

}
