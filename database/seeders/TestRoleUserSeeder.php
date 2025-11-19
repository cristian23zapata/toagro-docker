<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\Driver;

class TestRoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure roles exist
        $roles = ['admin', 'gestor', 'chofer', 'cliente'];
        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r]);
        }

        $adminRole = Role::where('name', 'admin')->first();
        $gestorRole = Role::where('name', 'gestor')->first();
        $choferRole = Role::where('name', 'chofer')->first();
        $clienteRole = Role::where('name', 'cliente')->first();

        // Create or find users
        $admin = User::firstOrCreate(
            ['email' => 'admin@local.test'],
            ['name' => 'Admin Demo', 'password' => bcrypt('secret'), 'role_id' => $adminRole->id]
        );

        $gestor = User::firstOrCreate(
            ['email' => 'gestor@local.test'],
            ['name' => 'Gestor Demo', 'password' => bcrypt('secret'), 'role_id' => $gestorRole->id]
        );

        $chofer = User::firstOrCreate(
            ['email' => 'chofer@local.test'],
            ['name' => 'Chofer Demo', 'password' => bcrypt('secret'), 'role_id' => $choferRole->id]
        );

        // Create driver record for the chofer user
        if ($chofer) {
            Driver::firstOrCreate(
                ['user_id' => $chofer->id],
                ['license_number' => 'CHOFER-001', 'phone' => '+34123456789', 'status' => Driver::STATUS_ACTIVE]
            );
        }

        $cliente = User::firstOrCreate(
            ['email' => 'cliente@local.test'],
            ['name' => 'Cliente Demo', 'password' => bcrypt('secret'), 'role_id' => $clienteRole->id]
        );

        $this->command->info('Test users created: admin@local.test, gestor@local.test, chofer@local.test, cliente@local.test (password: secret)');
    }
}
