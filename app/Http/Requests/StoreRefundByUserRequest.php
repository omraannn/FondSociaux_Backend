<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRefundByUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->can('créer un remboursement par un utilisateur');
    }


    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'subject' => 'required',
            'message' => 'required',
            'type_fee_id' => 'required|exists:type_fees,id',
            'expense_date' => 'required|date',
            'supporting_documents' => 'required',
            'supporting_documents.*' => 'required|file|mimes:pdf',
        ];
    }


    public function messages(): array
    {
        return [
            'user_id.required' => "L'utilisateur est requis.",
            'user_id.exists' => "L'utilisateur sélectionné n'existe pas.",

            'subject.required' => 'Le sujet est requis.',

            'message.required' => 'Le message est requis.',

            'type_fee_id.required' => 'Le type de frais est requis.',
            'type_fee_id.exists' => 'Le type de frais sélectionné n\'existe pas.',

            'expense_date.required' => "La date de la dépense est requise.",
            'expense_date.date' => "La date de la dépense doit être une date valide.",

            'supporting_documents.required' => 'Les documents justificatifs sont requis.',
            'supporting_documents.*.required' => 'Chaque document justificatif est requis.',
            'supporting_documents.*.file' => 'Chaque document justificatif doit être un fichier.',
            'supporting_documents.*.mimes' => 'Chaque document justificatif doit être au format PDF.',
        ];
    }
}
