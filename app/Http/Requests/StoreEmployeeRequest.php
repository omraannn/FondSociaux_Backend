<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('créer un employé');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'age' => 'required|integer',
            'cin' => 'required|string|max:255|unique:users,cin',
            'front_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'back_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'address' => 'required|string|max:255',
            'tel' => 'required|string|max:20|unique:users',
        ];
    }

    public function messages(): array
    {
        return [
            'firstname.required' => 'Le prénom est requis.',
            'firstname.string' => 'Le prénom doit être une chaîne de caractères.',
            'firstname.max' => 'Le prénom ne peut pas dépasser :max caractères.',
            'lastname.required' => 'Le nom est requis.',
            'lastname.string' => 'Le nom doit être une chaîne de caractères.',
            'lastname.max' => 'Le nom ne peut pas dépasser :max caractères.',
            'email.required' => 'L\'email est requis.',
            'email.email' => 'L\'email doit être une adresse email valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'age.required' => 'L\'âge est requis.',
            'age.integer' => 'L\'âge doit être un entier.',
            'cin.required' => 'Le CIN est requis.',
            'cin.string' => 'Le CIN doit être une chaîne de caractères.',
            'cin.max' => 'Le CIN ne peut pas dépasser :max caractères.',
            'cin.unique' => 'Ce CIN est déjà utilisé.',
            'front_image.required' => 'L\'image de devant est requise.',
            'front_image.image' => 'Le fichier doit être une image.',
            'front_image.max' => 'L\'image de devant ne peut pas dépasser :max Ko.',
            'back_image.required' => 'L\'image de dos est requise.',
            'back_image.image' => 'Le fichier doit être une image.',
            'back_image.max' => 'L\'image de dos ne peut pas dépasser :max Ko.',
            'address.required' => 'L\'adresse est requise.',
            'address.string' => 'L\'adresse doit être une chaîne de caractères.',
            'address.max' => 'L\'adresse ne peut pas dépasser :max caractères.',
            'tel.required' => 'Le numéro de téléphone est requis.',
            'tel.string' => 'Le téléphone doit être une chaîne de caractères.',
            'tel.max' => 'Le numéro de téléphone ne peut pas dépasser :max caractères.',
            'tel.unique' => 'Ce numéro de téléphone est déjà utilisé.',
        ];
    }
}
