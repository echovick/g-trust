<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Mail\WelcomeEmail;
use App\Models\Account;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
        ])->validate();

        return DB::transaction(function () use ($input) {
            // Create the user
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => $input['password'],
            ]);

            // Create default checking account
            $account = $this->createDefaultAccount($user);

            // Send welcome email with account details
            Mail::to($user->email)->send(new WelcomeEmail($user, $account));

            return $user;
        });
    }

    /**
     * Create a default checking account for the new user.
     */
    private function createDefaultAccount(User $user): Account
    {
        // Generate unique account number
        do {
            $accountNumber = 'ACC' . str_pad(random_int(1000000000, 9999999999), 10, '0', STR_PAD_LEFT);
        } while (Account::where('account_number', $accountNumber)->exists());

        return Account::create([
            'user_id' => $user->id,
            'account_number' => $accountNumber,
            'account_name' => $user->name . ' Checking Account',
            'account_type' => 'checking',
            'currency' => $user->preferred_currency ?? 'USD',
            'balance' => 0.00,
            'available_balance' => 0.00,
            'is_active' => true,
            'is_primary' => true,
        ]);
    }
}
