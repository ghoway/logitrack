<?php

namespace Database\Seeders;

use App\Models\Rate;
use App\Models\Route;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $courierRole = Role::firstOrCreate(['name' => 'courier']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        $permissions = [
            'ViewAny:Route', 'View:Route',
            'ViewAny:Rate', 'View:Rate',
            'ViewAny:Shipment', 'View:Shipment', 'Create:Shipment', 'Update:Shipment',
            'ViewAny:Payment', 'View:Payment',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $courierRole->syncPermissions([
            'ViewAny:Shipment', 'View:Shipment', 'Update:Shipment',
        ]);

        $userRole->syncPermissions([
            'ViewAny:Route', 'View:Route',
            'ViewAny:Rate', 'View:Rate',
            'ViewAny:Shipment', 'View:Shipment', 'Create:Shipment',
            'ViewAny:Payment', 'View:Payment',
        ]);

        $admin = User::updateOrCreate(
            ['email' => 'wahyu@mail.com'],
            [
                'name' => 'Wahyu',
                'password' => Hash::make('password'),
            ]
        );
        $admin->syncRoles([$superAdminRole]);

        $courierHumam = User::updateOrCreate(
            ['email' => 'humam@mail.com'],
            [
                'name' => 'Humam',
                'password' => Hash::make('password'),
            ]
        );
        $courierHumam->syncRoles([$courierRole]);

        $courierRafi = User::updateOrCreate(
            ['email' => 'rafi@mail.com'],
            [
                'name' => 'Rafi',
                'password' => Hash::make('password'),
            ]
        );
        $courierRafi->syncRoles([$courierRole]);

        $userUsamah = User::updateOrCreate(
            ['email' => 'usamah@mail.com'],
            [
                'name' => 'Usamah',
                'password' => Hash::make('password'),
            ]
        );
        $userUsamah->syncRoles([$userRole]);

        $userEllen = User::updateOrCreate(
            ['email' => 'ellen@mail.com'],
            [
                'name' => 'Ellen',
                'password' => Hash::make('password'),
            ]
        );
        $userEllen->syncRoles([$userRole]);

        $this->call([
            BankSeeder::class,
        ]);

        $jakartaSurabaya = Route::firstOrCreate(
            ['origin' => 'Jakarta', 'destination' => 'Surabaya'],
            ['is_active' => true]
        );

        $rateDarat1 = Rate::firstOrCreate(
            ['route_id' => $jakartaSurabaya->id, 'type' => 'darat'],
            ['price_per_kg' => 10000, 'estimated_days' => 3]
        );

        $ratePesawat1 = Rate::firstOrCreate(
            ['route_id' => $jakartaSurabaya->id, 'type' => 'pesawat'],
            ['price_per_kg' => 25000, 'estimated_days' => 1]
        );

        $baliJakarta = Route::firstOrCreate(
            ['origin' => 'Bali', 'destination' => 'Jakarta'],
            ['is_active' => true]
        );

        Rate::firstOrCreate(
            ['route_id' => $baliJakarta->id, 'type' => 'darat'],
            ['price_per_kg' => 15000, 'estimated_days' => 4]
        );

        Rate::firstOrCreate(
            ['route_id' => $baliJakarta->id, 'type' => 'pesawat'],
            ['price_per_kg' => 35000, 'estimated_days' => 2]
        );

        Shipment::create([
            'sender_id' => $userUsamah->id,
            'courier_id' => $courierHumam->id,
            'rate_id' => $rateDarat1->id,
            'receiver_name' => 'Usamah',
            'receiver_phone' => '081234567890',
            'receiver_address' => 'Jl. Darmo Permai No. 45, Surabaya',
            'actual_weight' => 3.5,
            'length' => 30,
            'width' => 20,
            'height' => 15,
            'chargeable_weight' => 3.5,
            'total_shipping_fee' => 35000,
            'status' => 'delivered',
        ]);

        Shipment::create([
            'sender_id' => $userEllen->id,
            'courier_id' => $courierRafi->id,
            'rate_id' => $ratePesawat1->id,
            'receiver_name' => 'Ellen',
            'receiver_phone' => '082345678901',
            'receiver_address' => 'Jl. Tunjungan No. 10, Surabaya',
            'actual_weight' => 1.2,
            'length' => 25,
            'width' => 15,
            'height' => 10,
            'chargeable_weight' => 1.2,
            'total_shipping_fee' => 30000,
            'status' => 'in_transit',
        ]);

        Shipment::create([
            'sender_id' => $userUsamah->id,
            'rate_id' => $ratePesawat1->id,
            'receiver_name' => 'Usamah',
            'receiver_phone' => '083456789012',
            'receiver_address' => 'Jl. Raya Kuta No. 88, Bali',
            'actual_weight' => 5.0,
            'length' => 40,
            'width' => 30,
            'height' => 25,
            'chargeable_weight' => 5.0,
            'total_shipping_fee' => 125000,
            'status' => 'pending',
        ]);
    }
}
