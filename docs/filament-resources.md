# Filament Resources

## Overview

LogiTrack uses Filament v5 resource classes with a modular structure. Each resource is organized in its own subdirectory under `app/Filament/Resources/` with separate files for forms, tables, and pages.

## Resources

### RouteResource

| Property | Value |
|---|---|
| Namespace | `App\Filament\Resources\Routes` |
| Model | `App\Models\Route` |
| Navigation Icon | `Heroicon::OutlinedMap` |
| Navigation Group | Master Data |
| Visibility | `super_admin` only |

**Form Fields:**
- `origin` (TextInput, required)
- `destination` (TextInput, required)
- `is_active` (Toggle, default: true)

**Table Columns:**
- `origin` (TextColumn, searchable)
- `destination` (TextColumn, searchable)
- `is_active` (ToggleColumn — inline toggle)
- `created_at` / `updated_at` (togglable by default)

**Actions:** View, Edit, Delete, Bulk Delete

**Pages:** ManageRoutes (single-page CRUD with modals)

### RateResource

| Property | Value |
|---|---|
| Namespace | `App\Filament\Resources\Rates` |
| Model | `App\Models\Rate` |
| Navigation Icon | `Heroicon::OutlinedCurrencyDollar` |
| Navigation Group | Master Data |
| Visibility | `super_admin` only |

**Form Fields:**
- `route_id` (Select, relationship to Route, filtered to active routes, searchable by origin/destination)
- `type` (Select: `pesawat`/`kapal`)
- `price_per_kg` (TextInput, numeric, prefix `Rp`)
- `estimated_days` (TextInput, numeric, suffix `Days`)

**Table Columns:**
- `route` (custom state: `"{origin} -> {destination}"`, searchable through relationship)
- `type` (badge: info for pesawat, success for kapal)
- `price_per_kg` (formatted as `Rp X.XXX`)
- `estimated_days` (suffixed `days`)

**Actions:** Edit, Delete, Bulk Delete

**Pages:** ManageRates (single-page CRUD with modals)

### ShipmentResource

| Property | Value |
|---|---|
| Namespace | `App\Filament\Resources\Shipments` |
| Model | `App\Models\Shipment` |
| Navigation Icon | `Heroicon::OutlinedTruck` |
| Navigation Group | Shipments & Payments |

**Query Scoping** (`getEloquentQuery()`):
- `super_admin`: All shipments
- `courier`: Shipments where `courier_id = auth()->id()`
- `user`: Shipments where `sender_id = auth()->id()`

#### Create Form (Wizard)

3-step Wizard with live reactive calculations:

**Step 1 — Shipment & Package Details**
- `sender_id` (Select, relationship to User, disabled for non-super_admin)
- `rate_id` (Select with custom label showing route + type + price/kg, live, triggers fee calculation)
- *Receiver Details section:*
  - `receiver_name` (TextInput)
  - `receiver_phone` (TextInput, tel)
  - `receiver_address` (Textarea)
- *Package Specifications section:*
  - `actual_weight` (TextInput, numeric, live)
  - `length`, `width`, `height` (TextInput, numeric, default 0, live)
  - `chargeable_weight` (disabled, auto-calculated)
  - `total_shipping_fee` (disabled, auto-calculated, prefix `Rp`)

**Step 2 — Payment & Upload Proof**
- `payment_instructions` (Placeholder — renders HTML of active banks with logo, account details, and QRIS codes)
- `payment_proof` (FileUpload, image, directory: `payment-proofs`, public visibility)

**Step 3 — Review & Submit**
- Confirmation message placeholder
- On submit, the `Shipment::created` boot event auto-creates a Payment record

#### Edit Form

Uses `ShipmentForm::configure()` with sections:
- **Sender & Courier Details:** sender select (disabled for non-admin), courier select (admin-only, filtered to `courier` role)
- **Receiver Details:** receiver name/phone/address (disabled for non-admin/non-creator users)
- **Shipping Route & Status:** rate select (with fee recalculation), status select (disabled for `user` role), delivery proof upload (visible when status = `delivered`)
- **Package Specifications:** actual_weight, length, width, height (all live, recalculate fees)

#### Table

