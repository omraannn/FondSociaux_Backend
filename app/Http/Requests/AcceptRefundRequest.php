<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AcceptRefundRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->can('accepter un remboursement');
    }


    public function rules(): array
    {
        return [
            'comment' => 'nullable|string|max:1000',
        ];
    }


    public function messages(): array
    {
        return [
            'comment.string' => 'Le commentaire doit être une chaîne de caractères.',
            'comment.max' => 'Le commentaire ne peut pas dépasser 1000 caractères.',
        ];
    }
}
