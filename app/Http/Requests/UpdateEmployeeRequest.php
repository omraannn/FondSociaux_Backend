<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user_id = $this->route('user_id');

        return [
            'firstname' => 'nullable|string|max:255',
            'lastname' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email,' . $user_id,
            'age' => 'nullable|integer',
            'cin' => 'nullable|digits:8|unique:users,cin,' . $user_id,
            'front_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'back_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'address' => 'nullable|string|max:255',
            'tel' => 'nullable|digits:8|unique:users,tel,' . $user_id,
            'status' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'firstname.string' => 'Le prénom doit être une chaîne de caractères.',
            'firstname.max' => 'Le prénom ne peut pas dépasser :max caractères.',
            'lastname.string' => 'Le nom de famille doit être une chaîne de caractères.',
            'lastname.max' => 'Le nom de famille ne peut pas dépasser :max caractères.',
            'email.email' => 'L\'email doit être une adresse email valide.',
            'email.max' => 'L\'email ne peut pas dépasser :max caractères.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'age.integer' => 'L\'âge doit être un nombre entier.',
            'cin.digits' => 'Le CIN doit comporter exactement :digits chiffres.',
            'cin.unique' => 'Ce CIN est déjà utilisé.',
            'front_image.image' => 'L\'image de devant doit être un fichier image.',
            'front_image.mimes' => 'L\'image de devant doit être de type :values.',
            'front_image.max' => 'L\'image de devant ne peut pas dépasser :max Ko.',
            'back_image.image' => 'L\'image de derrière doit être un fichier image.',
            'back_image.mimes' => 'L\'image de derrière doit être de type :values.',
            'back_image.max' => 'L\'image de derrière ne peut pas dépasser :max Ko.',
            'address.string' => 'L\'adresse doit être une chaîne de caractères.',
            'address.max' => 'L\'adresse ne peut pas dépasser :max caractères.',
            'tel.digits' => 'Le numéro de téléphone doit comporter exactement :digits chiffres.',
            'tel.unique' => 'Ce numéro de téléphone est déjà utilisé.',
            'status.boolean' => 'Le statut doit être un booléen.',
        ];
    }
}
