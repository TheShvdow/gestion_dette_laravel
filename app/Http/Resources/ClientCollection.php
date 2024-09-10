<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;



/**
 * @OA\Schema(
 *     schema="ClientCollection",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/ClientResource")
 * )
 */

class ClientCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
