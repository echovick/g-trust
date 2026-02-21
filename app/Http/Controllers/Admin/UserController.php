<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class UserController extends AdminController
{
    public function index(Request $request)
    {
        $users = User::query()
            ->with('accounts')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('admin/users/Index', [
            'users' => $users,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        return Inertia::render('admin/users/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'is_admin' => ['boolean'],
            'preferred_currency' => ['required', 'string', 'in:USD,EUR,GBP,NGN'],
            'create_account' => ['boolean'],
            'account_type' => ['required_if:create_account,true', 'string', 'in:savings,checking,business'],
            'account_currency' => ['required_if:create_account,true', 'string', 'in:USD,EUR,GBP,NGN'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_admin' => $validated['is_admin'] ?? false,
            'preferred_currency' => $validated['preferred_currency'],
            'email_verified_at' => now(),
        ]);

        if ($validated['create_account'] ?? false) {
            Account::create([
                'user_id' => $user->id,
                'account_number' => $this->generateAccountNumber(),
                'account_name' => $user->name,
                'account_type' => $validated['account_type'],
                'currency' => $validated['account_currency'],
                'balance' => 0,
                'available_balance' => 0,
                'is_active' => true,
                'is_primary' => true,
            ]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $user->load(['accounts', 'loans', 'investments']);

        return Inertia::render('admin/users/Show', [
            'user' => $user,
        ]);
    }

    public function resetPassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password reset successfully.');
    }

    private function generateAccountNumber(): string
    {
        do {
            $accountNumber = str_pad(random_int(1000000000, 9999999999), 10, '0', STR_PAD_LEFT);
        } while (Account::where('account_number', $accountNumber)->exists());

        return $accountNumber;
    }
}
