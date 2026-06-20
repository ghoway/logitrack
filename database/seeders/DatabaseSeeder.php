<?php

namespace Database\Seeders;

use App\Models\Rate;
use App\Models\Route;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Clear Spatie cached permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $courierRole = Role::firstOrCreate(['name' => 'courier']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Define permissions
        $permissions = [
            'ViewAny:Route', 'View:Route',
            'ViewAny:Rate', 'View:Rate',
            'ViewAny:Shipment', 'View:Shipment', 'Create:Shipment', 'Update:Shipment',
            'ViewAny:Payment', 'View:Payment',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $courierRole->syncPermissions([
            'ViewAny:Shipment', 'View:Shipment', 'Update:Shipment',
        ]);

        $userRole->syncPermissions([
            'ViewAny:Route', 'View:Route',
            'ViewAny:Rate', 'View:Rate',
            'ViewAny:Shipment', 'View:Shipment', 'Create:Shipment',
            'ViewAny:Payment', 'View:Payment',
        ]);

        // Create Users & Assign Roles
        $admin = User::firstOrCreate(
            ['email' => 'admin@mail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );
        $admin->syncRoles([$superAdminRole]);

        $courier = User::firstOrCreate(
            ['email' => 'kurir@mail.com'],
            [
                'name' => 'Courier',
                'password' => Hash::make('password'),
            ]
        );
        $courier->syncRoles([$courierRole]);

        $user = User::firstOrCreate(
            ['email' => 'user@mail.com'],
            [
                'name' => 'Customer',
                'password' => Hash::make('password'),
            ]
        );
        $user->syncRoles([$userRole]);

        // Run Bank Seeder
        $this->call([
            BankSeeder::class,
        ]);

        // Seed Sample Routes and Rates
        $route1 = Route::create([
            'origin' => 'Jakarta',
            'destination' => 'Surabaya',
            'is_active' => true,
        ]);

        Rate::create([
            'route_id' => $route1->id,
            'type' => 'darat',
            'price_per_kg' => 10000,
            'estimated_days' => 3,
        ]);

        Rate::create([
            'route_id' => $route1->id,
            'type' => 'pesawat',
            'price_per_kg' => 25000,
            'estimated_days' => 1,
        ]);

        $route2 = Route::create([
            'origin' => 'Bali',
            'destination' => 'Jakarta',
            'is_active' => true,
        ]);

        Rate::create([
            'route_id' => $route2->id,
            'type' => 'darat',
            'price_per_kg' => 15000,
            'estimated_days' => 4,
        ]);

        Rate::create([
            'route_id' => $route2->id,
            'type' => 'pesawat',
            'price_per_kg' => 35000,
            'estimated_days' => 2,
        ]);
    }
}
