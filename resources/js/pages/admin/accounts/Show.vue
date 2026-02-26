<script setup lang="ts">
import { ref } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { ArrowLeft, Wallet, User, DollarSign, ArrowRightLeft, TrendingUp, Receipt } from 'lucide-vue-next';

interface User {
    id: number;
    name: string;
    email: string;
}

interface Transaction {
    id: number;
    description: string;
    amount: number;
    currency: string;
    transaction_type: string;
    transaction_date: string;
    status: string;
}

interface Account {
    id: number;
    account_number: string;
    account_name: string;
    account_type: string;
    currency: string;
    balance: number;
    available_balance: number;
    is_active: boolean;
    user: User;
    transactions: Transaction[];
}

interface Props {
    account: Account;
}

const props = defineProps<Props>();

const showFundModal = ref(false);
const showTransferModal = ref(false);

const fundForm = useForm({
    amount: '',
    description: '',
});

const transferForm = useForm({
    from_account_number: props.account.account_number,
    to_account_number: '',
    amount: '',
    description: '',
});

const fundAccount = () => {
    fundForm.post(`/admin/accounts/${props.account.id}/fund`, {
        onSuccess: () => {
            fundForm.reset();
            showFundModal.value = false;
        },
    });
};

const makeTransfer = () => {
    transferForm.post('/admin/accounts/transfer', {
        onSuccess: () => {
            transferForm.reset();
            showTransferModal.value = false;
        },
    });
};

