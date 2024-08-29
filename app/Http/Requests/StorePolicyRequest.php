<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePolicyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('créer une politique');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'sub_description' => 'required|string',
            'active' => 'required|boolean',
        ];
    }

    public function messages() : array
    {
        return [
            'title.required' => 'Le titre est obligatoire.',
            'title.max' => 'Le titre ne doit pas dépasser 255 caractères.',
            'description.required' => 'La description est obligatoire.',
            'description.max' => 'La description ne doit pas dépasser 1000 caractères.',
            'sub_description.required' => 'La sous-description est obligatoire.',
            'active.required' => 'Le statut actif est obligatoire.',
            'active.boolean' => 'Le statut actif doit être vrai ou faux.',
        ];
    }
}
