<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Account;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@gtrust.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'preferred_currency' => 'USD',
                'email_verified_at' => now(),
            ]
        );

        // Create a primary account for admin
        if ($admin->accounts()->count() === 0) {
            Account::create([
                'user_id' => $admin->id,
                'account_number' => $this->generateAccountNumber(),
                'account_name' => $admin->name,
                'account_type' => 'checking',
                'currency' => 'USD',
                'balance' => 10000.00,
                'available_balance' => 10000.00,
                'is_active' => true,
                'is_primary' => true,
            ]);
        }

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@gtrust.com');
        $this->command->info('Password: password');
    }

    private function generateAccountNumber(): string
    {
        do {
            $accountNumber = str_pad(random_int(1000000000, 9999999999), 10, '0', STR_PAD_LEFT);
        } while (Account::where('account_number', $accountNumber)->exists());

        return $accountNumber;
    }
}
