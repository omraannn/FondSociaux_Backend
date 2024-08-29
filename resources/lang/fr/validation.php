<?php

return [
    'required_if' => 'Ce champ est obligatoire.',
    'required' => 'Ce champ est obligatoire.',
    'exists' => 'Ce champ est invalide.',
    'string' => 'Ce champ doit être une chaîne de caractères.',
    'max' => [
        'string' => 'Ce champ ne peut pas dépasser :max caractères.',
        'file' => 'Ce fichier ne peut pas dépasser :max kilo-octets.',
    ],
    'min' => [
        'string' => 'Le champ doit comporter au moins :min caractères.',
    ],
    'email' => 'Ce champ doit être une adresse email valide.',
    'unique' => 'Ce champ a déjà été pris.',
    'integer' => 'Ce champ doit être un nombre entier.',
    'image' => 'Ce champ doit être une image.',
    'mimes' => 'Ce champ doit être un fichier de type : :values.',
    'size' => [
        'string' => 'Ce champ doit être exactement :size caractères.',
    ],
    'attributes' => [
        'firstname' => 'prénom',
        'lastname' => 'nom',
        'email' => 'email',
        'password' => 'mot de passe',
        'age' => 'âge',
        'cin' => 'CIN',
        'front_image' => 'image de face',
        'back_image' => 'image de dos',
        'address' => 'adresse',
        'tel' => 'téléphone',
    ],
    'percentage' => [
        'max' => 'La valeur du champ pourcentage ne doit pas être supérieure à :max.',
        'min' => 'La valeur du champ pourcentage ne doit pas être inférieure à :min.',
    ],
];
