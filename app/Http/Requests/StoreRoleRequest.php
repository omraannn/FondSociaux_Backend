<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('créer des rôles');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ];
    }


    /**
     * Messages d'erreur personnalisés pour les règles de validation.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'Le nom du rôle est requis.',
            'name.unique' => 'Ce nom de rôle est déjà pris.',
            'permissions.array' => 'Les permissions doivent être fournies sous forme de tableau.',
            'permissions.*.exists' => 'Certaines permissions fournies n\'existent pas.',
        ];
    }
}
