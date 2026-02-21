<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import {
    Search, Filter, Download, ArrowUpRight, ArrowDownLeft,
    Receipt, TrendingUp, Clock, CheckCircle, XCircle, CheckSquare, Trash2
} from 'lucide-vue-next';

interface Account {
    id: number;
    account_number: string;
    account_name: string;
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
    transaction_date: string;
    account?: Account & { user?: User };
}

interface Props {
    transactions: {
        data: Transaction[];
        links: any[];
        meta: any;
    };
    stats: {
        total_transactions: number;
        pending_transactions: number;
        total_volume: number;
        today_volume: number;
    };
    filters: {
        search?: string;
        type?: string;
        status?: string;
        category?: string;
        date_from?: string;
        date_to?: string;
    };
}

const props = defineProps<Props>();

const searchForm = useForm({
    search: props.filters.search || '',
    type: props.filters.type || '',
    status: props.filters.status || '',
    category: props.filters.category || '',
    date_from: props.filters.date_from || '',
    date_to: props.filters.date_to || '',
});

const selectedTransactions = ref<number[]>([]);
const showFilters = ref(false);
const showBulkApproveModal = ref(false);
const showBulkRejectModal = ref(false);
const showBulkDeleteModal = ref(false);

const bulkRejectForm = useForm({
    transaction_ids: [] as number[],
    reason: '',
});

const selectAll = computed({
    get: () => {
        return props.transactions?.data?.length > 0 &&
               selectedTransactions.value.length === props.transactions.data.length;
    },
    set: (value: boolean) => {
        if (value) {
            selectedTransactions.value = props.transactions.data.map(t => t.id);
        } else {
            selectedTransactions.value = [];
        }
    }
});

const pendingSelected = computed(() => {
    return props.transactions.data
        .filter(t => selectedTransactions.value.includes(t.id) && t.status === 'pending')
        .length;
});

const toggleTransactionSelection = (transactionId: number) => {
    const index = selectedTransactions.value.indexOf(transactionId);
    if (index > -1) {
        selectedTransactions.value.splice(index, 1);
    } else {
        selectedTransactions.value.push(transactionId);
    }
};

