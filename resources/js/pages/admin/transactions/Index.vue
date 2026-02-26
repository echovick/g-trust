<script setup lang="ts">
import { ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import {
    Search, Filter, ArrowUpRight, ArrowDownLeft,
    Receipt, TrendingUp, Clock, CheckCircle, XCircle
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

const showFilters = ref(false);

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

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Total Transactions</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ (stats?.total_transactions || 0).toLocaleString() }}</p>
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
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats?.pending_transactions || 0 }}</p>
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
                                ${{ (stats?.total_volume || 0).toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
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
                                ${{ (stats?.today_volume || 0).toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
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
                            <tr v-for="transaction in (transactions?.data || [])" :key="transaction.id"
                                class="hover:bg-gray-50"
                            >
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
                                    <div class="flex items-center justify-end gap-3">
                                        <Link
                                            :href="`/admin/transactions/${transaction.id}/edit`"
                                            class="text-gray-500 hover:text-gray-800 font-medium"
                                        >
                                            Edit
                                        </Link>
                                        <Link
                                            :href="`/admin/transactions/${transaction.id}`"
                                            class="text-red-600 hover:text-red-900 font-medium"
                                        >
                                            View
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="transactions?.meta?.last_page > 1" class="px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            Showing {{ ((transactions?.meta?.current_page - 1) * transactions?.meta?.per_page) + 1 }}
                            to {{ Math.min(transactions?.meta?.current_page * transactions?.meta?.per_page, transactions?.meta?.total) }}
                            of {{ transactions?.meta?.total }} transactions
                        </div>
                        <div class="flex gap-2">
                            <Link
                                v-for="link in (transactions?.links || [])"
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

    </AdminLayout>
</template>
