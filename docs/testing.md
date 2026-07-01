# Testing

LogiTrack uses **Pest PHP 4** for testing with SQLite in-memory database for test isolation.

## Test Configuration

- **Framework:** Pest PHP 4 + `pest-plugin-laravel`
- **PHPUnit config:** `phpunit.xml` — SQLite in-memory, array cache, sync queue
- **Base test case:** `tests/TestCase.php`

## Running Tests

```bash
# Run all tests
php artisan test --compact

# Run specific test file
php artisan test --compact --filter=ShipmentCalculationTest

# Run a specific test method
php artisan test --compact --filter=testName
```

## Test Structure

```
tests/
├── Feature/
│   ├── ExampleTest.php                   # Smoke test: / returns 200
│   └── ShipmentCalculationTest.php       # Core business logic tests
├── Unit/
│   └── ExampleTest.php                   # Basic unit test
├── Pest.php                              # Pest configuration
└── TestCase.php                          # Base test case
```

## Existing Tests

### ShipmentCalculationTest

Uses `RefreshDatabase` trait for clean state per test.

#### Test: Tracking Number & Auto Payment Creation

Tests that:
- A shipment's `tracking_number` starts with `ID-`
- A `Payment` record is automatically created on shipment creation
- The payment `is_paid` defaults to `false`
- The payment `amount` matches `total_shipping_fee`

Uses volumetric calculation assertion: `(50 × 40 × 30) / 6000 = 10 kg` chargeable weight, `10 × 10000 = 100000` fee.

#### Test: Payment Approval Transitions Shipment

Tests that:
- Approving payment (`is_paid = true`) transitions the shipment status from `pending` to `picked_up`

## Writing Tests

### Creating a New Test

```bash
php artisan make:test --pest SomeFeatureTest
```

### Test Conventions

- Use `uses(RefreshDatabase::class)` for feature tests that modify the database
- Use `$this->actingAs(User::factory()->create())` before testing Filament panel pages
- For Filament resource tests, use `livewire()` with proper page classes
- Use descriptive test names in snake_case

### Example: Filament Resource Test

```php
use function Pest\Livewire\livewire;

test('super_admin can create shipment', function () {
    $admin = User::factory()->create()->assignRole('super_admin');
    $this->actingAs($admin);

    livewire(CreateShipment::class)
        ->fillForm([
            'receiver_name' => 'Test',
            'receiver_phone' => '08123456789',
            'receiver_address' => 'Test Address',
            'actual_weight' => 5,
            'length' => 50,
            'width' => 40,
            'height' => 30,
        ])
        ->call('create')
        ->assertNotified()
        ->assertHasNoFormErrors();
});
```

## Fixtures

### User Factory (`database/factories/UserFactory.php`)

Standard Laravel User factory with:
- Password pre-hashed
- `unverified` state for unverified email users

### Bank Factory (`database/factories/BankFactory.php`)

Defined but empty (no default attributes).

## Seeded Test Data

Run `php artisan db:seed --class=DatabaseSeeder` to populate:
- 3 roles
- 8 permissions
- 3 users (admin/kurir/user each with role)
- 2 routes with rates
- 2 banks
