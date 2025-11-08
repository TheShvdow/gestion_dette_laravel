<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Article;

/**
 * @OA\Schema(
 *     schema="StoreDetteRequest",
 *     type="object",
 *     required={"montant", "clientId", "articles"},
 *     @OA\Property(property="montant", type="number", format="float", example=1000000),
 *     @OA\Property(property="clientId", type="integer", example=1),
 *     @OA\Property(
 *         property="articles",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             required={"articleId", "qteVente", "prixVente"},
 *             @OA\Property(property="articleId", type="integer", example=3),
 *             @OA\Property(property="qteVente", type="integer", example=500),
 *             @OA\Property(property="prixVente", type="number", format="float", example=100)
 *         )
 *     ),
 *     @OA\Property(
 *         property="paiement",
 *         type="object",
 *         @OA\Property(property="montant", type="number", format="float", example=500000)
 *     )
 * )
 */
class StoreDetteRequest extends FormRequest
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
            'clientId' => 'required|exists:clients,id',
            'articles' => 'required|array|min:1',
            'articles.*.articleId' => 'required|exists:articles,id',
            'articles.*.qteVente' => 'required|integer|min:1',
            'articles.*.prixVente' => 'required|numeric|min:0',
            'paiement' => 'nullable|array',
            'paiement.montant' => 'nullable|numeric|min:0|lte:montant',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Vérifier que la qteVente est inférieure ou égale à la quantité en stock
            if ($this->has('articles')) {
                foreach ($this->input('articles') as $index => $articleData) {
                    $article = Article::find($articleData['articleId'] ?? null);

                    if ($article && isset($articleData['qteVente'])) {
                        if ($articleData['qteVente'] > $article->quantite) {
                            $validator->errors()->add(
                                "articles.{$index}.qteVente",
                                "La quantite vendue ne peut pas depasser la quantite en stock ({$article->quantite})"
                            );
                        }
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
            'clientId.required' => 'Le client est obligatoire',
            'clientId.exists' => 'Le client specifie n\'existe pas',
            'articles.required' => 'Les articles sont obligatoires',
            'articles.array' => 'Les articles doivent etre un tableau',
            'articles.min' => 'Au moins un article est requis',
            'articles.*.articleId.required' => 'L\'ID de l\'article est obligatoire',
            'articles.*.articleId.exists' => 'L\'article specifie n\'existe pas',
            'articles.*.qteVente.required' => 'La quantite vendue est obligatoire',
            'articles.*.qteVente.integer' => 'La quantite vendue doit etre un entier',
            'articles.*.qteVente.min' => 'La quantite vendue doit etre superieure a 0',
            'articles.*.prixVente.required' => 'Le prix de vente est obligatoire',
            'articles.*.prixVente.numeric' => 'Le prix de vente doit etre un nombre',
            'articles.*.prixVente.min' => 'Le prix de vente doit etre positif',
            'paiement.array' => 'Le paiement doit etre un objet',
            'paiement.montant.numeric' => 'Le montant du paiement doit etre un nombre',
            'paiement.montant.min' => 'Le montant du paiement doit etre positif',
            'paiement.montant.lte' => 'Le montant du paiement ne peut pas depasser le montant de la dette',
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
