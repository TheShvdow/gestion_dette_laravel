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
 *     required={"surname", "adresse", "telephone", "user"},
 *     @OA\Property(
 *         property="surname",
 *         type="string",
 *         example="Doe"
 *     ),
 *     @OA\Property(
 *         property="adresse",
 *         type="string",
 *         example="123 Rue Principale"
 *     ),
 *     @OA\Property(
 *         property="telephone",
 *         type="string",
 *         example="771234567"
 *     ),
 *     @OA\Property(
 *         property="user",
 *         type="object",
 *         required={"nom", "prenom", "login", "password", "roleId"},
 *         @OA\Property(property="nom", type="string", example="John"),
 *         @OA\Property(property="prenom", type="string", example="Doe"),
 *         @OA\Property(property="login", type="string", example="johndoe"),
 *         @OA\Property(property="password", type="string", example="Password123!"),
 *         @OA\Property(property="roleId", type="integer", example="3")
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
        $rules = [
            'surname' => ['required', 'string', 'max:255','unique:clients,surname'],
            'address' => ['string', 'max:255'],
            'telephone' => ['required',new TelephoneRule()],'unique:clients,telephone',

            'user' => ['sometimes','array'],
            'user.nom' => ['required_with:user','string'],
            'user.prenom' => ['required_with:user','string'],
            'user.login' => ['required_with:user','string'],
            'user.password' => ['required_with:user', new CustumPasswordRule(),'confirmed'],
            'user.roleId' => ['required_with:user','integer']
        ];
/*
        if ($this->filled('user')) {
            $userRules = (new StoreUserRequest())->Rules();
            $rules = array_merge($rules, ['user' => 'array']);
            $rules = array_merge($rules, array_combine(
                array_map(fn($key) => "user.$key", array_keys($userRules)),
                $userRules
            ));
        }
*/
      //  dd($rules);

        return $rules;
    }

    function messages()
    {
        return [
            'surname.required' => "Le surnom est obligatoire.",
            'telephone.required' => "Le numéro de téléphone est obligatoire.",
        ];
    }

    function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->sendResponse($validator->errors(),StateEnum::ECHEC,404));
    }
}
