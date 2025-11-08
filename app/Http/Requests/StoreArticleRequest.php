<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @OA\Schema(
 *     schema="StoreArticleRequest",
 *     type="object",
 *     required={"libelle", "prix", "qteStock"},
 *     @OA\Property(
 *         property="libelle",
 *         type="string",
 *         example="Lait Laicran 700g",
 *         description="Libellé de l'article (obligatoire et unique)"
 *     ),
 *     @OA\Property(
 *         property="prix",
 *         type="number",
 *         format="float",
 *         example=1700,
 *         description="Prix de l'article (obligatoire)"
 *     ),
 *     @OA\Property(
 *         property="qteStock",
 *         type="integer",
 *         example=1000,
 *         description="Quantité en stock (obligatoire)"
 *     )
 * )
 */

class StoreArticleRequest extends FormRequest
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
            'libelle' => 'required|unique:articles,libelle|string',
            'prix' => 'required|numeric|min:0',
            'qteStock' => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'libelle.required' => 'Le libellé est obligatoire.',
            'libelle.unique' => 'Le libellé doit être unique.',
            'prix.required' => 'Le prix est obligatoire.',
            'prix.numeric' => 'Le prix doit être un nombre.',
            'prix.min' => 'Le prix doit être supérieur ou égal à 0.',
            'qteStock.required' => 'La quantité en stock est obligatoire.',
            'qteStock.integer' => 'La quantité en stock doit être un nombre entier.',
            'qteStock.min' => 'La quantité en stock doit être supérieure ou égale à 0.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 411,
            'data' => $validator->errors(),
            'message' => 'Erreur de validation'
        ], 411));
    }
}