const formatCurrency = (amount: number, currency: string) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency,
    }).format(amount);
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <AdminLayout :title="`Account: ${account.account_number}`">
        <div class="space-y-4 sm:space-y-6">
            <div class="flex flex-col gap-4">
                <div class="flex items-center gap-4">
                    <a
                        href="/admin/accounts"
                        class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
                    >
                        <ArrowLeft :size="20" class="text-gray-600" />
                    </a>
                    <div class="flex-1">
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">{{ account.account_name }}</h1>
                        <p class="text-gray-600 mt-1 text-sm sm:text-base">{{ account.account_number }}</p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a
                        :href="`/admin/accounts/${account.id}/edit`"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                        Edit Account
                    </a>
                    <button
                        @click="showFundModal = true"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                    >
                        <DollarSign :size="18" />
                        Fund Account
                    </button>
                    <button
                        @click="showTransferModal = true"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                    >
                        <ArrowRightLeft :size="18" />
                        Transfer
                    </button>
                </div>
            </div>

            <!-- Fund Account Modal -->
            <div
                v-if="showFundModal"
                class="fixed inset-0 backdrop-blur-sm bg-black/30 z-50 flex items-center justify-center p-4"
                @click="showFundModal = false"
            >
                <div
                    class="bg-white rounded-lg shadow-xl max-w-md w-full p-6"
                    @click.stop
                >
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Fund Account</h2>
                    <form @submit.prevent="fundAccount" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Amount ({{ account.currency }}) *
                            </label>
                            <input
                                v-model="fundForm.amount"
                                type="number"
                                step="0.01"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-gray-900"
                                placeholder="0.00"
                            />
                            <p v-if="fundForm.errors.amount" class="mt-1 text-sm text-red-600">
                                {{ fundForm.errors.amount }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Description *
                            </label>
                            <textarea
                                v-model="fundForm.description"
                                required
                                rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-gray-900"
                                placeholder="e.g., Admin deposit - Monthly allowance"
                            ></textarea>
                            <p v-if="fundForm.errors.description" class="mt-1 text-sm text-red-600">
                                {{ fundForm.errors.description }}
                            </p>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t">
                            <button
                                type="button"
                                @click="showFundModal = false"
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                :disabled="fundForm.processing"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50"
                            >
                                {{ fundForm.processing ? 'Processing...' : 'Fund Account' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Transfer Modal -->
            <div
                v-if="showTransferModal"
                class="fixed inset-0 backdrop-blur-sm bg-black/30 z-50 flex items-center justify-center p-4"
                @click="showTransferModal = false"
            >
                <div
                    class="bg-white rounded-lg shadow-xl max-w-md w-full p-6"
                    @click.stop
                >
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Intra-Account Transfer</h2>
                    <form @submit.prevent="makeTransfer" class="space-y-4">
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <p class="text-sm text-gray-600">From Account</p>
                            <p class="font-medium text-gray-900">{{ account.account_number }}</p>
                            <p class="text-sm text-gray-500">
                                Balance: {{ formatCurrency(account.available_balance, account.currency) }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                To Account Number *
                            </label>
                            <input
                                v-model="transferForm.to_account_number"
                                type="text"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-gray-900"
                                placeholder="e.g. ACC3821460197"
                            />
                            <p v-if="transferForm.errors.to_account_number" class="mt-1 text-sm text-red-600">
                                {{ transferForm.errors.to_account_number }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Amount ({{ account.currency }}) *
                            </label>
                            <input
                                v-model="transferForm.amount"
                                type="number"
                                step="0.01"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-gray-900"
                                placeholder="0.00"
                            />
                            <p v-if="transferForm.errors.amount" class="mt-1 text-sm text-red-600">
                                {{ transferForm.errors.amount }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Description *
                            </label>
                            <textarea
                                v-model="transferForm.description"
                                required
                                rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-gray-900"
                                placeholder="e.g., Admin transfer - Account reconciliation"
                            ></textarea>
                            <p v-if="transferForm.errors.description" class="mt-1 text-sm text-red-600">
                                {{ transferForm.errors.description }}
                            </p>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t">
                            <button
                                type="button"
                                @click="showTransferModal = false"
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                :disabled="transferForm.processing"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50"
                            >
                                {{ transferForm.processing ? 'Processing...' : 'Transfer Funds' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Account Details -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm text-gray-600">Current Balance</p>
                        <Wallet :size="20" class="text-gray-400" />
                    </div>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ formatCurrency(account.balance, account.currency) }}
                    </p>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm text-gray-600">Available Balance</p>
                        <TrendingUp :size="20" class="text-green-600" />
                    </div>
                    <p class="text-2xl font-bold text-green-600">
                        {{ formatCurrency(account.available_balance, account.currency) }}
                    </p>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm text-gray-600">Account Type</p>
                        <Receipt :size="20" class="text-gray-400" />
                    </div>
                    <p class="text-2xl font-bold text-gray-900 capitalize">
                        {{ account.account_type }}
                    </p>
                </div>
            </div>

            <!-- User Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Account Holder</h2>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center">
                        <User :size="24" class="text-gray-500" />
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">{{ account.user.name }}</p>
                        <p class="text-sm text-gray-500">{{ account.user.email }}</p>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Transactions</h2>
                </div>
                <div v-if="account.transactions.length > 0" class="divide-y divide-gray-200">
                    <div
                        v-for="transaction in account.transactions"
                        :key="transaction.id"
                        class="p-4 hover:bg-gray-50"
                    >
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ transaction.description }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ formatDate(transaction.transaction_date) }}
                                </p>
                            </div>
                            <div class="text-right flex flex-col items-end gap-1">
                                <p
                                    :class="[
                                        'text-sm font-medium',
                                        transaction.transaction_type === 'credit'
                                            ? 'text-green-600'
                                            : 'text-red-600',
                                    ]"
                                >
                                    {{ transaction.transaction_type === 'credit' ? '+' : '-' }}
                                    {{ formatCurrency(transaction.amount, transaction.currency) }}
                                </p>
                                <span
                                    :class="[
                                        'inline-block px-2 py-1 text-xs rounded-full',
                                        transaction.status === 'completed'
                                            ? 'bg-green-100 text-green-800'
                                            : transaction.status === 'pending'
                                            ? 'bg-yellow-100 text-yellow-800'
                                            : 'bg-red-100 text-red-800',
                                    ]"
                                >
                                    {{ transaction.status }}
                                </span>
                                <Link
                                    :href="`/admin/transactions/${transaction.id}/edit`"
                                    class="text-xs text-gray-500 hover:text-gray-800 underline"
                                >
                                    Edit
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else class="p-8 text-center text-gray-500">
                    <Receipt :size="48" class="mx-auto mb-3 text-gray-300" />
                    <p>No transactions found</p>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
