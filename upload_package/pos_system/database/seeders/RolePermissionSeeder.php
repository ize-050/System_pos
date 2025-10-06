<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // สร้าง Permissions
        $permissions = [
            // User Management
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            
            // Product Management
            'view-products',
            'create-products',
            'edit-products',
            'delete-products',
            
            // Category Management
            'view-categories',
            'create-categories',
            'edit-categories',
            'delete-categories',
            
            // Sales Management
            'view-sales',
            'create-sales',
            'edit-sales',
            'delete-sales',
            
            // Reports
            'view-reports',
            'export-reports',
            
            // Settings
            'manage-settings',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // สร้าง Roles
        $adminRole = Role::create(['name' => 'admin']);
        $managerRole = Role::create(['name' => 'manager']);
        $cashierRole = Role::create(['name' => 'cashier']);

        // กำหนด Permissions ให้ Roles
        $adminRole->givePermissionTo(Permission::all());
        
        $managerRole->givePermissionTo([
            'view-users', 'create-users', 'edit-users',
            'view-products', 'create-products', 'edit-products', 'delete-products',
            'view-categories', 'create-categories', 'edit-categories', 'delete-categories',
            'view-sales', 'create-sales', 'edit-sales',
            'view-reports', 'export-reports',
        ]);
        
        $cashierRole->givePermissionTo([
            'view-products',
            'view-categories',
            'view-sales', 'create-sales',
        ]);

        // สร้าง Admin User
        $admin = User::create([
            'username' => 'admin',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@pos.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
        
        $admin->assignRole('admin');

        // สร้าง Manager User
        $manager = User::create([
            'username' => 'manager',
            'first_name' => 'Manager',
            'last_name' => 'User',
            'email' => 'manager@pos.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'email_verified_at' => now(),
        ]);
        
        $manager->assignRole('manager');

        // สร้าง Cashier User
        $cashier = User::create([
            'username' => 'cashier',
            'first_name' => 'Cashier',
            'last_name' => 'User',
            'email' => 'cashier@pos.com',
            'password' => Hash::make('password'),
            'role' => 'cashier',
            'email_verified_at' => now(),
        ]);
        
        $cashier->assignRole('cashier');
    }
}
