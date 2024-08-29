<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Gestion des utilisateurs
            'voir les employés',
            'créer un employé',
            'mettre à jour un employé',
            'supprimer un employé',
            'confirmer un employé',
            'rejeter un employé',

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
            'voir les remboursements par utilisateur',
            'voir les remboursements des utilisateurs',
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
            \Spatie\Permission\Models\Permission::create(['name' => $permission]);
        }

        $roles = [
            'GUEST',
            'RH',
            'EMPLOYEE',
        ];

        foreach ($roles as $role) {
            \Spatie\Permission\Models\Role::create(['name' => $role]);
        }

        // Assign permissions to roles
        $adminRole = \Spatie\Permission\Models\Role::findByName('RH');
        $adminRole->givePermissionTo([
            // Gestion des utilisateurs
            'voir les employés',
            'créer un employé',
            'mettre à jour un employé',
            'supprimer un employé',
            'confirmer un employé',
            'rejeter un employé',

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
            'voir les remboursements des utilisateurs',
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
        ]);

        $employeeRole = \Spatie\Permission\Models\Role::findByName('EMPLOYEE');
        $employeeRole->givePermissionTo([
            'créer un remboursement par un utilisateur',
            'voir les statistiques par utilisateur',
            'mettre à jour un remboursement par un utilisateur',
            'annuler un remboursement',
            'voir les remboursements par utilisateur'
        ]);


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
