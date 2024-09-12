<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Article",
 *     type="object",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="libelle",
 *         type="string",
 *         example="Article Example"
 *     ),
 *     @OA\Property(
 *         property="prix",
 *         type="number",
 *         format="float",
 *         example=29.99
 *     ),
 *     @OA\Property(
 *         property="quantite",
 *         type="integer",
 *         format="int32",
 *         example=100
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         example="2024-01-01T00:00:00Z"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         example="2024-01-01T00:00:00Z"
 *     ),
 *     @OA\Property(
 *         property="deleted_at",
 *         type="string",
 *         format="date-time",
 *         example="2024-01-01T00:00:00Z"
 *     )
 * )
 */


class Article extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'libelle',
        'prix',
        'quantite',
        
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
        
    ];

    public function scoopFilter($query,$critere)
    {
        if (isset($critere['libelle'])) {
            $query = $query->where('libelle', 'like', '%' . $critere['libelle'] . '%');
        }
        return $query;

    }

}
