# Data Models & Relationships

## Entity Relationship Diagram

```
User (sender) ──┐
                ├──< Shipment >── Rate ──> Route
User (courier) ─┘     │
                      └── Payment
```

## Models

### Route

Shipping origin-destination paths.

| Column | Type | Description |
|---|---|---|
| id | bigint, PK | Auto-increment |
| origin | string | Origin city/region |
| destination | string | Destination city/region |
| is_active | boolean | Whether this route is available (default: true) |
| created_at | timestamp | |
| updated_at | timestamp | |

*Unique constraint: (origin, destination)*

**Relationships:**
- `hasMany(Rate)` — rates available for this route

### Rate

Shipping pricing tiers per route.

| Column | Type | Description |
|---|---|---|
| id | bigint, PK | Auto-increment |
| route_id | bigint, FK → routes | Linked route |
| type | string | Shipping type (`darat`/`pesawat`/`kapal`) |
| price_per_kg | decimal(12,2) | Price per kilogram |
| estimated_days | integer | Estimated delivery days |
| created_at | timestamp | |
| updated_at | timestamp | |

*Unique constraint: (route_id, type)*

**Relationships:**
- `belongsTo(Route)` — parent route
- `hasMany(Shipment)` — shipments using this rate

### Shipment

Core entity representing a shipping order.

| Column | Type | Description |
|---|---|---|
| id | bigint, PK | Auto-increment |
| tracking_number | string | Auto-generated (`ID-YYYYMMDDXXXX`) |
| sender_id | bigint, FK → users | Sender/user who created the shipment |
| courier_id | bigint, FK → users, nullable | Assigned courier |
| rate_id | bigint, FK → rates | Selected shipping rate |
| receiver_name | string | Receiver's full name |
| receiver_phone | string | Receiver's phone number |
| receiver_address | text | Receiver's address |
| actual_weight | decimal(8,2) | Actual package weight (kg) |
| length | decimal(8,2) | Package length (cm) |
| width | decimal(8,2) | Package width (cm) |
| height | decimal(8,2) | Package height (cm) |
| chargeable_weight | decimal(8,2) | Max of actual vs volumetric weight |
| total_shipping_fee | decimal(12,2) | `chargeable_weight × price_per_kg` |
| status | string | `pending`, `picked_up`, `in_transit`, `delivered` |
| delivery_proof | string, nullable | Path to delivery proof image |
| created_at | timestamp | |
| updated_at | timestamp | |

**Relationships:**
- `belongsTo(sender)` — User who created the shipment
- `belongsTo(courier)` — User assigned as courier
- `belongsTo(rate)` — Selected shipping rate
- `hasOne(payment)` — Associated payment record

**Auto-generated values** (via `Shipment::boot()`):
- `creating`: Generates `tracking_number` as `ID-YYYYMMDDXXXX` (4 random uppercase alphanumeric chars)
- `created`: Automatically creates a `Payment` record with `amount = total_shipping_fee`, `is_paid = false`, `proof = ''`

### Payment

Payment record linked to a shipment.

| Column | Type | Description |
|---|---|---|
| id | bigint, PK | Auto-increment |
| shipment_id | bigint, FK → shipments | Associated shipment |
| amount | decimal(12,2) | Payment amount |
| proof | string | Path to payment proof image |
| is_paid | boolean | Payment confirmation status |
| created_at | timestamp | |
| updated_at | timestamp | |

**Relationships:**
- `belongsTo(Shipment)` — parent shipment

**Casts:**
- `is_paid` → boolean

### Bank

Bank accounts displayed as payment instructions during checkout.

| Column | Type | Description |
|---|---|---|
| id | bigint, PK | Auto-increment |
| bank_name | string | Bank name |
| bank_logo | string, nullable | Path to bank logo image |
| bank_no | string, nullable | Bank account number |
| account_name | string, nullable | Account holder name |
| qris_image | string, nullable | Path to QRIS code image |
| is_active | boolean | Whether this bank is shown (default: true) |
| created_at | timestamp | |
| updated_at | timestamp | |

**Relationships:** None (standalone resource)

**Casts:**
- `is_active` → boolean

### User

Extended Laravel Authenticatable with Spatie roles.

| Column | Type | Description |
|---|---|---|
| id | bigint, PK | Auto-increment |
| name | string | |
| email | string | |
| password | string | |
| email_verified_at | timestamp, nullable | |
| remember_token | string, nullable | |
| created_at | timestamp | |
| updated_at | timestamp | |

**Traits:** `HasFactory`, `Notifiable`, `HasRoles` (Spatie)

## Migration Summary

| # | Migration | Tables |
|---|---|---|
| 1 | `0001_01_01_000000` | users, password_reset_tokens, sessions |
| 2 | `0001_01_01_000001` | cache, cache_locks |
| 3 | `0001_01_01_000002` | jobs, job_batches, failed_jobs |
| 4 | `2026_06_19_140708` | routes |
| 5 | `2026_06_19_141047` | rates |
| 6 | `2026_06_19_141259` | shipments |
| 7 | `2026_06_19_143953` | payments |
| 8 | `2026_06_19_153811` | Spatie permission tables |
| 9 | `2026_06_19_232734` | Add delivery_proof to shipments |
| 10 | `2026_06_19_235259` | banks |
| 11 | `2026_06_19_235311` | Change receiver_address to text |
