<script setup lang="ts">
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { ArrowLeft, Trash2, AlertCircle } from 'lucide-vue-next';

interface Account {
    id: number;
    account_number: string;
    account_name: string;
    currency: string;
    balance: number;
    available_balance: number;
    user: {
        id: number;
        name: string;
        email: string;
    };
}

interface Transaction {
    id: number;
    account_id: number;
    transaction_type: 'debit' | 'credit';
    category: string;
    description: string;
    amount: number;
    currency: string;
    balance_after: number;
    reference_number: string;
    status: string;
    transaction_date: string;
    account: Account;
}

interface Props {
    transaction: Transaction;
}

const props = defineProps<Props>();

const form = useForm({
    category: props.transaction.category,
    description: props.transaction.description,
    amount: props.transaction.amount,
});

const showDeleteModal = ref(false);
const deleteInProgress = ref(false);

const submitForm = () => {
    form.put(`/admin/transactions/${props.transaction.id}`, {
        preserveScroll: true,
    });
};

const deleteTransaction = () => {
    deleteInProgress.value = true;
    router.delete(`/admin/transactions/${props.transaction.id}`, {
        onFinish: () => {
            deleteInProgress.value = false;
            showDeleteModal.value = false;
        },
    });
};

const formatCurrency = (amount: number, currency: string) => {
    return `${currency} ${amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'long',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
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
    'reversal',
    'other',
];
</script>

<template>
    <AdminLayout :title="`Edit Transaction: ${transaction.reference_number}`">
        <div class="space-y-6">
            <div class="flex items-center gap-4">
                <a
                    :href="`/admin/transactions/${transaction.id}`"
                    class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
                >
                    <ArrowLeft :size="20" class="text-gray-600" />
                </a>
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900">Edit Transaction</h1>
                    <p class="text-gray-600 mt-1">{{ transaction.reference_number }}</p>
                </div>
                <button
                    @click="showDeleteModal = true"
                    class="px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition-colors flex items-center gap-2"
                >
                    <Trash2 :size="18" />
                    Delete Transaction
                </button>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 flex items-start gap-3">
                <AlertCircle :size="20" class="text-yellow-600 mt-0.5 flex-shrink-0" />
                <div>
                    <p class="text-sm font-medium text-yellow-900">Editing Restrictions</p>
                    <p class="text-sm text-yellow-800 mt-1">
                        Only pending transactions can be edited. Changes to the amount will not affect account balances until the transaction is approved.
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 max-w-3xl">
                <!-- Transaction Info (Read-only) -->
                <div class="mb-6 pb-6 border-b">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Transaction Information</h3>
                    <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-600">Account</p>
                                <p class="font-medium text-gray-900">{{ transaction.account.account_number }}</p>
                                <p class="text-sm text-gray-600">{{ transaction.account.user.name }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Transaction Type</p>
                                <span
                                    class="inline-block mt-1 px-3 py-1 rounded-full text-sm font-medium"
                                    :class="transaction.transaction_type === 'credit' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                >
                                    {{ transaction.transaction_type === 'credit' ? '+ Credit' : '- Debit' }}
                                </span>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-600">Reference Number</p>
                                <p class="font-mono text-sm text-gray-900">{{ transaction.reference_number }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Date Created</p>
                                <p class="text-sm text-gray-900">{{ formatDate(transaction.transaction_date) }}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-600">Status</p>
                                <span class="inline-block mt-1 px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">
                                    {{ transaction.status }}
                                </span>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Account Balance</p>
                                <p class="font-medium text-gray-900">
                                    {{ formatCurrency(transaction.account.balance, transaction.currency) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <form @submit.prevent="submitForm" class="space-y-6">
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
                        ></textarea>
                        <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                            {{ form.errors.description }}
                        </p>
                    </div>

                    <!-- Amount -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Amount ({{ transaction.currency }}) *
                        </label>
                        <input
                            v-model="form.amount"
                            type="number"
                            step="0.01"
                            min="0.01"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-gray-900"
                        />
                        <p v-if="form.errors.amount" class="mt-1 text-sm text-red-600">
                            {{ form.errors.amount }}
                        </p>
                        <p v-if="form.amount !== transaction.amount" class="mt-2 text-sm text-yellow-700 bg-yellow-50 border border-yellow-200 rounded px-3 py-2">
                            ⚠️ Amount changed from {{ formatCurrency(transaction.amount, transaction.currency) }} to {{ formatCurrency(parseFloat(form.amount.toString()), transaction.currency) }}
                        </p>
                    </div>

                    <!-- Balance Impact Notice -->
                    <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-sm text-blue-800">
                            <strong>Note:</strong> Changes will not affect the account balance until this transaction is approved.
                            This transaction is currently pending and has not been processed.
                        </p>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center gap-3 pt-4 border-t">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 font-medium"
                        >
                            {{ form.processing ? 'Saving Changes...' : 'Save Changes' }}
                        </button>
                        <a
                            :href="`/admin/transactions/${transaction.id}`"
                            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium"
                        >
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div
            v-if="showDeleteModal"
            class="fixed inset-0 backdrop-blur-sm bg-black/30 z-50 flex items-center justify-center p-4"
            @click="showDeleteModal = false"
        >
            <div
                class="bg-white rounded-lg shadow-xl max-w-md w-full p-6"
                @click.stop
            >
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <AlertCircle :size="24" class="text-red-600" />
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Delete Transaction</h2>
                        <p class="text-sm text-gray-600">This action cannot be undone</p>
                    </div>
                </div>

                <div class="mb-6">
                    <p class="text-sm text-gray-700 mb-3">
                        Are you sure you want to delete this transaction?
                    </p>
                    <div class="bg-gray-50 rounded-lg p-3 text-sm space-y-2">
                        <p><strong>Reference:</strong> {{ transaction.reference_number }}</p>
                        <p><strong>Description:</strong> {{ transaction.description }}</p>
                        <p><strong>Amount:</strong> {{ formatCurrency(transaction.amount, transaction.currency) }}</p>
                        <p><strong>Status:</strong> {{ transaction.status }}</p>
                    </div>
                    <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <p class="text-xs text-yellow-800">
                            <strong>Note:</strong> Only pending or cancelled transactions can be deleted.
                            For completed transactions, use the reverse function instead.
                        </p>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button
                        @click="showDeleteModal = false"
                        :disabled="deleteInProgress"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                    >
                        Cancel
                    </button>
                    <button
                        @click="deleteTransaction"
                        :disabled="deleteInProgress"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50"
                    >
                        {{ deleteInProgress ? 'Deleting...' : 'Delete Transaction' }}
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
