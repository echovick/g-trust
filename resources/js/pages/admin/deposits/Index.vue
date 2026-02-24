<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Search, ArrowDownToLine, Eye, CheckCircle, Clock, XCircle, AlertCircle } from 'lucide-vue-next';

interface User {
    id: number;
    name: string;
    email: string;
}

interface Account {
    id: number;
    account_number: string;
    account_name: string;
    user: User;
}

interface Deposit {
    id: number;
    account: Account;
    deposit_type: string;
    amount: string;
    currency: string;
    reference_number: string;
    check_number?: string;
    status: 'pending' | 'processing' | 'approved' | 'rejected' | 'completed';
    deposit_date: string;
    available_date?: string;
    created_at: string;
}

interface Props {
    deposits: {
        data: Deposit[];
        links: any[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
    stats: {
        total_deposits: number;
        pending_deposits: number;
        approved_deposits: number;
        rejected_deposits: number;
    };
    filters: {
        search?: string;
        status?: string;
    };
}

const props = defineProps<Props>();

const searchForm = useForm({
    search: props.filters.search || '',
    status: props.filters.status || '',
});

const search = () => {
    searchForm.get('/admin/deposits', {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    searchForm.reset();
    search();
};

const getStatusColor = (status: string) => {
    switch (status) {
        case 'approved':
        case 'completed':
            return 'bg-green-50 text-green-700';
        case 'pending':
            return 'bg-yellow-50 text-yellow-700';
        case 'processing':
            return 'bg-blue-50 text-blue-700';
        case 'rejected':
            return 'bg-red-50 text-red-700';
        default:
            return 'bg-gray-50 text-gray-700';
    }
};

const getStatusIcon = (status: string) => {
    switch (status) {
        case 'approved':
        case 'completed':
            return CheckCircle;
        case 'pending':
            return Clock;
        case 'processing':
            return AlertCircle;
        case 'rejected':
            return XCircle;
        default:
            return Clock;
    }
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

const formatCurrency = (amount: string, currency: string) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency,
    }).format(parseFloat(amount));
};
</script>

<template>
    <AdminLayout title="Deposits">
        <div class="space-y-4 sm:space-y-6">
            <!-- Header -->
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Deposits</h1>
                <p class="text-gray-600 mt-1">Review and manage check deposits</p>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Total Deposits</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.total_deposits }}</p>
                        </div>
                        <ArrowDownToLine :size="24" class="text-gray-400" />
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Pending</p>
                            <p class="text-2xl font-bold text-yellow-600 mt-1">{{ stats.pending_deposits }}</p>
                        </div>
                        <Clock :size="24" class="text-yellow-400" />
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Approved</p>
                            <p class="text-2xl font-bold text-green-600 mt-1">{{ stats.approved_deposits }}</p>
                        </div>
                        <CheckCircle :size="24" class="text-green-400" />
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Rejected</p>
                            <p class="text-2xl font-bold text-red-600 mt-1">{{ stats.rejected_deposits }}</p>
                        </div>
                        <XCircle :size="24" class="text-red-400" />
                    </div>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <form @submit.prevent="search" class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1 relative">
                        <Search :size="20" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
                        <input
                            v-model="searchForm.search"
                            type="text"
                            placeholder="Search by user name or email..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-gray-900"
                        />
                    </div>
                    <select
                        v-model="searchForm.status"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-gray-900"
                    >
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="completed">Completed</option>
                    </select>
                    <button
                        type="submit"
                        class="px-6 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition-colors whitespace-nowrap"
                    >
                        Search
                    </button>
                    <button
                        v-if="searchForm.search || searchForm.status"
                        type="button"
                        @click="clearFilters"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                    >
                        Clear
                    </button>
                </form>
            </div>

            <!-- Deposits Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    User
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Account
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Amount
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Reference
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="deposit in deposits.data" :key="deposit.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ deposit.account.user.name }}</div>
                                    <div class="text-sm text-gray-500">{{ deposit.account.user.email }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ deposit.account.account_number }}</div>
                                    <div class="text-sm text-gray-500">{{ deposit.account.account_name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ formatCurrency(deposit.amount, deposit.currency) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-mono text-gray-900">{{ deposit.reference_number }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ formatDate(deposit.deposit_date) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="[
                                        'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium',
                                        getStatusColor(deposit.status)
                                    ]">
                                        <component :is="getStatusIcon(deposit.status)" :size="14" />
                                        {{ deposit.status.charAt(0).toUpperCase() + deposit.status.slice(1) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <Link
                                        :href="`/admin/deposits/${deposit.id}`"
                                        class="inline-flex items-center gap-1 text-red-600 hover:text-red-900"
                                    >
                                        <Eye :size="16" />
                                        View
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div v-if="deposits.data.length === 0" class="p-12 text-center">
                    <ArrowDownToLine :size="48" class="text-gray-300 mx-auto mb-4" />
                    <p class="text-gray-500">No deposits found</p>
                </div>

                <!-- Pagination -->
                <div v-if="deposits.last_page > 1" class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing
                            <span class="font-medium">{{ ((deposits.current_page || 1) - 1) * (deposits.per_page || 20) + 1 }}</span>
                            to
                            <span class="font-medium">
                                {{ Math.min((deposits.current_page || 1) * (deposits.per_page || 20), deposits.total || 0) }}
                            </span>
                            of
                            <span class="font-medium">{{ deposits.total || 0 }}</span>
                            results
                        </div>
                        <div class="flex gap-2">
                            <Link
                                v-for="link in deposits.links"
                                :key="link.label"
                                :href="link.url || '#'"
                                :class="[
                                    'px-3 py-1 rounded border',
                                    link.active
                                        ? 'bg-red-600 text-white border-red-600'
                                        : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50',
                                    !link.url ? 'opacity-50 cursor-not-allowed' : '',
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
