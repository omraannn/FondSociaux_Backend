<?php


namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTypeFeeRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('créer un type de frais');
    }

    /**
     * Règles de validation applicables à la requête.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'percentage' => 'required_if:refund_type,percentage',
            'unit_price' => 'required_if:refund_type,per_unit',
            'ceiling' => 'nullable|numeric|min:0',
            'ceiling_type' => 'required|in:none,per_day,per_year',
            'refund_type' => 'required|in:percentage,per_unit',
            'percentage.max' => 'La valeur du champ pourcentage ne doit pas être supérieure à 100.',
            'ceiling.min' => 'La valeur du champ plafond ne doit pas être inférieure à 0.',
        ];
    }

    /**
     * Messages d'erreur personnalisés pour les règles de validation.
     *
     * @return array
     */
    public function messages():array
    {
        return [
            'category_id.required' => 'Le champ catégorie est obligatoire.',
            'category_id.exists' => 'La catégorie sélectionnée est invalide.',
            'title.required' => 'Le champ titre est obligatoire.',
            'title.max' => 'Le titre ne doit pas dépasser 255 caractères.',
            'description.required' => 'Le champ description est obligatoire.',
            'description.max' => 'La description ne doit pas dépasser 1000 caractères.',
            'percentage.required_if' => 'Le champ pourcentage est obligatoire.',
            'percentage.max' => 'La valeur du champ pourcentage ne doit pas être supérieure à 100.',
            'unit_price.required_if' => 'Ce champ est obligatoire.',
            'unit_price.min' => 'Le prix unitaire ne peut pas être inférieur à 0.',
            'ceiling.min' => 'La valeur du champ plafond ne doit pas être inférieure à 0.',
            'ceiling_type.required' => 'Le champ type de plafond est obligatoire.',
            'ceiling_type.in' => 'Le type de plafond sélectionné est invalide.',
            'refund_type.required' => 'Le type de remboursement est obligatoire.',
            'refund_type.in' => 'Le type de remboursement sélectionné est invalide.',
        ];
    }
}
