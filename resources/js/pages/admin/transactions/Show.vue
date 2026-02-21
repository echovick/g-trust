<script setup lang="ts">
import { ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import {
    ArrowLeft, CheckCircle, XCircle, RotateCcw, ArrowUpRight,
    ArrowDownLeft, User, Wallet, Calendar, Hash
} from 'lucide-vue-next';

interface Account {
    id: number;
    account_number: string;
    account_name: string;
    balance: number;
    currency: string;
}

interface User {
    id: number;
    name: string;
    email: string;
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
    status: 'pending' | 'completed' | 'failed' | 'cancelled';
    merchant_name?: string;
    transaction_date: string;
    created_at: string;
    account?: Account & { user?: User };
    relatedAccount?: Account;
}

interface Props {
    transaction: Transaction;
}

const props = defineProps<Props>();

const showRejectModal = ref(false);
const showReverseModal = ref(false);

const rejectForm = useForm({
    reason: '',
});

const reverseForm = useForm({
    reason: '',
});

const approveTransaction = () => {
    if (confirm('Are you sure you want to approve this transaction?')) {
        router.post(`/admin/transactions/${props.transaction.id}/approve`, {}, {
            preserveState: false,
        });
    }
};

const rejectTransaction = () => {
    rejectForm.post(`/admin/transactions/${props.transaction.id}/reject`, {
        onSuccess: () => {
            showRejectModal.value = false;
            rejectForm.reset();
        },
    });
};

const reverseTransaction = () => {
    reverseForm.post(`/admin/transactions/${props.transaction.id}/reverse`, {
        onSuccess: () => {
            showReverseModal.value = false;
            reverseForm.reset();
        },
    });
};

const formatCurrency = (amount: number, currency: string) => {
    return `${currency} ${amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleString('en-US', {
        month: 'long',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const getStatusColor = (status: string) => {
    switch (status) {
        case 'completed': return 'bg-green-50 text-green-700 border-green-200';
        case 'pending': return 'bg-yellow-50 text-yellow-700 border-yellow-200';
        case 'failed': return 'bg-red-50 text-red-700 border-red-200';
        case 'cancelled': return 'bg-gray-50 text-gray-700 border-gray-200';
        default: return 'bg-gray-50 text-gray-700 border-gray-200';
    }
};
</script>

<template>
    <AdminLayout title="Transaction Details">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <Link href="/admin/transactions" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                    <ArrowLeft :size="20" />
                </Link>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Transaction Details</h1>
                    <p class="text-gray-600 mt-1">{{ transaction.reference_number }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Transaction Overview -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="flex items-start justify-between mb-6">
                            <div class="flex items-start gap-4">
                                <div
                                    class="w-16 h-16 rounded-full flex items-center justify-center"
                                    :class="transaction.transaction_type === 'credit' ? 'bg-green-50' : 'bg-red-50'"
                                >
                                    <ArrowDownLeft
                                        v-if="transaction.transaction_type === 'credit'"
                                        :size="32"
                                        class="text-green-600"
                                    />
                                    <ArrowUpRight v-else :size="32" class="text-red-600" />
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900">
                                        {{ transaction.transaction_type === 'credit' ? '+' : '-' }}
                                        {{ formatCurrency(transaction.amount, transaction.currency) }}
                                    </h2>
                                    <p class="text-gray-600 mt-1">{{ transaction.description }}</p>
                                    <p class="text-sm text-gray-500 mt-1 capitalize">
                                        {{ transaction.category.replace('_', ' ') }}
                                    </p>
                                </div>
                            </div>
                            <span
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium border"
                                :class="getStatusColor(transaction.status)"
                            >
                                {{ transaction.status.charAt(0).toUpperCase() + transaction.status.slice(1) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                                    <Hash :size="16" />
                                    <span>Reference Number</span>
                                </div>
                                <p class="font-mono text-sm font-medium text-gray-900">{{ transaction.reference_number }}</p>
                            </div>

                            <div>
                                <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                                    <Calendar :size="16" />
                                    <span>Transaction Date</span>
                                </div>
                                <p class="text-sm font-medium text-gray-900">{{ formatDate(transaction.transaction_date) }}</p>
                            </div>

                            <div>
                                <div class="text-sm text-gray-500 mb-1">Type</div>
                                <p class="text-sm font-medium text-gray-900 capitalize">
                                    {{ transaction.transaction_type }}
                                </p>
                            </div>

                            <div>
                                <div class="text-sm text-gray-500 mb-1">Balance After</div>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ formatCurrency(transaction.balance_after, transaction.currency) }}
                                </p>
                            </div>

                            <div v-if="transaction.merchant_name" class="col-span-2">
                                <div class="text-sm text-gray-500 mb-1">Merchant</div>
                                <p class="text-sm font-medium text-gray-900">{{ transaction.merchant_name }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Account Information -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Information</h3>
                        <div class="space-y-4">
                            <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-lg">
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <Wallet :size="24" class="text-blue-600" />
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ transaction.account?.account_name }}</p>
                                            <p class="text-sm text-gray-600">{{ transaction.account?.account_number }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm text-gray-500">Current Balance</p>
                                            <p class="font-semibold text-gray-900">
                                                {{ formatCurrency(transaction.account?.balance || 0, transaction.account?.currency || 'USD') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-lg">
                                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <User :size="24" class="text-purple-600" />
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900">{{ transaction.account?.user?.name }}</p>
                                    <p class="text-sm text-gray-600">{{ transaction.account?.user?.email }}</p>
                                    <Link
                                        :href="`/admin/users/${transaction.account?.user?.id}`"
                                        class="text-sm text-red-600 hover:text-red-700 font-medium mt-2 inline-block"
                                    >
                                        View User Profile →
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions Sidebar -->
                <div class="space-y-6">
                    <!-- Admin Actions -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Admin Actions</h3>
                        <div class="space-y-3">
                            <Link
                                v-if="transaction.status === 'pending'"
                                :href="`/admin/transactions/${transaction.id}/edit`"
                                class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                                Edit Transaction
                            </Link>

                            <button
                                v-if="transaction.status === 'pending'"
                                @click="approveTransaction"
                                class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium"
                            >
                                <CheckCircle :size="20" />
                                Approve Transaction
                            </button>

                            <button
                                v-if="transaction.status === 'pending'"
                                @click="showRejectModal = true"
                                class="w-full flex items-center justify-center gap-2 px-4 py-3 border-2 border-red-600 text-red-600 rounded-lg hover:bg-red-50 transition-colors font-medium"
                            >
                                <XCircle :size="20" />
                                Reject Transaction
                            </button>

                            <button
                                v-if="transaction.status === 'completed'"
                                @click="showReverseModal = true"
                                class="w-full flex items-center justify-center gap-2 px-4 py-3 border-2 border-orange-600 text-orange-600 rounded-lg hover:bg-orange-50 transition-colors font-medium"
                            >
                                <RotateCcw :size="20" />
                                Reverse Transaction
                            </button>

                            <p v-if="transaction.status === 'cancelled'" class="text-sm text-gray-500 text-center py-4">
                                This transaction has been cancelled and cannot be modified.
                            </p>
                        </div>
                    </div>

                    <!-- Activity Log (placeholder) -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Activity Log</h3>
                        <div class="space-y-3">
                            <div class="flex items-start gap-3 text-sm">
                                <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                                <div>
                                    <p class="font-medium text-gray-900">Transaction Created</p>
                                    <p class="text-gray-500">{{ formatDate(transaction.created_at) }}</p>
                                </div>
                            </div>
                            <div v-if="transaction.status === 'completed'" class="flex items-start gap-3 text-sm">
                                <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                                <div>
                                    <p class="font-medium text-gray-900">Transaction Completed</p>
                                    <p class="text-gray-500">{{ formatDate(transaction.transaction_date) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        <div
            v-if="showRejectModal"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
            @click.self="showRejectModal = false"
        >
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Reject Transaction</h3>
                <form @submit.prevent="rejectTransaction" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Reason for Rejection <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            v-model="rejectForm.reason"
                            rows="4"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                            placeholder="Explain why this transaction is being rejected..."
                        ></textarea>
                        <div v-if="rejectForm.errors.reason" class="mt-2 text-sm text-red-600">
                            {{ rejectForm.errors.reason }}
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button
                            type="button"
                            @click="showRejectModal = false"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="rejectForm.processing"
                            class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors disabled:opacity-50"
                        >
                            {{ rejectForm.processing ? 'Rejecting...' : 'Reject Transaction' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Reverse Modal -->
        <div
            v-if="showReverseModal"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
            @click.self="showReverseModal = false"
        >
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Reverse Transaction</h3>
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-4">
                    <p class="text-sm text-orange-800">
                        <strong>Warning:</strong> This will create a reversal transaction and update the account balance.
                        This action cannot be undone.
                    </p>
                </div>
                <form @submit.prevent="reverseTransaction" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Reason for Reversal <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            v-model="reverseForm.reason"
                            rows="4"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                            placeholder="Explain why this transaction is being reversed..."
                        ></textarea>
                        <div v-if="reverseForm.errors.reason" class="mt-2 text-sm text-red-600">
                            {{ reverseForm.errors.reason }}
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button
                            type="button"
                            @click="showReverseModal = false"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="reverseForm.processing"
                            class="flex-1 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium transition-colors disabled:opacity-50"
                        >
                            {{ reverseForm.processing ? 'Reversing...' : 'Reverse Transaction' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
