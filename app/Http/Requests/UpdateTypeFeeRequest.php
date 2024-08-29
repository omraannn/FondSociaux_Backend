<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTypeFeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('mettre à jour un type de frais');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => 'nullable|exists:categories,id',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'percentage' => 'required_if:refund_type,percentage',
            'unit_price' => 'required_if:refund_type,per_unit',
            'ceiling' => 'nullable|numeric|min:0',
            'ceiling_type' => 'required|in:none,per_day,per_year',
            'refund_type' => 'nullable|in:percentage,per_unit',
        ];
    }


    public function messages() : array
    {
        return [
            'category_id.exists' => 'La catégorie sélectionnée est invalide.',
            'title.max' => 'Le titre ne doit pas dépasser 255 caractères.',
            'description.max' => 'La description ne doit pas dépasser 1000 caractères.',
            'percentage.required_if' => 'Ce champ est obligatoire.',
            'percentage.max' => 'La valeur du champ pourcentage ne doit pas être supérieure à 100.',
            'unit_price.required_if' => 'Ce champ est obligatoire.',
            'unit_price.min' => 'Le prix unitaire ne peut pas être inférieur à 0.',
            'ceiling.min' => 'La valeur du champ plafond ne doit pas être inférieure à 0.',
            'ceiling_type.required' => 'Le champ type de plafond est obligatoire.',
            'ceiling_type.in' => 'Le type de plafond sélectionné est invalide.',
            'refund_type.in' => 'Le type de remboursement sélectionné est invalide.',
        ];
    }
}
