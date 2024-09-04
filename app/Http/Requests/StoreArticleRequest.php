<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'libelle' => 'required|unique:articles|string',
            'prix' => 'required|numeric|min:100.00',
            'quantite' => 'required|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'libelle.required' => 'Le libellé est obligatoire.',
            'libelle.unique' => 'Le libellé doit être unique.',
            'prix.required' => 'Le prix est obligatoire.',
            'prix.numeric' => 'Le prix doit être positive.',
            'quantite.required' => 'La quantité en stock est obligatoire.',
            'prix.float' => 'Le prix doit être un nombre à virgule si possible.',
            'quantite.integer' => 'La quantité en stock doit être un nombre.',
            'quantite.min' => 'La quantité en stock doit être supérieure ou égale à 0.',
            
        ];

    }
}