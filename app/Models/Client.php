<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * @OA\Schema(
 *     schema="Client",
 *     type="object",
 *     required={"Surname", "Telephone"},
 *     @OA\Property(
 *         property="Surname",
 *         type="string",
 *         example="John"
 *     ),
 *     @OA\Property(
 *         property="Telephone",
 *         type="number",
 *         example="77#######"
 *     ),
 *     @OA\Property(
 *         property="Adresse",
 *         type="string",
 *         example="123 Rue Principale"
 *     ),
 * )
 */
class Client extends Model
{
    use HasFactory;

   // public mixed $user_id;
    protected $fillable = [
        'surname',
        'adresse',
        'telephone',
        'user_id'
    ];
    protected $hidden = [
        //  'password',
        'created_at',
        'updated_at',
    ];

    function user() {
        return $this->belongsTo(User::class);
    }
}
