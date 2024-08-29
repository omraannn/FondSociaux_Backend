<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePolicyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->can('mettre à jour une politique');
    }


    public function rules(): array
    {
        return [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'sub_description' => 'nullable|string',
            'active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'title.max' => 'Le titre ne doit pas dépasser 255 caractères.',
            'description.max' => 'La description ne doit pas dépasser 1000 caractères.',
            'active.boolean' => 'Le statut actif doit être vrai ou faux.',
        ];
    }
}
