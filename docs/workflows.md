# Business Workflows

## 1. Shipment Creation Flow

**Actors:** Super Admin, User (sender)

**Steps:**

1. **Navigate** to Shipments → Create Shipment
2. **Step 1 — Fill Details:**
   - Select sender (auto-set to current user for non-admin)
   - Select route/rate from dropdown (shows origin → destination, type, and price/kg)
   - Enter receiver details (name, phone, address)
   - Enter package dimensions and actual weight
   - Fee calculation runs **live** on every keystroke:
     - `volumetric_weight = (length × width × height) / 6000`
     - `chargeable_weight = max(actual_weight, volumetric_weight)`
     - `total_shipping_fee = chargeable_weight × price_per_kg`
3. **Step 2 — Payment:**
   - View payment instructions generated from active bank records (name, account number, QRIS)
   - Upload payment proof image
4. **Step 3 — Review:**
   - Verify all entered data
   - Submit

**Auto-created on submission:**
- Tracking number: `ID-YYYYMMDDXXXX`
- Payment record: `amount = total_shipping_fee`, `is_paid = false`

## 2. Payment Approval Workflow

**Actors:** Super Admin

**Steps:**

1. Admin views shipment list or payment list
2. Finds a shipment with "Unpaid" or "Proof Uploaded" status
3. Clicks **Approve Payment** action in either:
   - Shipment table row action
   - Payment table row action
4. System executes in a database transaction:
   - Sets `payment.is_paid = true`
   - Sets `shipment.status = 'picked_up'`

**Result:** Shipment transitions from `pending` to `picked_up`. Courier can now be assigned and begin delivery.

## 3. Shipment Status Update & Delivery Flow

**Actors:** Super Admin, Courier (assigned)

**Steps:**

1. After payment approval, shipment is `picked_up`
2. Courier (or admin) can update status via the **Update Status** action on the shipment table
3. A modal opens with:
   - Status select (`pending`, `picked_up`, `in_transit`, `delivered`)
   - Delivery proof file upload (only visible/required when status is `delivered`)
4. On status change to `delivered`, a delivery proof photo is required

**Status Lifecycle:**
```
pending → picked_up → in_transit → delivered
```
- `pending`: Awaiting payment approval (auto-set on creation)
- `picked_up`: Payment approved, package is with courier
- `in_transit`: Package en route to destination
- `delivered`: Package received, delivery proof uploaded

## 4. Role-Based Data Access

| Action | super_admin | courier | user |
|---|---|---|---|
| View all shipments | ✅ | ❌ | ❌ |
| View own shipments | ✅ | ✅ (assigned) | ✅ (sent) |
| Create shipment | ✅ | ❌ | ✅ |
| Edit shipment | ✅ | ✅ (assigned) | ❌ |
| Assign courier | ✅ | ❌ | ❌ |
| Approve payment | ✅ | ❌ | ❌ |
| Update shipment status | ✅ | ✅ (assigned) | ❌ |
| Manage routes | ✅ | ❌ | ❌ |
| Manage rates | ✅ | ❌ | ❌ |
| Manage banks | ✅ | ❌ | ❌ |
| Manage users | ✅ | ❌ | ❌ |
| View payments | ✅ | ❌ | ✅ (own) |
| Upload payment proof | ❌ | ❌ | ✅ |

## 5. Fee Calculation Logic

The `calculateFees()` method (defined in both `ShipmentResource` and `ShipmentForm`) is called reactively via `->afterStateUpdated()` on dimension/weight fields and the rate selector.

```
volumetricWeight = (length × width × height) / 6000
chargeableWeight = max(actualWeight, volumetricWeight)
totalFee = chargeableWeight × rate.pricePerKg
```

All values are rounded to 2 decimal places. Fee fields are disabled (read-only) and dehydrated so they're saved to the database.

## 6. Payment Instructions Display

When a user reaches Step 2 of shipment creation, active banks are rendered as an HTML card grid showing:

- Bank logo image
- Bank name
- Account number (in `<code>` tag)
- Account holder name
- QRIS code image (if available)
- Total payment due amount at the top
