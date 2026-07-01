# Roles & Permissions

## Overview

LogiTrack uses Spatie Laravel Permission with Filament Shield for role-based access control.

## Roles

| Role | Description |
|---|---|
| `super_admin` | Full system access. Can manage routes, rates, shipments, payments, banks, and users. Bypasses all individual permission checks via a global Gate `before` hook. |
| `courier` | Delivery personnel. Can only view and update shipments assigned to them. Cannot create shipments or manage master data. |
| `user` | Customer/sender. Can create shipments, view their own shipments, upload payment proofs, and view routes/rates. |

## Permissions

Defined in `DatabaseSeeder.php`:

| Permission | Description |
|---|---|
| `ViewAny:Route` | List all routes |
| `View:Route` | View a specific route |
| `ViewAny:Rate` | List all rates |
| `View:Rate` | View a specific rate |
| `ViewAny:Shipment` | List all shipments |
| `View:Shipment` | View a specific shipment |
| `Create:Shipment` | Create new shipments |
| `Update:Shipment` | Update existing shipments |
| `ViewAny:Payment` | List all payments |
| `View:Payment` | View a specific payment |

### Role-Permission Mapping

```
super_admin:
  [All permissions — granted via Gate::before bypass]

courier:
  - ViewAny:Shipment
  - View:Shipment
  - Update:Shipment

user:
  - ViewAny:Route
  - View:Route
  - ViewAny:Rate
  - View:Rate
  - ViewAny:Shipment
  - View:Shipment
  - Create:Shipment
  - ViewAny:Payment
  - View:Payment
```

## Global Authorization

`AppServiceProvider.php:23` registers a Gate `before` hook:

```php
Gate::before(fn ($user, $ability) => $user->hasRole('super_admin') ? true : null);
```

This grants `super_admin` access to all abilities without needing explicit permission assignments. Returning `null` allows individual policies to decide for other roles.

## Query-Level Scoping

Beyond policy checks, each resource overrides `getEloquentQuery()` to scope records:

### ShipmentResource

```php
super_admin → all shipments
courier     → shipments where courier_id = auth()->id()
user        → shipments where sender_id = auth()->id()
```

### PaymentResource

```php
super_admin → all payments
user        → payments for shipments where sender_id = auth()->id()
courier     → navigation hidden, no access
```

## Policies

| Policy | Implementation |
|---|---|
| BankPolicy | All methods: `super_admin` only (explicit role check, not Shield) |
| PaymentPolicy | Delegates to Shield permission checks |
| RatePolicy | Delegates to Shield permission checks |
| RolePolicy | Delegates to Shield permission checks |
| RoutePolicy | Delegates to Shield permission checks |
| ShipmentPolicy | Delegates to Shield permission checks |
| UserPolicy | Delegates to Shield permission checks |

## Seeder Data

The `DatabaseSeeder` creates:
- 3 roles with their assigned permissions
- 3 users with matching roles

## Adding New Permissions

1. Create the permission via `Permission::firstOrCreate(['name' => 'PermissionName'])`
2. Assign to roles via `$role->givePermissionTo('PermissionName')`
3. Create or update the corresponding Policy method
4. Generate Shield permissions: `php artisan shield:generate --all`
