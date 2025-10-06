<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeder.
     *
     * @return void
     */
    public function run()
    {
        // Create roles if they don't exist
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $cashierRole = Role::firstOrCreate(['name' => 'cashier']);

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@possystem.com'],
            [
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'phone' => '0800000001',
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now()
            ]
        );
        $admin->assignRole($adminRole);

        // Create manager user
        $manager = User::firstOrCreate(
            ['email' => 'manager@possystem.com'],
            [
                'username' => 'manager',
                'password' => Hash::make('manager123'),
                'first_name' => 'Store',
                'last_name' => 'Manager',
                'phone' => '0800000002',
                'role' => 'manager',
                'is_active' => true,
                'email_verified_at' => now()
            ]
        );
        $manager->assignRole($managerRole);

        // Create cashier user
        $cashier = User::firstOrCreate(
            ['email' => 'cashier@possystem.com'],
            [
                'username' => 'cashier',
                'password' => Hash::make('cashier123'),
                'first_name' => 'Store',
                'last_name' => 'Cashier',
                'phone' => '0800000003',
                'role' => 'cashier',
                'is_active' => true,
                'email_verified_at' => now()
            ]
        );
        $cashier->assignRole($cashierRole);

        $this->command->info('Users created successfully!');
    }
}