| Column | Details |
|---|---|
| tracking_number | TextColumn, searchable, sortable |
| sender.name | TextColumn, searchable, sortable |
| courier.name | TextColumn, placeholder "Unassigned" |
| rate | Custom state: `"{origin} -> {destination} ({type})"` |
| receiver_name | TextColumn, toggleable (hidden by default) |
| chargeable_weight | TextColumn, suffix "kg" |
| total_shipping_fee | TextColumn, formatted `Rp X.XXX` |
| status | Badge (gray/warning/info/success) |
| delivery_proof | ImageColumn, square, placeholder "No proof" |
| payment.is_paid | Badge (green "Paid" / yellow "Proof Uploaded" / red "Unpaid") |

**Filters:**
- `status` (SelectFilter: pending/picked_up/in_transit/delivered)
- `is_paid` (TernaryFilter: queries through payment relationship)

**Record Actions:**
- View (infolist)
- Edit (visible to super_admin or assigned courier)
- **Approve Payment** (super_admin only, visible when payment exists and unpaid): Sets `is_paid = true`, transitions shipment to `picked_up`
- **Update Status** (super_admin or assigned courier): Opens modal with status select and conditional delivery proof upload

**Infolist (View):**
- Shipment Details section: tracking_number, sender, courier, receiver info
- Package Info & Fees section: chargeable_weight, total_shipping_fee, status badge
- Delivery Proof section: visible only when status = `delivered`

### PaymentResource

| Property | Value |
|---|---|
| Namespace | `App\Filament\Resources\Payments` |
| Model | `App\Models\Payment` |
| Navigation Icon | `Heroicon::OutlinedCreditCard` |
| Navigation Group | Shipments & Payments |
| Registration | Only visible to `super_admin` and `user` roles |

**Query Scoping:**
- `super_admin`: All payments
- `user`: Payments for shipments where `sender_id = auth()->id()`
- `courier`: Not visible in navigation

**Form Fields:**
- `shipment_id` (Select, disabled, links to tracking number)
- `amount` (TextInput, numeric, prefix `Rp`, disabled for non-admin)
- `proof` (FileUpload, image, directory `payment-proofs`, required on create for non-admin)
- `is_paid` (Toggle, disabled for non-admin)

**Table Columns:**
- `shipment.tracking_number` (searchable)
- `shipment.sender.name` (searchable)
- `amount` (formatted `Rp X.XXX`)
- `proof` (ImageColumn, 50x50 thumbnail)
- `is_paid` (Badge: success/Paid, danger/Unpaid)
- `created_at` (dateTime, toggleable)

**Filters:**
- `is_paid` (TernaryFilter)

**Record Actions:**
- View
- Edit (visible to super_admin or user with unpaid payment)
- **Approve Payment** (super_admin, unpaid only, requires confirmation): Sets `is_paid = true`, transitions shipment to `picked_up`

### BankResource

| Property | Value |
|---|---|
| Namespace | `App\Filament\Resources\Banks` |
| Model | `App\Models\Bank` |
| Navigation Icon | `Heroicon::OutlinedBuildingOffice` |
| Navigation Group | Shipments & Payments |

**Policy:** Only `super_admin` can access all operations.

**Form Fields:**
- `bank_name` (TextInput, placeholder)
- `bank_no` (TextInput)
- `account_name` (TextInput)
- `bank_logo` (FileUpload, image, directory `banks`, public)
- `qris_image` (FileUpload, image, directory `banks`, public)
- `is_active` (Toggle, default: true)

**Table Columns:**
- `bank_logo` (ImageColumn, circular)
- `bank_name` (searchable)
- `bank_no` (searchable)
- `account_name` (searchable)
- `is_active` (IconColumn, boolean)
- `qris_image` (ImageColumn)

**Actions:** Edit, Bulk Delete

**Pages:** ManageBanks (single-page CRUD with modals)

### UserResource

| Property | Value |
|---|---|
| Namespace | `App\Filament\Resources\Users` |
| Model | `App\Models\User` |
| Navigation Icon | `Heroicon::OutlinedUserGroup` |
| Navigation Group | (none — top level) |

**Form Fields:**
- `name` (TextInput)
- `email` (TextInput, email)
- `roles` (Select, relationship to Spatie roles, required)
- `password` (TextInput, password, required only on create, auto-hashed)

**Table Columns:**
- `name` (searchable)
- `email` (searchable)
- `email_verified_at` (dateTime)
- `created_at` / `updated_at` (togglable)

**Actions:** View, Edit, Delete, Bulk Delete

**Pages:** ManageUsers (single-page CRUD with modals)
