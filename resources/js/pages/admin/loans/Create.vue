<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { ArrowLeft, Banknote } from 'lucide-vue-next';

interface User {
    id: number;
    name: string;
    email: string;
}

interface Account {
    id: number;
    account_number: string;
    account_name: string;
    balance: number;
    available_balance: number;
    currency: string;
    user: {
        id: number;
        name: string;
        email: string;
    };
}

interface Props {
    users: User[];
    accounts: Account[];
}

const props = defineProps<Props>();

const form = useForm({
    user_id: null as number | null,
    loan_type: 'personal' as string,
    loan_amount: 0,
    interest_rate: 0,
    term_months: 12,
    currency: 'USD',
    linked_account_id: null as number | null,
    auto_pay: false,
});

const submit = () => {
    form.post('/admin/loans', {
        preserveScroll: true,
    });
};

const formatCurrency = (amount: number, currency: string) => {
    return `${currency} ${amount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
};
</script>

<template>
    <Head title="Create Loan" />

    <AdminLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div>
                <Link href="/admin/loans" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4">
                    <ArrowLeft class="h-4 w-4 mr-1" />
                    Back to Loans
                </Link>
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Create New Loan</h1>
                        <p class="mt-1 text-sm text-gray-500">Create a new loan for a user</p>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white shadow rounded-lg">
                <form @submit.prevent="submit" class="p-6 space-y-6">
                    <!-- Borrower Selection -->
                    <div>
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Borrower Information</h2>
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Borrower *</label>
                                <select
                                    v-model.number="form.user_id"
                                    required
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm rounded-md"
                                >
                                    <option :value="null" disabled>Select user...</option>
                                    <option v-for="user in users" :key="user.id" :value="user.id">
                                        {{ user.name }} ({{ user.email }})
                                    </option>
                                </select>
                                <p v-if="form.errors.user_id" class="mt-1 text-sm text-red-600">{{ form.errors.user_id }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Loan Details -->
                    <div class="border-t border-gray-200 pt-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Loan Details</h2>
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Loan Type *</label>
                                <select
                                    v-model="form.loan_type"
                                    required
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm rounded-md"
                                >
                                    <option value="personal">Personal</option>
                                    <option value="mortgage">Mortgage</option>
                                    <option value="auto">Auto</option>
                                    <option value="business">Business</option>
                                    <option value="student">Student</option>
                                </select>
                                <p v-if="form.errors.loan_type" class="mt-1 text-sm text-red-600">{{ form.errors.loan_type }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Currency *</label>
                                <select
                                    v-model="form.currency"
                                    required
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm rounded-md"
                                >
                                    <option value="USD">USD</option>
                                    <option value="EUR">EUR</option>
                                    <option value="GBP">GBP</option>
                                </select>
                                <p v-if="form.errors.currency" class="mt-1 text-sm text-red-600">{{ form.errors.currency }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Loan Amount *</label>
                                <input
                                    v-model.number="form.loan_amount"
                                    type="number"
                                    step="0.01"
                                    min="100"
                                    required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                                    placeholder="0.00"
                                />
                                <p v-if="form.errors.loan_amount" class="mt-1 text-sm text-red-600">{{ form.errors.loan_amount }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Interest Rate (%) *</label>
                                <input
                                    v-model.number="form.interest_rate"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    max="100"
                                    required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                                    placeholder="0.00"
                                />
                                <p v-if="form.errors.interest_rate" class="mt-1 text-sm text-red-600">{{ form.errors.interest_rate }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Term (Months) *</label>
                                <input
                                    v-model.number="form.term_months"
                                    type="number"
                                    min="1"
                                    max="360"
                                    required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                                    placeholder="12"
                                />
                                <p v-if="form.errors.term_months" class="mt-1 text-sm text-red-600">{{ form.errors.term_months }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Account Linking -->
                    <div class="border-t border-gray-200 pt-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Account Settings</h2>
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Linked Account (Optional)</label>
                                <select
                                    v-model.number="form.linked_account_id"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm rounded-md"
                                >
                                    <option :value="null">No linked account</option>
                                    <option v-for="account in accounts" :key="account.id" :value="account.id">
                                        {{ account.account_number }} - {{ account.account_name }} ({{ formatCurrency(account.available_balance, account.currency) }})
                                    </option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Account for disbursement and auto-pay (if enabled)</p>
                                <p v-if="form.errors.linked_account_id" class="mt-1 text-sm text-red-600">{{ form.errors.linked_account_id }}</p>
                            </div>

                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input
                                        v-model="form.auto_pay"
                                        type="checkbox"
                                        class="focus:ring-orange-500 h-4 w-4 text-orange-600 border-gray-300 rounded"
                                    />
                                </div>
                                <div class="ml-3 text-sm">
                                    <label class="font-medium text-gray-700">Enable Auto-Pay</label>
                                    <p class="text-gray-500">Automatically deduct monthly payments from the linked account</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200">
                        <Link
                            href="/admin/loans"
                            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 disabled:opacity-50"
                        >
                            <Banknote class="h-4 w-4 mr-2" />
                            Create Loan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
