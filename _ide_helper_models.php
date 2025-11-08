<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @OA\Schema (
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
 * @property int $id
 * @property string $libelle
 * @property string $prix
 * @property int $quantite
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Database\Factories\ArticleFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Article newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Article newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Article onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Article query()
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereLibelle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article wherePrix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereQuantite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Article withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperArticle {}
}

namespace App\Models{
/**
 * 
 *
 * @OA\Schema (
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
 * @property int $id
 * @property string $surname
 * @property string $telephone
 * @property string|null $adresse
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\ClientFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Client filter($telephone = null, $surnume = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Client newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Client newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Client query()
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereAdresse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereSurname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereTelephone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperClient {}
}

namespace App\Models{
/**
 * 
 *
 * @OA\Schema (
 *     schema="Dette",
 *     type="object",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="date",
 *         type="string",
 *         format="date-time",
 *         example="2024-01-01T00:00:00Z"
 *     ),
 *     @OA\Property(
 *         property="montant",
 *         type="number",
 *         format="float",
 *         example=29.99
 *     ),
 *     @OA\Property(
 *         property="montantDu",
 *         type="number",
 *         format="float",
 *         example=22.99
 *     ),
 *     @OA\Property(
 *         property="montantRestant",
 *         type="number",
 *         format="float",
 *         example=7.99
 *     ),
 *     @OA\Property(
 *         property="client_id",
 *         type="integer",
 *         format="int64",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="article_details",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="id", type="integer", format="int64", example=1),
 *             @OA\Property(property="article_id", type="integer", format="int64", example=1),
 *             @OA\Property(property="libelle", type="string", example="Article Example"),
 *             @OA\Property(property="quantite", type="integer", format="int64", example=1),
 *             @OA\Property(property="prixVente", type="number", format="float", example=29.99)
 *         )
 *     )
 * )
 * @property int $id
 * @property string $date
 * @property string $montant
 * @property string $montantDu
 * @property string $montantRestant
 * @property int $client_id
 * @property array $article_details
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Client $client
 * @property mixed $articles
 * @method static \Database\Factories\DetteFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Dette newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Dette newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Dette query()
 * @method static \Illuminate\Database\Eloquent\Builder|Dette whereArticleDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dette whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dette whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dette whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dette whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dette whereMontant($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dette whereMontantDu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dette whereMontantRestant($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dette whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperDette {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $libelle
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\RoleFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereLibelle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperRole {}
}

namespace App\Models{
/**
 * 
 *
 * @OA\Schema (
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
 * @property int $id
 * @property string $nom
 * @property string $prenom
 * @property string $login
 * @property mixed $password
 * @property string|null $photo
 * @property int $roleId
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $refresh_token
 * @property-read \App\Models\Client|null $client
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Passport\Client> $clients
 * @property-read int|null $clients_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Role|null $role
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Passport\Token> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePrenom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRefreshToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUser {}
}

