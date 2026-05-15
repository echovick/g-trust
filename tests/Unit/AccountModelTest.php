<?php

use App\Models\Account;

it('builds the account name from user name and account type', function () {
    expect(Account::buildAccountName('John Doe', 'checking'))->toBe('John Doe Checking Account');
    expect(Account::buildAccountName('Jane Smith', 'savings'))->toBe('Jane Smith Savings Account');
    expect(Account::buildAccountName('Acme Co.', 'business'))->toBe('Acme Co. Business Account');
});
