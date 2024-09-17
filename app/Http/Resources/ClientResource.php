<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @OA\Schema(
 *     schema="ClientResource",
 *     type="object",
 *     title="Client Resource",
 *     properties={
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="surname", type="string", example="Doe"),
 *         @OA\Property(property="adresse", type="string", example="123 Rue Principale"),
 *         @OA\Property(property="telephone", type="string", example="778275234")
 *     }
 * )
 */

class ClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            
            'surnom' => $this->surname,
            'adresse' => $this->adresse,
            'telephone' => $this->telephone,
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
