<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRefundByUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->can('mettre à jour un remboursement par un utilisateur');
    }


    public function rules(): array
    {
        return [
            'amount_spent' => 'nullable|numeric',
            'expense_date' => 'nullable|date',
            'quantity' => 'nullable|numeric',
            'subject' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:1000',
            'type_fee_id' => 'nullable|exists:type_fees,id',
            'supporting_documents.*' => 'nullable|file|mimes:pdf',
        ];
    }


    public function messages(): array
    {
        return [
            'amount_spent.numeric' => 'Le montant dépensé doit être un nombre.',
            'expense_date.date' => 'La date de dépense doit être une date valide.',
            'quantity.numeric' => 'La quantité doit être un nombre.',
            'subject.string' => 'L\'objet doit être une chaîne de caractères.',
            'subject.max' => 'L\'objet ne peut pas dépasser 255 caractères.',
            'message.string' => 'Le message doit être une chaîne de caractères.',
            'message.max' => 'Le message ne peut pas dépasser 1000 caractères.',
            'type_fee_id.exists' => 'Le type de frais sélectionné est invalide.',
            'supporting_documents.*.file' => 'Chaque document justificatif doit être un fichier.',
            'supporting_documents.*.mimes' => 'Chaque document justificatif doit être au format PDF.',
        ];
    }
}
