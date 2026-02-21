<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Banknote, Search, Filter, X, Eye, TrendingUp } from 'lucide-vue-next';

interface Loan {
    id: number;
    loan_number: string;
    loan_type: string;
    loan_amount: number;
    outstanding_balance: number;
    interest_rate: number;
    monthly_payment: number;
    status: string;
    currency: string;
    term_months: number;
    remaining_months: number;
    origination_date: string;
    maturity_date: string;
    next_payment_date: string;
    user: {
        id: number;
        name: string;
        email: string;
    };
    linkedAccount?: {
        id: number;
        account_number: string;
    };
}

interface Props {
    loans: {
        data: Loan[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
    stats: {
        total_loans: number;
        active_loans: number;
        pending_loans: number;
        total_outstanding: number;
        total_disbursed: number;
    };
    filters: {
        search?: string;
        type?: string;
        status?: string;
    };
}

const props = defineProps<Props>();

const searchForm = useForm({
    search: props.filters.search || '',
    type: props.filters.type || '',
    status: props.filters.status || '',
});

const showFilters = ref(false);

const search = () => {
    searchForm.get('/admin/loans', {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    searchForm.reset();
    search();
};

const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
        pending: 'bg-yellow-100 text-yellow-800',
        active: 'bg-green-100 text-green-800',
        paid_off: 'bg-blue-100 text-blue-800',
        defaulted: 'bg-red-100 text-red-800',
        cancelled: 'bg-gray-100 text-gray-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const getLoanTypeColor = (type: string) => {
    const colors: Record<string, string> = {
        personal: 'bg-purple-100 text-purple-800',
        mortgage: 'bg-indigo-100 text-indigo-800',
        auto: 'bg-blue-100 text-blue-800',
        business: 'bg-green-100 text-green-800',
        student: 'bg-yellow-100 text-yellow-800',
    };
    return colors[type] || 'bg-gray-100 text-gray-800';
};

const hasActiveFilters = computed(() => {
    return searchForm.search || searchForm.type || searchForm.status;
});

const formatCurrency = (amount: number, currency: string) => {
    return `${currency} ${amount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
};
</script>

<template>
    <Head title="Loan Management" />

    <AdminLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Loan Management</h1>
                    <p class="mt-1 text-sm text-gray-500">Manage and monitor all loans</p>
                </div>
                <Link
                    href="/admin/loans/create"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
                >
                    <Banknote class="h-4 w-4 mr-2" />
                    Create Loan
                </Link>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-5">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <Banknote class="h-6 w-6 text-gray-400" />
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Loans</dt>
                                    <dd class="text-lg font-semibold text-gray-900">{{ stats.total_loans }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="h-6 w-6 rounded-full bg-green-100 flex items-center justify-center">
                                    <div class="h-3 w-3 rounded-full bg-green-500"></div>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Active Loans</dt>
                                    <dd class="text-lg font-semibold text-gray-900">{{ stats.active_loans }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="h-6 w-6 rounded-full bg-yellow-100 flex items-center justify-center">
                                    <div class="h-3 w-3 rounded-full bg-yellow-500"></div>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Pending</dt>
                                    <dd class="text-lg font-semibold text-gray-900">{{ stats.pending_loans }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <TrendingUp class="h-6 w-6 text-red-400" />
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Outstanding</dt>
                                    <dd class="text-sm font-semibold text-gray-900">{{ formatCurrency(stats.total_outstanding, 'USD') }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <Banknote class="h-6 w-6 text-green-400" />
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Disbursed</dt>
                                    <dd class="text-sm font-semibold text-gray-900">{{ formatCurrency(stats.total_disbursed, 'USD') }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <Search class="h-5 w-5 text-gray-400" />
                            </div>
                            <input
                                v-model="searchForm.search"
                                @input="search"
                                type="text"
                                placeholder="Search by loan number, user name, or email..."
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                            />
                        </div>
                    </div>
                    <button
                        @click="showFilters = !showFilters"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
                    >
                        <Filter class="h-4 w-4 mr-2" />
                        Filters
                        <span v-if="hasActiveFilters" class="ml-2 bg-orange-100 text-orange-800 rounded-full px-2 py-0.5 text-xs">
                            Active
                        </span>
                    </button>
                </div>

                <!-- Filter Panel -->
                <div v-if="showFilters" class="mt-4 pt-4 border-t border-gray-200">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Loan Type</label>
                            <select
                                v-model="searchForm.type"
                                @change="search"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm rounded-md"
                            >
                                <option value="">All Types</option>
                                <option value="personal">Personal</option>
                                <option value="mortgage">Mortgage</option>
                                <option value="auto">Auto</option>
                                <option value="business">Business</option>
                                <option value="student">Student</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select
                                v-model="searchForm.status"
                                @change="search"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm rounded-md"
                            >
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="active">Active</option>
                                <option value="paid_off">Paid Off</option>
                                <option value="defaulted">Defaulted</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>

                        <div class="flex items-end">
                            <button
                                v-if="hasActiveFilters"
                                @click="clearFilters"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                            >
                                <X class="h-4 w-4 mr-2" />
                                Clear Filters
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loans Table -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Loan
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Borrower
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Amount
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Outstanding
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Monthly Payment
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="loan in (loans?.data || [])" :key="loan.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <Banknote class="h-5 w-5 text-gray-400 mr-3" />
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ loan.loan_number }}</div>
                                        <div class="text-sm text-gray-500">{{ loan.term_months }} months ({{ loan.remaining_months }} left)</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ loan.user.name }}</div>
                                <div class="text-sm text-gray-500">{{ loan.user.email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="getLoanTypeColor(loan.loan_type)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full uppercase">
                                    {{ loan.loan_type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ formatCurrency(loan.loan_amount, loan.currency) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ formatCurrency(loan.outstanding_balance, loan.currency) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ formatCurrency(loan.monthly_payment, loan.currency) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="getStatusColor(loan.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full uppercase">
                                    {{ loan.status.replace('_', ' ') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <Link
                                    :href="`/admin/loans/${loan.id}`"
                                    class="text-orange-600 hover:text-orange-900 inline-flex items-center"
                                >
                                    <Eye class="h-4 w-4 mr-1" />
                                    View
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="!loans || loans.data.length === 0">
                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                No loans found
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div v-if="loans && loans.last_page > 1" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <button
                            :disabled="loans.current_page === 1"
                            @click="router.get(`/admin/loans?page=${loans.current_page - 1}`)"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
                        >
                            Previous
                        </button>
                        <button
                            :disabled="loans.current_page === loans.last_page"
                            @click="router.get(`/admin/loans?page=${loans.current_page + 1}`)"
                            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
                        >
                            Next
                        </button>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing
                                <span class="font-medium">{{ (loans.current_page - 1) * loans.per_page + 1 }}</span>
                                to
                                <span class="font-medium">{{ Math.min(loans.current_page * loans.per_page, loans.total) }}</span>
                                of
                                <span class="font-medium">{{ loans.total }}</span>
                                results
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <button
                                    :disabled="loans.current_page === 1"
                                    @click="router.get(`/admin/loans?page=${loans.current_page - 1}`)"
                                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50"
                                >
                                    Previous
                                </button>
                                <button
                                    :disabled="loans.current_page === loans.last_page"
                                    @click="router.get(`/admin/loans?page=${loans.current_page + 1}`)"
                                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50"
                                >
                                    Next
                                </button>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
