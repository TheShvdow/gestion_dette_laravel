<?php

namespace App\Http\Requests;


use App\Enums\StateEnum;
use App\Rules\CustumPasswordRule;
use App\Rules\TelephoneRule;
use App\Traits\RestResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @OA\Schema(
 *     schema="StoreClientRequest",
 *     type="object",
 *     required={"nom", "prenom", "telephone"},
 *     @OA\Property(
 *         property="nom",
 *         type="string",
 *         example="Wade",
 *         description="Nom du client (obligatoire)"
 *     ),
 *     @OA\Property(
 *         property="prenom",
 *         type="string",
 *         example="Idrissa",
 *         description="Prénom du client (obligatoire)"
 *     ),
 *     @OA\Property(
 *         property="telephone",
 *         type="string",
 *         example="771234567",
 *         description="Téléphone portable sénégalais (obligatoire et unique)"
 *     ),
 *     @OA\Property(
 *         property="login",
 *         type="string",
 *         example="idrissa.wade",
 *         description="Login pour créer un compte utilisateur (optionnel mais unique)"
 *     ),
 *     @OA\Property(
 *         property="password",
 *         type="string",
 *         example="SecureP@ss2024!",
 *         description="Mot de passe (obligatoire si login fourni, min 5 caractères avec majuscules, minuscules, chiffres et caractères spéciaux)"
 *     )
 * )
 */



class StoreClientRequest extends FormRequest
{
    use RestResponseTrait;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'telephone' => ['required', new TelephoneRule(), 'unique:clients,telephone'],

            // Si login est fourni, alors password est obligatoire
            'login' => ['nullable', 'string', 'unique:users,login'],
            'password' => ['required_with:login', new CustumPasswordRule()],
        ];
    }

    function messages()
    {
        return [
            'nom.required' => "Le nom est obligatoire.",
            'prenom.required' => "Le prénom est obligatoire.",
            'telephone.required' => "Le numéro de téléphone est obligatoire.",
            'telephone.unique' => "Ce numéro de téléphone existe déjà.",
            'login.unique' => "Ce login existe déjà.",
            'password.required_with' => "Le mot de passe est obligatoire lorsque le login est fourni.",
        ];
    }

    function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 411,
            'data' => $validator->errors(),
            'message' => 'Erreur de validation'
        ], 411));
    }
}
