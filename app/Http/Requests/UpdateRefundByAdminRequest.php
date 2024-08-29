<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRefundByAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->can('mettre à jour un remboursement pour un utilisateur');
    }


    public function rules(): array
    {
        return [
            'amount_spent' => 'nullable|numeric',
            'expense_date' => 'nullable|date',
            'quantity' => 'nullable|numeric',
            'subject' => 'nullable|string|max:255',
            'message' => 'nullable|string',
            'RH_comment' => 'nullable|string',
            'type_fee_id' => 'nullable|exists:type_fees,id',
            'supporting_documents.*' => 'nullable|file|mimes:pdf',
        ];
    }


    public function messages(): array
    {
        return [
            'amount_spent.numeric' => 'Le montant dépensé doit être un nombre valide.',
            'expense_date.date' => 'La date de dépense doit être une date valide.',
            'quantity.numeric' => 'La quantité doit être un nombre valide.',
            'subject.string' => 'Le sujet doit être une chaîne de caractères.',
            'subject.max' => 'Le sujet ne peut pas dépasser 255 caractères.',
            'message.string' => 'Le message doit être une chaîne de caractères.',
            'RH_comment.string' => 'Le commentaire de l\'administrateur doit être une chaîne de caractères.',
            'type_fee_id.exists' => 'Le type de frais sélectionné n\'existe pas.',
            'supporting_documents.*.file' => 'Le fichier de support doit être un fichier.',
            'supporting_documents.*.mimes' => 'Le fichier de support doit être au format PDF.',
        ];
    }
}
