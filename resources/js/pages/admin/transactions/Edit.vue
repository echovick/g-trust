<script setup lang="ts">
import { ref, computed } from 'vue';
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

// Format the incoming UTC datetime to a local datetime-local input value
const toDatetimeLocal = (iso: string) => {
    const d = new Date(iso);
    const pad = (n: number) => String(n).padStart(2, '0');
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
};

const form = useForm({
    description:      props.transaction.description,
    amount:           props.transaction.amount,
    transaction_date: toDatetimeLocal(props.transaction.transaction_date),
});

const showDeleteModal = ref(false);
const deleteInProgress = ref(false);

const amountChanged = computed(() =>
    parseFloat(form.amount.toString()) !== parseFloat(props.transaction.amount.toString())
);

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

const formatCurrency = (amount: number, currency: string) =>
    new Intl.NumberFormat('en-US', { style: 'currency', currency }).format(amount);

const statusColors: Record<string, string> = {
    completed: 'bg-green-100 text-green-800',
    pending:   'bg-yellow-100 text-yellow-800',
    cancelled: 'bg-red-100 text-red-800',
    failed:    'bg-red-100 text-red-800',
};
</script>

<template>
    <AdminLayout :title="`Edit Transaction: ${transaction.reference_number}`">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <a
                    :href="`/admin/transactions/${transaction.id}`"
                    class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
                >
                    <ArrowLeft :size="20" class="text-gray-600" />
                </a>
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900">Edit Transaction</h1>
                    <p class="text-gray-500 mt-1 font-mono text-sm">{{ transaction.reference_number }}</p>
                </div>
                <button
                    @click="showDeleteModal = true"
                    class="px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition-colors flex items-center gap-2"
                >
                    <Trash2 :size="18" />
                    Delete
                </button>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 max-w-3xl space-y-6">
                <!-- Read-only info -->
                <div class="bg-gray-50 rounded-lg p-4 grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Account</p>
                        <p class="font-medium text-gray-900">{{ transaction.account.account_number }}</p>
                        <p class="text-gray-500">{{ transaction.account.user.name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Type</p>
                        <span
                            class="inline-block px-3 py-1 rounded-full text-xs font-medium"
                            :class="transaction.transaction_type === 'credit' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                        >
                            {{ transaction.transaction_type === 'credit' ? '+ Credit' : '− Debit' }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Status</p>
                        <span
                            class="inline-block px-3 py-1 rounded-full text-xs font-medium"
                            :class="statusColors[transaction.status] ?? 'bg-gray-100 text-gray-700'"
                        >
                            {{ transaction.status }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Category</p>
                        <p class="font-medium text-gray-900 capitalize">{{ transaction.category ?? '—' }}</p>
                    </div>
                </div>

                <form @submit.prevent="submitForm" class="space-y-5">
                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                        <textarea
                            v-model="form.description"
                            required
                            rows="2"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-gray-900"
                        ></textarea>
                        <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                            {{ form.errors.description }}
                        </p>
                    </div>

                    <!-- Transaction Date & Time -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date & Time *</label>
                        <input
                            v-model="form.transaction_date"
                            type="datetime-local"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-gray-900"
                        />
                        <p v-if="form.errors.transaction_date" class="mt-1 text-sm text-red-600">
                            {{ form.errors.transaction_date }}
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
                        <div v-if="amountChanged && transaction.status === 'completed'" class="mt-2 flex items-start gap-2 text-sm text-amber-800 bg-amber-50 border border-amber-200 rounded px-3 py-2">
                            <AlertCircle :size="16" class="mt-0.5 flex-shrink-0" />
                            <span>
                                Changing the amount on a completed transaction will adjust the account balance by
                                <strong>{{ formatCurrency(Math.abs(parseFloat(form.amount.toString()) - parseFloat(transaction.amount.toString())), transaction.currency) }}</strong>.
                            </span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-3 pt-4 border-t">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 font-medium"
                        >
                            {{ form.processing ? 'Saving...' : 'Save Changes' }}
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

        <!-- Delete Modal -->
        <div
            v-if="showDeleteModal"
            class="fixed inset-0 backdrop-blur-sm bg-black/30 z-50 flex items-center justify-center p-4"
            @click="showDeleteModal = false"
        >
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6" @click.stop>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <Trash2 :size="20" class="text-red-600" />
                    </div>
                    <h2 class="text-lg font-semibold text-gray-900">Delete Transaction</h2>
                </div>
                <div class="bg-gray-50 rounded-lg p-3 text-sm space-y-1 mb-4">
                    <p><span class="text-gray-500">Reference:</span> <span class="font-mono font-medium">{{ transaction.reference_number }}</span></p>
                    <p><span class="text-gray-500">Amount:</span> {{ formatCurrency(transaction.amount, transaction.currency) }}</p>
                    <p><span class="text-gray-500">Status:</span> {{ transaction.status }}</p>
                </div>
                <p class="text-sm text-gray-600 mb-6">This action cannot be undone.</p>
                <div class="flex justify-end gap-3">
                    <button
                        @click="showDeleteModal = false"
                        :disabled="deleteInProgress"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50"
                    >
                        Cancel
                    </button>
                    <button
                        @click="deleteTransaction"
                        :disabled="deleteInProgress"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50"
                    >
                        {{ deleteInProgress ? 'Deleting...' : 'Delete Transaction' }}
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
