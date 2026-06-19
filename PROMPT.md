Act as an expert Senior Laravel & Filament Developer. I am building a logistics app called "LogiTrack" using Laravel v13, Filament v5, and Laravel Boost with MCP enabled.

The application uses Filament Shield for Roles & Permissions management (Roles: Admin, Sender/Client, Courier), so you do not need to generate role logic in migrations. The core business relies on fixed A-to-B route-based shipping with dynamic pricing, volumetric package calculations, and manual bank transfer verification.

I have already created the migrations and models for:

1. Route (origin, destination, is_active)
2. Rate (route_id, type [pesawat/kapal], price_per_kg, estimated_days)
3. Shipment (tracking_number, sender_id, courier_id, rate_id, receiver_name/phone/address, actual_weight, length, width, height, chargeable_weight, total_shipping_fee, status [pending, picked_up, in_transit, delivered])
4. Payment (shipment_id, amount, proof, is_paid [boolean])

Please generate and complete the entire Filament v5 backend architecture. Write clean, complete, and production-ready code for the following components:

### 1. Filament Resources & Pages

Create the fully implemented Filament Resources, including Form schemas, Table definitions, Filters, and Actions:

- **RouteResource**: Simple CRUD for paths.
- **RateResource**: Manage shipping rates linked to Routes with a Select dropdown.
- **ShipmentResource**:
    - Form must use modern Filament v5 reactive state management (`reactive()` or `afterStateUpdated()`) to automatically calculate volumetric weight when `length`, `width`, `height`, or `actual_weight` changes.
    - Volumetric formula: (Length _ Width _ Height) / 6000.
    - `chargeable_weight` must be the maximum value between `actual_weight` and the volumetric weight.
    - `total_shipping_fee` must be automatically calculated by multiplying `chargeable_weight` with the selected `Rate`'s `price_per_kg`.
    - Automatically create a corresponding `Payment` record with `is_paid = false` upon Shipment creation using Filament's lifecycle hooks or a clean database observer.
- **PaymentResource**:
    - Display payment details, a file upload preview for the `proof` column (image format), and the `is_paid` toggle/badge status.
    - Create a custom Filament Header/Table Action named "Approve Payment". When clicked, it must set `is_paid = true` and transition the associated Shipment's status from `pending` to `picked_up` or the next logical shipping step.

### 2. Form & Table Optimizations (Filament v5 Standards)

- Use appropriate field types (`TextInput::make()->numeric()`, `FileUpload::make()`, `Select::make()`).
- Implement proper table columns (`TextColumn`, `ImageColumn` for the proof, `IconColumn` or `BadgeColumn` for boolean/status states).
- Add filters for Shipment statuses and Payment `is_paid` conditions.

### 3. Scopes & Shield Integration

Ensure that:

- Admins can see and manage everything.
- Senders (Clients) can only view/create their own Shipments and upload their payment proofs.
- Couriers can only view Shipments assigned to them (`courier_id`) and update the shipment status (e.g., changing status to `in_transit` or `delivered`).
  Implement this logic cleanly inside the Resource queries (`getEloquentQuery()`) or via policies conforming to Filament Shield v5 standards.

Please output the complete PHP files for the Resources, Pages, and any additional Service classes or Observers needed to make this system run flawlessly. Do not truncate the code.
