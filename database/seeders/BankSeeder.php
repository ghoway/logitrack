<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Bank::create([
            'bank_name' => 'Bank BCA',
            'bank_no' => '1234567890',
            'account_name' => 'PT LogiTrack Indonesia',
            'is_active' => true,
        ]);

        Bank::create([
            'bank_name' => 'Bank Mandiri',
            'bank_no' => '9876543210',
            'account_name' => 'PT LogiTrack Indonesia',
            'is_active' => true,
        ]);
    }
}
