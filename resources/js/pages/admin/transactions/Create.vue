<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { ArrowLeft, ArrowDownLeft, ArrowUpRight, AlertCircle, Check } from 'lucide-vue-next';

interface Account {
    id: number;
    account_number: string;
    account_name: string;
    currency: string;
    balance: number;
    available_balance: number;
    user_name: string;
    user_email: string;
}

interface Props {
    accounts: Account[];
}

const props = defineProps<Props>();

const form = useForm({
    account_id: '',
    transaction_type: 'credit',
    category: '',
    description: '',
    amount: 0,
    status: 'completed',
});

const selectedAccount = computed(() => {
    if (!form.account_id) return null;
    return props.accounts.find(a => a.id === parseInt(form.account_id as string));
});

const balanceAfterTransaction = computed(() => {
    if (!selectedAccount.value || !form.amount) return 0;

    if (form.status === 'pending') {
        return selectedAccount.value.balance; // Pending doesn't affect balance
    }

    if (form.transaction_type === 'credit') {
        return selectedAccount.value.balance + parseFloat(form.amount.toString());
    } else {
        return selectedAccount.value.balance - parseFloat(form.amount.toString());
    }
});

const hasInsufficientFunds = computed(() => {
    if (!selectedAccount.value || !form.amount || form.transaction_type !== 'debit' || form.status === 'pending') {
        return false;
    }
    return selectedAccount.value.available_balance < parseFloat(form.amount.toString());
});

const submitForm = () => {
    form.post('/admin/transactions', {
        onSuccess: () => {
            form.reset();
        },
    });
};

