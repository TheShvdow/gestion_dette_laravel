<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
//use Laravel\Sanctum\HasApiTokens;


/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     required={"nom", "prenom", "login", "role"},
 *     @OA\Property(
 *         property="nom",
 *         type="string",
 *         example="John"
 *     ),
 *     @OA\Property(
 *         property="prenom",
 *         type="string",
 *         example="Doe"
 *     ),
 *     @OA\Property(
 *         property="login",
 *         type="string",
 *         example="johndoe"
 *     ),
 *     @OA\Property(
 *         property="role",
 *         type="string",
 *         example="CLIENT"
 *     ),
 *     @OA\Property(
 *         property="photo",
 *         type="string",
 *         example="/uploads/users/photo.jpg",
 *         nullable=true
 *     )
 * )
 */


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'prenom',
        'photo',
        'login',
        'roleId',
        'password',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
    //    'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    function client() {
        return $this->hasOne(Client::class,'user_id');
    }

    function role() {
        return $this->belongsTo(Role::class);
    }

    public function hasRole($roleName)
    {
        return $this->role && $this->role->libelle === $roleName;
    }
    
}
