<?php

namespace App\Http\Requests;

use App\Enums\StateEnum;
use App\Rules\CustumPasswordRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @OA\Schema(
 *     schema="StoreUserRequest",
 *     type="object",
 *     required={"login", "password", "roleId"},
 *     @OA\Property(
 *         property="login",
 *         type="string",
 *         example="admin.user",
 *         description="Login unique (obligatoire)"
 *     ),
 *     @OA\Property(
 *         property="password",
 *         type="string",
 *         example="SecureP@ss2024!",
 *         description="Mot de passe (obligatoire, min 5 caractères avec majuscules, minuscules, chiffres et caractères spéciaux)"
 *     ),
 *     @OA\Property(
 *         property="roleId",
 *         type="integer",
 *         example=1,
 *         description="ID du rôle (1=Admin, 2=Boutiquier)"
 *     )
 * )
 */

class StoreUserRequest extends FormRequest
{
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
            'login' => ['required', 'string', 'max:255', 'unique:users,login'],
            'password' => ['required', new CustumPasswordRule()],
            'roleId' => ['required', 'integer', 'in:1,2'], // Seulement Admin (1) ou Boutiquier (2)
        ];
    }

    public function messages(): array
    {
        return [
            'login.required' => 'Le login est obligatoire.',
            'login.unique' => 'Ce login est déjà utilisé.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'roleId.required' => 'Le rôle est obligatoire.',
            'roleId.in' => 'Le rôle doit être Admin (1) ou Boutiquier (2).',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 411,
            'data' => $validator->errors(),
            'message' => 'Erreur de validation'
        ], 411));
    }
}