const formatCurrency = (amount: number, currency: string) => {
    return `${currency} ${amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
};

const transactionCategories = [
    'deposit',
    'withdrawal',
    'transfer',
    'payment',
    'refund',
    'fee',
    'interest',
    'adjustment',
    'other',
];
</script>

<template>
    <AdminLayout title="Create Transaction">
        <div class="space-y-6">
            <div class="flex items-center gap-4">
                <a
                    href="/admin/transactions"
                    class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
                >
                    <ArrowLeft :size="20" class="text-gray-600" />
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Create New Transaction</h1>
                    <p class="text-gray-600 mt-1">Manually create a transaction for an account</p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 max-w-3xl">
                <form @submit.prevent="submitForm" class="space-y-6">
                    <!-- Account Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Account *
                        </label>
                        <select
                            v-model="form.account_id"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-gray-900"
                        >
                            <option value="" disabled>Select an account...</option>
                            <option v-for="account in accounts" :key="account.id" :value="account.id">
                                {{ account.account_number }} - {{ account.user_name }} ({{ formatCurrency(account.available_balance, account.currency) }})
                            </option>
                        </select>
                        <p v-if="form.errors.account_id" class="mt-1 text-sm text-red-600">
                            {{ form.errors.account_id }}
                        </p>

                        <!-- Account Details Preview -->
                        <div v-if="selectedAccount" class="mt-3 p-4 bg-gray-50 rounded-lg">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-600">Account Holder</p>
                                    <p class="font-medium text-gray-900">{{ selectedAccount.user_name }}</p>
                                    <p class="text-sm text-gray-500">{{ selectedAccount.user_email }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600">Current Balance</p>
                                    <p class="font-bold text-gray-900">
                                        {{ formatCurrency(selectedAccount.balance, selectedAccount.currency) }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        Available: {{ formatCurrency(selectedAccount.available_balance, selectedAccount.currency) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Transaction Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Transaction Type *
                        </label>
                        <div class="grid grid-cols-2 gap-4">
                            <label
                                class="relative flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition-all hover:border-green-300"
                                :class="form.transaction_type === 'credit' ? 'border-green-500 bg-green-50' : 'border-gray-200'"
                            >
                                <input
                                    v-model="form.transaction_type"
                                    type="radio"
                                    value="credit"
                                    class="sr-only"
                                />
                                <div class="w-10 h-10 rounded-full flex items-center justify-center"
                                    :class="form.transaction_type === 'credit' ? 'bg-green-100' : 'bg-gray-100'"
                                >
                                    <ArrowDownLeft :size="20" :class="form.transaction_type === 'credit' ? 'text-green-600' : 'text-gray-500'" />
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">Credit</p>
                                    <p class="text-sm text-gray-600">Add money to account</p>
                                </div>
                                <div v-if="form.transaction_type === 'credit'" class="w-5 h-5 bg-green-500 rounded-full flex items-center justify-center">
                                    <Check :size="14" class="text-white" />
                                </div>
                            </label>

                            <label
                                class="relative flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition-all hover:border-red-300"
                                :class="form.transaction_type === 'debit' ? 'border-red-500 bg-red-50' : 'border-gray-200'"
                            >
                                <input
                                    v-model="form.transaction_type"
                                    type="radio"
                                    value="debit"
                                    class="sr-only"
                                />
                                <div class="w-10 h-10 rounded-full flex items-center justify-center"
                                    :class="form.transaction_type === 'debit' ? 'bg-red-100' : 'bg-gray-100'"
                                >
                                    <ArrowUpRight :size="20" :class="form.transaction_type === 'debit' ? 'text-red-600' : 'text-gray-500'" />
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">Debit</p>
                                    <p class="text-sm text-gray-600">Deduct money from account</p>
                                </div>
                                <div v-if="form.transaction_type === 'debit'" class="w-5 h-5 bg-red-500 rounded-full flex items-center justify-center">
                                    <Check :size="14" class="text-white" />
                                </div>
                            </label>
                        </div>
                        <p v-if="form.errors.transaction_type" class="mt-1 text-sm text-red-600">
                            {{ form.errors.transaction_type }}
                        </p>
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Category *
                        </label>
                        <select
                            v-model="form.category"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-gray-900 capitalize"
                        >
                            <option value="" disabled>Select a category...</option>
                            <option v-for="category in transactionCategories" :key="category" :value="category" class="capitalize">
                                {{ category }}
                            </option>
                        </select>
                        <p v-if="form.errors.category" class="mt-1 text-sm text-red-600">
                            {{ form.errors.category }}
                        </p>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Description *
                        </label>
                        <textarea
                            v-model="form.description"
                            required
                            rows="3"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-gray-900"
                            placeholder="e.g., Manual adjustment - Correction for duplicate charge"
                        ></textarea>
                        <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                            {{ form.errors.description }}
                        </p>
                    </div>

                    <!-- Amount -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Amount *
                        </label>
                        <input
                            v-model="form.amount"
                            type="number"
                            step="0.01"
                            min="0.01"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-gray-900"
                            placeholder="0.00"
                        />
                        <p v-if="form.errors.amount" class="mt-1 text-sm text-red-600">
                            {{ form.errors.amount }}
                        </p>

                        <!-- Insufficient Funds Warning -->
                        <div v-if="hasInsufficientFunds" class="mt-2 p-3 bg-red-50 border border-red-200 rounded-lg flex items-start gap-2">
                            <AlertCircle :size="16" class="text-red-600 mt-0.5 flex-shrink-0" />
                            <p class="text-sm text-red-800">
                                Insufficient funds! Available balance is {{ formatCurrency(selectedAccount!.available_balance, selectedAccount!.currency) }}.
                            </p>
                        </div>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Status *
                        </label>
                        <div class="grid grid-cols-2 gap-4">
                            <label
                                class="relative flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition-all"
                                :class="form.status === 'completed' ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-green-300'"
                            >
                                <input
                                    v-model="form.status"
                                    type="radio"
                                    value="completed"
                                    class="sr-only"
                                />
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">Completed</p>
                                    <p class="text-sm text-gray-600">Update account balance immediately</p>
                                </div>
                                <div v-if="form.status === 'completed'" class="w-5 h-5 bg-green-500 rounded-full flex items-center justify-center">
                                    <Check :size="14" class="text-white" />
                                </div>
                            </label>

                            <label
                                class="relative flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition-all"
                                :class="form.status === 'pending' ? 'border-yellow-500 bg-yellow-50' : 'border-gray-200 hover:border-yellow-300'"
                            >
                                <input
                                    v-model="form.status"
                                    type="radio"
                                    value="pending"
                                    class="sr-only"
                                />
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">Pending</p>
                                    <p class="text-sm text-gray-600">Requires approval before processing</p>
                                </div>
                                <div v-if="form.status === 'pending'" class="w-5 h-5 bg-yellow-500 rounded-full flex items-center justify-center">
                                    <Check :size="14" class="text-white" />
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Balance Preview -->
                    <div v-if="selectedAccount && form.amount > 0" class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-sm font-medium text-blue-900 mb-2">Transaction Preview</p>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-blue-700">Current Balance</p>
                                <p class="font-bold text-blue-900">
                                    {{ formatCurrency(selectedAccount.balance, selectedAccount.currency) }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-blue-700">Balance After {{ form.status === 'pending' ? '(if approved)' : '' }}</p>
                                <p class="font-bold" :class="hasInsufficientFunds ? 'text-red-600' : 'text-blue-900'">
                                    {{ formatCurrency(balanceAfterTransaction, selectedAccount.currency) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center gap-3 pt-4 border-t">
                        <button
                            type="submit"
                            :disabled="form.processing || hasInsufficientFunds"
                            class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed font-medium"
                        >
                            {{ form.processing ? 'Creating Transaction...' : 'Create Transaction' }}
                        </button>
                        <a
                            href="/admin/transactions"
                            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium"
                        >
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
