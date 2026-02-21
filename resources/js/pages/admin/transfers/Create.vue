<script setup lang="ts">
import { computed } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { ArrowLeft, Send, DollarSign, FileText, User, Wallet } from 'lucide-vue-next';

interface Account {
    id: number;
    account_number: string;
    account_name: string;
    account_type: string;
    currency: string;
    balance: number;
    available_balance: number;
    user?: {
        id: number;
        name: string;
        email: string;
    };
}

interface Props {
    accounts: Account[];
}

const props = defineProps<Props>();

const transferForm = useForm({
    from_account_id: '',
    to_account_id: '',
    amount: '',
    description: '',
    execute_immediately: true,
});

const fromAccount = computed(() => {
    return props.accounts.find(acc => acc.id === Number(transferForm.from_account_id));
});

const toAccount = computed(() => {
    return props.accounts.find(acc => acc.id === Number(transferForm.to_account_id));
});

const availableToAccounts = computed(() => {
    return props.accounts.filter(acc => acc.id !== Number(transferForm.from_account_id));
});

const submitTransfer = () => {
    transferForm.post('/admin/transfers', {
        preserveScroll: true,
    });
};

const formatCurrency = (amount: number, currency: string) => {
    return `${currency} ${amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
};
</script>

<template>
    <AdminLayout title="Create Transfer">
        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <Link href="/admin/transfers" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                    <ArrowLeft :size="20" />
                </Link>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Create New Transfer</h1>
                    <p class="text-gray-600 mt-1">Initiate a transfer between any accounts</p>
                </div>
            </div>

            <form @submit.prevent="submitTransfer" class="space-y-6">
                <!-- From Account -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Source Account</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                From Account <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="transferForm.from_account_id"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                            >
                                <option value="">Select source account</option>
                                <option v-for="account in accounts" :key="account.id" :value="account.id">
                                    {{ account.account_name }} ({{ account.account_number }}) -
                                    {{ account.user?.name }} -
                                    {{ formatCurrency(account.available_balance, account.currency) }}
                                </option>
                            </select>
                            <div v-if="transferForm.errors.from_account_id" class="mt-2 text-sm text-red-600">
                                {{ transferForm.errors.from_account_id }}
                            </div>
                        </div>

                        <!-- From Account Details -->
                        <div v-if="fromAccount" class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <Wallet :size="24" class="text-blue-600" />
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ fromAccount.account_name }}</p>
                                            <p class="text-sm text-gray-600">{{ fromAccount.account_number }}</p>
                                            <p class="text-sm text-gray-500 mt-1 capitalize">{{ fromAccount.account_type }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm text-gray-500">Available Balance</p>
                                            <p class="font-semibold text-gray-900">
                                                {{ formatCurrency(fromAccount.available_balance, fromAccount.currency) }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-3 pt-3 border-t border-blue-200 flex items-center gap-2">
                                        <User :size="16" class="text-gray-500" />
                                        <span class="text-sm text-gray-700">{{ fromAccount.user?.name }}</span>
                                        <span class="text-sm text-gray-500">•</span>
                                        <span class="text-sm text-gray-500">{{ fromAccount.user?.email }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- To Account -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Destination Account</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                To Account <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="transferForm.to_account_id"
                                required
                                :disabled="!transferForm.from_account_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 disabled:bg-gray-100 disabled:cursor-not-allowed"
                            >
                                <option value="">{{ transferForm.from_account_id ? 'Select destination account' : 'Select source account first' }}</option>
                                <option v-for="account in availableToAccounts" :key="account.id" :value="account.id">
                                    {{ account.account_name }} ({{ account.account_number }}) -
                                    {{ account.user?.name }} -
                                    {{ account.currency }}
                                </option>
                            </select>
                            <div v-if="transferForm.errors.to_account_id" class="mt-2 text-sm text-red-600">
                                {{ transferForm.errors.to_account_id }}
                            </div>
                        </div>

                        <!-- To Account Details -->
                        <div v-if="toAccount" class="p-4 bg-green-50 rounded-lg border border-green-200">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <Wallet :size="24" class="text-green-600" />
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ toAccount.account_name }}</p>
                                            <p class="text-sm text-gray-600">{{ toAccount.account_number }}</p>
                                            <p class="text-sm text-gray-500 mt-1 capitalize">{{ toAccount.account_type }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm text-gray-500">Current Balance</p>
                                            <p class="font-semibold text-gray-900">
                                                {{ formatCurrency(toAccount.balance, toAccount.currency) }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-3 pt-3 border-t border-green-200 flex items-center gap-2">
                                        <User :size="16" class="text-gray-500" />
                                        <span class="text-sm text-gray-700">{{ toAccount.user?.name }}</span>
                                        <span class="text-sm text-gray-500">•</span>
                                        <span class="text-sm text-gray-500">{{ toAccount.user?.email }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Currency Warning -->
                        <div v-if="fromAccount && toAccount && fromAccount.currency !== toAccount.currency" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <p class="text-sm text-yellow-800">
                                <strong>Note:</strong> This transfer involves currency conversion from {{ fromAccount.currency }} to {{ toAccount.currency }}.
                                The current exchange rate will be applied automatically.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Transfer Details -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Transfer Details</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <div class="flex items-center gap-2">
                                    <DollarSign :size="16" />
                                    Amount <span class="text-red-500">*</span>
                                </div>
                            </label>
                            <div class="relative">
                                <input
                                    v-model="transferForm.amount"
                                    type="number"
                                    step="0.01"
                                    min="0.01"
                                    required
                                    :disabled="!fromAccount"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 disabled:bg-gray-100 disabled:cursor-not-allowed"
                                    :placeholder="fromAccount ? `Enter amount in ${fromAccount.currency}` : 'Select source account first'"
                                />
                                <span v-if="fromAccount" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500">
                                    {{ fromAccount.currency }}
                                </span>
                            </div>
                            <div v-if="transferForm.errors.amount" class="mt-2 text-sm text-red-600">
                                {{ transferForm.errors.amount }}
                            </div>
                            <p v-if="fromAccount && transferForm.amount" class="text-sm text-gray-500 mt-2">
                                Available: {{ formatCurrency(fromAccount.available_balance, fromAccount.currency) }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <div class="flex items-center gap-2">
                                    <FileText :size="16" />
                                    Description <span class="text-red-500">*</span>
                                </div>
                            </label>
                            <textarea
                                v-model="transferForm.description"
                                rows="3"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                placeholder="Enter transfer description or reason"
                            ></textarea>
                            <div v-if="transferForm.errors.description" class="mt-2 text-sm text-red-600">
                                {{ transferForm.errors.description }}
                            </div>
                        </div>

                        <div class="border-t pt-4">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input
                                    type="checkbox"
                                    v-model="transferForm.execute_immediately"
                                    class="mt-1 w-4 h-4 text-red-500 border-gray-300 rounded focus:ring-red-500"
                                />
                                <div>
                                    <div class="font-medium text-gray-900">Execute Immediately</div>
                                    <div class="text-sm text-gray-600">
                                        Process this transfer right away. If unchecked, the transfer will be created as pending.
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex gap-4">
                    <Link
                        href="/admin/transfers"
                        class="flex-1 px-6 py-3 border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50 transition-colors text-center"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="transferForm.processing"
                        class="flex-1 flex items-center justify-center gap-2 px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <Send :size="20" />
                        {{ transferForm.processing ? 'Creating Transfer...' : 'Create Transfer' }}
                    </button>
                </div>

                <!-- Summary -->
                <div v-if="fromAccount && toAccount && transferForm.amount" class="bg-gray-50 rounded-lg border border-gray-200 p-6">
                    <h4 class="font-semibold text-gray-900 mb-4">Transfer Summary</h4>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">From</span>
                            <span class="font-medium text-gray-900">{{ fromAccount.account_name }} ({{ fromAccount.account_number }})</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">To</span>
                            <span class="font-medium text-gray-900">{{ toAccount.account_name }} ({{ toAccount.account_number }})</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Amount</span>
                            <span class="font-medium text-gray-900">{{ formatCurrency(Number(transferForm.amount), fromAccount.currency) }}</span>
                        </div>
                        <div v-if="fromAccount.currency !== toAccount.currency" class="flex justify-between">
                            <span class="text-gray-600">Currency Conversion</span>
                            <span class="font-medium text-gray-900">{{ fromAccount.currency }} → {{ toAccount.currency }}</span>
                        </div>
                        <div class="flex justify-between pt-3 border-t">
                            <span class="text-gray-600">Execution</span>
                            <span class="font-medium text-gray-900">{{ transferForm.execute_immediately ? 'Immediate' : 'Pending Approval' }}</span>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
