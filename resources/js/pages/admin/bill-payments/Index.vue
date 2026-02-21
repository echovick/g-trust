<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Receipt, Search, Filter, X, Eye, Calendar, RefreshCw } from 'lucide-vue-next';

interface BillPayment {
    id: number;
    payee_name: string;
    payee_type: string;
    amount: number;
    currency: string;
    frequency: string;
    status: string;
    scheduled_date: string;
    next_payment_date?: string;
    reference_number: string;
    auto_pay: boolean;
    account: {
        id: number;
        account_number: string;
        account_name: string;
        user: {
            id: number;
            name: string;
            email: string;
        };
    };
}

interface Props {
    bill_payments: {
        data: BillPayment[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
    stats: {
        total_bill_payments: number;
        pending_payments: number;
        auto_pay_enabled: number;
        total_scheduled_amount: number;
    };
    filters: {
        search?: string;
        status?: string;
        frequency?: string;
        payee_type?: string;
    };
}

const props = defineProps<Props>();

const searchForm = useForm({
    search: props.filters.search || '',
    status: props.filters.status || '',
    frequency: props.filters.frequency || '',
    payee_type: props.filters.payee_type || '',
});

const showFilters = ref(false);

const search = () => {
    searchForm.get('/admin/bill-payments', {
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
        completed: 'bg-green-100 text-green-800',
        cancelled: 'bg-red-100 text-red-800',
        failed: 'bg-gray-100 text-gray-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const getFrequencyColor = (frequency: string) => {
    const colors: Record<string, string> = {
        once: 'bg-gray-100 text-gray-800',
        weekly: 'bg-blue-100 text-blue-800',
        monthly: 'bg-purple-100 text-purple-800',
        quarterly: 'bg-indigo-100 text-indigo-800',
        annually: 'bg-green-100 text-green-800',
    };
    return colors[frequency] || 'bg-gray-100 text-gray-800';
};

const hasActiveFilters = computed(() => {
    return searchForm.search || searchForm.status || searchForm.frequency || searchForm.payee_type;
});

const formatCurrency = (amount: number, currency: string) => {
    return `${currency} ${amount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
};
</script>

<template>
    <Head title="Bill Payment Management" />

    <AdminLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Bill Payment Management</h1>
                    <p class="mt-1 text-sm text-gray-500">Monitor and manage all bill payments</p>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <Receipt class="h-6 w-6 text-gray-400" />
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Payments</dt>
                                    <dd class="text-lg font-semibold text-gray-900">{{ stats?.total_bill_payments || 0 }}</dd>
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
                                    <dd class="text-lg font-semibold text-gray-900">{{ stats?.pending_payments || 0 }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <RefreshCw class="h-6 w-6 text-blue-400" />
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Auto-Pay Enabled</dt>
                                    <dd class="text-lg font-semibold text-gray-900">{{ stats?.auto_pay_enabled || 0 }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <Calendar class="h-6 w-6 text-green-400" />
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Scheduled Amount</dt>
                                    <dd class="text-sm font-semibold text-gray-900">{{ formatCurrency(stats?.total_scheduled_amount || 0, 'USD') }}</dd>
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
                                placeholder="Search by payee name, reference, or user..."
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
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select
                                v-model="searchForm.status"
                                @change="search"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm rounded-md"
                            >
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="failed">Failed</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Frequency</label>
                            <select
                                v-model="searchForm.frequency"
                                @change="search"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm rounded-md"
                            >
                                <option value="">All Frequencies</option>
                                <option value="once">One-Time</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                                <option value="quarterly">Quarterly</option>
                                <option value="annually">Annually</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Payee Type</label>
                            <select
                                v-model="searchForm.payee_type"
                                @change="search"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm rounded-md"
                            >
                                <option value="">All Types</option>
                                <option value="utility">Utility</option>
                                <option value="loan">Loan</option>
                                <option value="insurance">Insurance</option>
                                <option value="subscription">Subscription</option>
                                <option value="other">Other</option>
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

            <!-- Bill Payments Table -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Payee
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Account Holder
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Amount
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Frequency
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Scheduled Date
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Auto-Pay
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="payment in (bill_payments?.data || [])" :key="payment.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <Receipt class="h-5 w-5 text-gray-400 mr-3" />
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ payment.payee_name }}</div>
                                        <div class="text-sm text-gray-500 uppercase">{{ payment.payee_type }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ payment.account.user.name }}</div>
                                <div class="text-sm text-gray-500">{{ payment.account.account_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ formatCurrency(payment.amount, payment.currency) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="getFrequencyColor(payment.frequency)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full uppercase">
                                    {{ payment.frequency }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ new Date(payment.scheduled_date).toLocaleDateString() }}</div>
                                <div v-if="payment.next_payment_date" class="text-xs text-gray-500">
                                    Next: {{ new Date(payment.next_payment_date).toLocaleDateString() }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="getStatusColor(payment.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full uppercase">
                                    {{ payment.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span v-if="payment.auto_pay" class="text-green-600 text-sm">Enabled</span>
                                <span v-else class="text-gray-400 text-sm">Disabled</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <Link
                                    :href="`/admin/bill-payments/${payment.id}`"
                                    class="text-orange-600 hover:text-orange-900 inline-flex items-center"
                                >
                                    <Eye class="h-4 w-4 mr-1" />
                                    View
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="!bill_payments || bill_payments.data.length === 0">
                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                No bill payments found
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div v-if="bill_payments && bill_payments.last_page > 1" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <button
                            :disabled="bill_payments.current_page === 1"
                            @click="router.get(`/admin/bill-payments?page=${bill_payments.current_page - 1}`)"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
                        >
                            Previous
                        </button>
                        <button
                            :disabled="bill_payments.current_page === bill_payments.last_page"
                            @click="router.get(`/admin/bill-payments?page=${bill_payments.current_page + 1}`)"
                            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
                        >
                            Next
                        </button>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing
                                <span class="font-medium">{{ (bill_payments.current_page - 1) * bill_payments.per_page + 1 }}</span>
                                to
                                <span class="font-medium">{{ Math.min(bill_payments.current_page * bill_payments.per_page, bill_payments.total) }}</span>
                                of
                                <span class="font-medium">{{ bill_payments.total }}</span>
                                results
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <button
                                    :disabled="bill_payments.current_page === 1"
                                    @click="router.get(`/admin/bill-payments?page=${bill_payments.current_page - 1}`)"
                                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50"
                                >
                                    Previous
                                </button>
                                <button
                                    :disabled="bill_payments.current_page === bill_payments.last_page"
                                    @click="router.get(`/admin/bill-payments?page=${bill_payments.current_page + 1}`)"
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
