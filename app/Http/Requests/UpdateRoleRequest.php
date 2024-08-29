<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->can('modifier des rôles');
    }


    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:roles,name,' . $this->route('id'),
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom du rôle est requis.',
            'name.unique' => 'Ce nom de rôle est déjà pris.',
            'permissions.array' => 'Les permissions doivent être fournies sous forme de tableau.',
            'permissions.*.exists' => 'Certaines permissions fournies n\'existent pas.',
        ];
    }
}