const searchTransactions = () => {
    router.get('/admin/transactions', searchForm.data(), {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    searchForm.reset();
    searchTransactions();
};

const bulkApprove = () => {
    router.post('/admin/transactions/bulk/approve', {
        transaction_ids: selectedTransactions.value,
    }, {
        onSuccess: () => {
            selectedTransactions.value = [];
            showBulkApproveModal.value = false;
        },
    });
};

const bulkReject = () => {
    bulkRejectForm.transaction_ids = selectedTransactions.value;
    bulkRejectForm.post('/admin/transactions/bulk/reject', {
        onSuccess: () => {
            selectedTransactions.value = [];
            showBulkRejectModal.value = false;
            bulkRejectForm.reset();
        },
    });
};

const bulkDelete = () => {
    if (confirm(`Are you sure you want to delete ${selectedTransactions.value.length} transaction(s)? This cannot be undone.`)) {
        router.post('/admin/transactions/bulk/destroy', {
            transaction_ids: selectedTransactions.value,
        }, {
            onSuccess: () => {
                selectedTransactions.value = [];
                showBulkDeleteModal.value = false;
            },
        });
    }
};

const bulkExport = () => {
    const params = new URLSearchParams(searchForm.data() as any);
    if (selectedTransactions.value.length > 0) {
        selectedTransactions.value.forEach(id => params.append('transaction_ids[]', id.toString()));
    }
    window.location.href = `/admin/transactions/bulk/export?${params.toString()}`;
};

const formatCurrency = (amount: number, currency: string) => {
    return `${currency} ${amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
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

const getStatusColor = (status: string) => {
    switch (status) {
        case 'completed': return 'bg-green-50 text-green-700';
        case 'pending': return 'bg-yellow-50 text-yellow-700';
        case 'failed': return 'bg-red-50 text-red-700';
        case 'cancelled': return 'bg-gray-50 text-gray-700';
        default: return 'bg-gray-50 text-gray-700';
    }
};

const getStatusIcon = (status: string) => {
    switch (status) {
        case 'completed': return CheckCircle;
        case 'pending': return Clock;
        default: return XCircle;
    }
};
</script>

<template>
    <AdminLayout title="Transaction Management">
        <div class="space-y-4 sm:space-y-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Transactions</h1>
                    <p class="text-gray-600 mt-1">Monitor and manage all system transactions</p>
                </div>
                <Link
                    href="/admin/transactions/create"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                >
                    <Receipt :size="20" />
                    <span class="hidden sm:inline">Create Transaction</span>
                    <span class="sm:hidden">Create</span>
                </Link>
            </div>

            <!-- Bulk Actions Bar -->
            <div v-if="selectedTransactions.length > 0" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <CheckSquare :size="20" class="text-blue-600" />
                        <span class="text-sm font-medium text-blue-900">
                            {{ selectedTransactions.length }} transaction(s) selected
                            <span v-if="pendingSelected > 0" class="text-yellow-700">
                                ({{ pendingSelected }} pending)
                            </span>
                        </span>
                        <button
                            @click="selectedTransactions = []"
                            class="text-sm text-blue-600 hover:text-blue-800 underline"
                        >
                            Clear selection
                        </button>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-if="pendingSelected > 0"
                            @click="showBulkApproveModal = true"
                            class="inline-flex items-center gap-2 px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors"
                        >
                            <CheckCircle :size="16" />
                            Approve ({{ pendingSelected }})
                        </button>
                        <button
                            v-if="pendingSelected > 0"
                            @click="showBulkRejectModal = true"
                            class="inline-flex items-center gap-2 px-3 py-2 bg-orange-600 text-white text-sm rounded-lg hover:bg-orange-700 transition-colors"
                        >
                            <XCircle :size="16" />
                            Reject ({{ pendingSelected }})
                        </button>
                        <button
                            @click="bulkExport"
                            class="inline-flex items-center gap-2 px-3 py-2 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700 transition-colors"
                        >
                            <Download :size="16" />
                            Export
                        </button>
                        <button
                            @click="showBulkDeleteModal = true"
                            class="inline-flex items-center gap-2 px-3 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors"
                        >
                            <Trash2 :size="16" />
                            Delete
                        </button>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Total Transactions</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.total_transactions.toLocaleString() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <Receipt :size="24" class="text-blue-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Pending</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.pending_transactions }}</p>
                        </div>
                        <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                            <Clock :size="24" class="text-yellow-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Total Volume</p>
                            <p class="text-xl sm:text-2xl font-bold text-gray-900 mt-1">
                                ${{ stats.total_volume.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <TrendingUp :size="24" class="text-green-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Today's Volume</p>
                            <p class="text-xl sm:text-2xl font-bold text-gray-900 mt-1">
                                ${{ stats.today_volume.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                            <Receipt :size="24" class="text-purple-600" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <form @submit.prevent="searchTransactions" class="space-y-4">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="flex-1 relative">
                            <Search :size="20" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
                            <input
                                v-model="searchForm.search"
                                type="text"
                                placeholder="Search by reference, description, user..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-gray-900"
                            />
                        </div>
                        <button
                            type="button"
                            @click="showFilters = !showFilters"
                            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors flex items-center justify-center gap-2"
                        >
                            <Filter :size="20" />
                            Filters
                        </button>
                        <button
                            type="submit"
                            class="px-6 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition-colors whitespace-nowrap"
                        >
                            Search
                        </button>
                    </div>

                    <!-- Advanced Filters -->
                    <div v-if="showFilters" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-4 border-t">
                        <select
                            v-model="searchForm.type"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                        >
                            <option value="">All Types</option>
                            <option value="credit">Credit</option>
                            <option value="debit">Debit</option>
                        </select>

                        <select
                            v-model="searchForm.status"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                        >
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                            <option value="failed">Failed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>

                        <input
                            v-model="searchForm.date_from"
                            type="date"
                            placeholder="From Date"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                        />

                        <input
                            v-model="searchForm.date_to"
                            type="date"
                            placeholder="To Date"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                        />

                        <button
                            type="button"
                            @click="clearFilters"
                            class="px-4 py-2 text-gray-600 hover:text-gray-900 transition-colors"
                        >
                            Clear Filters
                        </button>
                    </div>
                </form>
            </div>

            <!-- Transactions Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left">
                                    <input
                                        type="checkbox"
                                        v-model="selectAll"
                                        class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500"
                                    />
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Transaction
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    User / Account
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Amount
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="transaction in transactions.data" :key="transaction.id"
                                class="hover:bg-gray-50"
                                :class="selectedTransactions.includes(transaction.id) ? 'bg-blue-50' : ''"
                            >
                                <td class="px-6 py-4">
                                    <input
                                        type="checkbox"
                                        :checked="selectedTransactions.includes(transaction.id)"
                                        @change="toggleTransactionSelection(transaction.id)"
                                        class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500"
                                    />
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-start gap-3">
                                        <div
                                            class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0"
                                            :class="transaction.transaction_type === 'credit' ? 'bg-green-50' : 'bg-red-50'"
                                        >
                                            <ArrowDownLeft
                                                v-if="transaction.transaction_type === 'credit'"
                                                :size="20"
                                                class="text-green-600"
                                            />
                                            <ArrowUpRight v-else :size="20" class="text-red-600" />
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ transaction.description }}</div>
                                            <div class="text-xs text-gray-500">Ref: {{ transaction.reference_number }}</div>
                                            <div class="text-xs text-gray-500 capitalize">{{ transaction.category }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-900">{{ transaction.account?.user?.name || 'N/A' }}</div>
                                        <div class="text-gray-500">{{ transaction.account?.account_number }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div
                                        class="text-sm font-semibold"
                                        :class="transaction.transaction_type === 'credit' ? 'text-green-600' : 'text-red-600'"
                                    >
                                        {{ transaction.transaction_type === 'credit' ? '+' : '-' }}
                                        {{ formatCurrency(transaction.amount, transaction.currency) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium"
                                        :class="getStatusColor(transaction.status)"
                                    >
                                        <component :is="getStatusIcon(transaction.status)" :size="14" />
                                        {{ transaction.status.charAt(0).toUpperCase() + transaction.status.slice(1) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ formatDate(transaction.transaction_date) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <Link
                                        :href="`/admin/transactions/${transaction.id}`"
                                        class="text-red-600 hover:text-red-900 font-medium"
                                    >
                                        View Details
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="transactions.meta.last_page > 1" class="px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            Showing {{ ((transactions.meta.current_page - 1) * transactions.meta.per_page) + 1 }}
                            to {{ Math.min(transactions.meta.current_page * transactions.meta.per_page, transactions.meta.total) }}
                            of {{ transactions.meta.total }} transactions
                        </div>
                        <div class="flex gap-2">
                            <Link
                                v-for="link in transactions.links"
                                :key="link.label"
                                :href="link.url || '#'"
                                :class="[
                                    'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                                    link.active
                                        ? 'bg-red-500 text-white'
                                        : link.url
                                            ? 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                            : 'bg-gray-50 text-gray-400 cursor-not-allowed'
                                ]"
                                v-html="link.label"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bulk Approve Modal -->
        <div
            v-if="showBulkApproveModal"
            class="fixed inset-0 backdrop-blur-sm bg-black/30 z-50 flex items-center justify-center p-4"
            @click="showBulkApproveModal = false"
        >
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6" @click.stop>
                <h2 class="text-xl font-bold text-gray-900 mb-4">Approve Transactions</h2>
                <p class="text-sm text-gray-600 mb-4">
                    Are you sure you want to approve {{ pendingSelected }} pending transaction(s)?
                </p>
                <p class="text-sm text-green-800 bg-green-50 border border-green-200 rounded p-3 mb-4">
                    <strong>Note:</strong> Account balances will be updated immediately. Transactions with insufficient funds will be skipped.
                </p>
                <div class="flex justify-end gap-3">
                    <button
                        @click="showBulkApproveModal = false"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50"
                    >
                        Cancel
                    </button>
                    <button
                        @click="bulkApprove"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700"
                    >
                        Approve Transactions
                    </button>
                </div>
            </div>
        </div>

        <!-- Bulk Reject Modal -->
        <div
            v-if="showBulkRejectModal"
            class="fixed inset-0 backdrop-blur-sm bg-black/30 z-50 flex items-center justify-center p-4"
            @click="showBulkRejectModal = false"
        >
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6" @click.stop>
                <h2 class="text-xl font-bold text-gray-900 mb-4">Reject Transactions</h2>
                <p class="text-sm text-gray-600 mb-4">
                    Reject {{ pendingSelected }} pending transaction(s)
                </p>
                <form @submit.prevent="bulkReject" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Rejection *</label>
                        <textarea
                            v-model="bulkRejectForm.reason"
                            required
                            rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                            placeholder="e.g., Suspicious activity detected"
                        ></textarea>
                        <p v-if="bulkRejectForm.errors.reason" class="mt-1 text-sm text-red-600">
                            {{ bulkRejectForm.errors.reason }}
                        </p>
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <button
                            type="button"
                            @click="showBulkRejectModal = false"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="bulkRejectForm.processing"
                            class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 disabled:opacity-50"
                        >
                            {{ bulkRejectForm.processing ? 'Rejecting...' : 'Reject Transactions' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bulk Delete Modal -->
        <div
            v-if="showBulkDeleteModal"
            class="fixed inset-0 backdrop-blur-sm bg-black/30 z-50 flex items-center justify-center p-4"
            @click="showBulkDeleteModal = false"
        >
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6" @click.stop>
                <h2 class="text-xl font-bold text-gray-900 mb-4">Delete Transactions</h2>
                <p class="text-sm text-gray-600 mb-4">
                    Are you sure you want to delete {{ selectedTransactions.length }} transaction(s)?
                </p>
                <div class="text-sm text-red-800 bg-red-50 border border-red-200 rounded p-3 mb-4 space-y-2">
                    <p><strong>Warning:</strong> This action cannot be undone.</p>
                    <p>Only pending or cancelled transactions can be deleted. Completed transactions will be skipped.</p>
                </div>
                <div class="flex justify-end gap-3">
                    <button
                        @click="showBulkDeleteModal = false"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50"
                    >
                        Cancel
                    </button>
                    <button
                        @click="bulkDelete"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                    >
                        Delete Transactions
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
