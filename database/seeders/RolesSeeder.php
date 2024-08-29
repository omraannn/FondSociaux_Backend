<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create RH role
        $rhRole = Role::firstOrCreate([
            'name' => 'RH',
            'guard_name' => 'api'
        ]);
        $permissions = Permission::all();
        $rhRole->syncPermissions($permissions);


        // Create EMPLOYEE role
        $employeeRole = Role::firstOrCreate([
            'name' => 'EMPLOYEE',
            'guard_name' => 'api'
        ]);
        $employeePermissions = [
            'permission globale',
            'voir le profil',
            'mettre à jour le profil',
            'voir les catégories',
            'voir les politiques',
            'voir les types de frais',
            'voir les remboursements',
            'créer un remboursement par un utilisateur',
            'mettre à jour un remboursement par un utilisateur',
            'annuler un remboursement',
            'voir les statistiques par utilisateur'
        ];
        $employeePermissions = Permission::whereIn('name', $employeePermissions)->get();
        $employeeRole->syncPermissions($employeePermissions);


        // Create GUEST role
        $guestRole = Role::firstOrCreate([
            'name' => 'GUEST',
            'guard_name' => 'api'
        ]);
        $guestPermissions = [
            'permission globale',
            'voir le profil',
            'mettre à jour le profil',
            'voir les politiques'
        ];
        $guestPermissions = Permission::whereIn('name', $guestPermissions)->get();
        $guestRole->syncPermissions($guestPermissions);
    }
}
