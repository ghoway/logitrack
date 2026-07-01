# Architecture

## Tech Stack

| Component | Technology | Version |
|---|---|---|
| Backend | PHP | 8.4 |
| Framework | Laravel | 13 |
| Admin Panel | Filament | 5 |
| Component Engine | Livewire | 4 |
| CSS | Tailwind CSS | 4 |
| Database | SQLite (default), MySQL, MariaDB, PostgreSQL, SQL Server | - |
| Testing | Pest PHP | 4 |
| Formatter | Laravel Pint | 1 |
| Logging | Laravel Pail | 1 |

## Directory Structure

```
G:\logitrack/
├── app/
│   ├── Filament/
│   │   └── Resources/
│   │       ├── Banks/          # Payment bank accounts CRUD
│   │       │   ├── BankResource.php
│   │       │   ├── Pages/ManageBanks.php
│   │       │   ├── Schemas/BankForm.php
│   │       │   └── Tables/BanksTable.php
│   │       ├── Payments/       # Payment management
│   │       │   ├── PaymentResource.php
│   │       │   ├── Pages/{ListPayments,CreatePayment,EditPayment}.php
│   │       │   ├── Schemas/PaymentForm.php
│   │       │   └── Tables/PaymentsTable.php
│   │       ├── Rates/          # Shipping rates CRUD
│   │       │   ├── RateResource.php
│   │       │   └── Pages/ManageRates.php
│   │       ├── Routes/         # Shipping routes CRUD
│   │       │   ├── RouteResource.php
│   │       │   └── Pages/ManageRoutes.php
│   │       ├── Shipments/      # Shipment management (wizard form)
│   │       │   ├── ShipmentResource.php
│   │       │   ├── Pages/{ListShipments,CreateShipment,EditShipment}.php
│   │       │   ├── Schemas/ShipmentForm.php
│   │       │   └── Tables/ShipmentsTable.php
│   │       └── Users/          # User management
│   │           ├── UserResource.php
│   │           └── Pages/ManageUsers.php
│   ├── Http/Controllers/       # Controllers (minimal — Filament handles routing)
│   ├── Models/
│   │   ├── Bank.php
│   │   ├── Payment.php
│   │   ├── Rate.php
│   │   ├── Route.php
│   │   ├── Shipment.php
│   │   └── User.php
│   ├── Policies/               # Authorization policies
│   │   ├── BankPolicy.php
│   │   ├── PaymentPolicy.php
│   │   ├── RatePolicy.php
│   │   ├── RolePolicy.php
│   │   ├── RoutePolicy.php
│   │   ├── ShipmentPolicy.php
│   │   └── UserPolicy.php
│   └── Providers/
│       ├── AppServiceProvider.php
│       └── Filament/AdminPanelProvider.php
├── config/                     # Laravel configuration files
├── database/
│   ├── factories/              # Model factories
│   ├── migrations/             # Database migrations (11 files)
│   └── seeders/                # Database seeders
├── docs/                       # Project documentation
├── resources/
│   ├── css/app.css             # Tailwind CSS v4 entry
│   ├── js/app.js               # JavaScript entry
│   └── views/                  # Blade templates
├── routes/
│   ├── web.php                 # Web routes (GET / -> welcome)
│   └── console.php             # Artisan commands
└── tests/
    ├── Feature/                # Feature tests
    ├── Unit/                   # Unit tests
    ├── Pest.php                # Pest configuration
    └── TestCase.php            # Base test case
```

## Architecture Decisions

### Filament-first Architecture

All admin functionality is built entirely through Filament resources, forms, and tables. There are no custom controllers, Livewire components, or Blade views for the admin panel. Filament auto-discovers resources via `AdminPanelProvider.php`.

### Role-based Query Scoping

Data access is scoped at the Eloquent query level via `getEloquentQuery()` overrides in each resource. This ensures role-based filtering is applied globally, including to relationship queries and exports.

### Global Gate for Super Admin

`AppServiceProvider.php:23` registers a `Gate::before` hook that grants `super_admin` users access to all abilities, bypassing individual policy checks.

### Wizard Form for Shipment Creation

Shipment creation uses a 3-step Wizard:
1. **Shipment & Package Details** — sender, route/rate, receiver info, package dimensions (with live fee calculation)
2. **Payment & Upload Proof** — displays payment instructions from active banks, accepts proof upload
3. **Review & Submit** — confirmation message

### Volumetric Weight Calculation

Shipping fees are calculated client-side (reactive) using the formula:
- `volumetric_weight = (length × width × height) / 6000`
- `chargeable_weight = max(actual_weight, volumetric_weight)`
- `total_shipping_fee = chargeable_weight × price_per_kg`
