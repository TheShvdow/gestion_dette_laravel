<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Dette;

class StorePaiementRequest extends FormRequest
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
            'montant' => 'required|numeric|min:0',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Récupérer l'ID de la dette depuis la route
            $detteId = $this->route('id');

            if ($detteId) {
                $dette = Dette::find($detteId);

                if ($dette && $this->has('montant')) {
                    $montant = $this->input('montant');

                    // Vérifier que le montant est inférieur ou égal au montant restant
                    if ($montant > $dette->montantRestant) {
                        $validator->errors()->add(
                            'montant',
                            "Le montant du paiement ne peut pas depasser le montant restant ({$dette->montantRestant})"
                        );
                    }
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'montant.required' => 'Le montant est obligatoire',
            'montant.numeric' => 'Le montant doit etre un nombre',
            'montant.min' => 'Le montant doit etre positif',
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
