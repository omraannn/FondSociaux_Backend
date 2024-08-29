<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'permission globale',

            // Gestion des utilisateurs
            'voir les employés',
            'créer un employé',
            'mettre à jour un employé',
            'supprimer un employé',
            'confirmer un employé',
            'rejeter un employé',
            'voir le profil',
            'mettre à jour le profil',

            // Gestion des catégories
            'voir les catégories',
            'créer une catégorie',
            'mettre à jour une catégorie',
            'supprimer une catégorie',

            // Gestion des politiques
            'voir les politiques',
            'créer une politique',
            'mettre à jour une politique',
            'supprimer une politique',

            // Gestion des types de frais
            'voir les types de frais',
            'créer un type de frais',
            'mettre à jour un type de frais',
            'supprimer un type de frais',

            // Gestion des remboursements
            'voir les remboursements',
            'créer un remboursement pour un utilisateur',
            'créer un remboursement par un utilisateur',
            'mettre à jour un remboursement pour un utilisateur',
            'mettre à jour un remboursement par un utilisateur',
            'annuler un remboursement',
            'supprimer un remboursement',
            'accepter un remboursement',
            'rejeter un remboursement',
            'mettre à jour le statut payé',

            // Gestion des rôles
            'voir les rôles',
            'créer des rôles',
            'modifier des rôles',
            'supprimer des rôles',

            // Statistiques
            'voir les statistiques des utilisateurs',
            'voir les statistiques par utilisateur',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
