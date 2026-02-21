<script setup lang="ts">
import { ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import {
    Search, Filter, Plus, Send, ArrowLeftRight, Globe,
    CheckCircle, Clock, XCircle, Calendar, TrendingUp
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

interface Beneficiary {
    id: number;
    name: string;
    bank_name: string;
}

interface Transfer {
    id: number;
    from_account_id: number;
    to_account_id?: number;
    beneficiary_id?: number;
    transfer_type: 'internal' | 'external' | 'international';
    amount: number;
    from_currency: string;
    to_currency: string;
    fee: number;
    reference_number: string;
    description: string;
    status: 'pending' | 'completed' | 'failed' | 'scheduled';
    scheduled_date?: string;
    completed_at?: string;
    created_at: string;
    fromAccount?: Account & { user?: User };
    toAccount?: Account & { user?: User };
    beneficiary?: Beneficiary;
}

interface Props {
    transfers: {
        data: Transfer[];
        links: any[];
        meta: any;
    };
    stats: {
        total_transfers: number;
        pending_transfers: number;
        total_volume: number;
        today_volume: number;
    };
    filters: {
        search?: string;
        type?: string;
        status?: string;
        date_from?: string;
        date_to?: string;
    };
}

const props = defineProps<Props>();

const searchForm = useForm({
    search: props.filters.search || '',
    type: props.filters.type || '',
    status: props.filters.status || '',
    date_from: props.filters.date_from || '',
    date_to: props.filters.date_to || '',
});

const showFilters = ref(false);

const searchTransfers = () => {
    router.get('/admin/transfers', searchForm.data(), {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    searchForm.reset();
    searchTransfers();
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
        case 'scheduled': return 'bg-blue-50 text-blue-700';
        case 'failed': return 'bg-red-50 text-red-700';
        default: return 'bg-gray-50 text-gray-700';
    }
};

const getStatusIcon = (status: string) => {
    switch (status) {
        case 'completed': return CheckCircle;
        case 'pending': return Clock;
        case 'scheduled': return Calendar;
        default: return XCircle;
    }
};

const getTypeIcon = (type: string) => {
    switch (type) {
        case 'internal': return ArrowLeftRight;
        case 'external': return Send;
        case 'international': return Globe;
        default: return Send;
    }
};

const getTypeColor = (type: string) => {
    switch (type) {
        case 'internal': return 'bg-blue-50 text-blue-600';
        case 'external': return 'bg-green-50 text-green-600';
        case 'international': return 'bg-purple-50 text-purple-600';
        default: return 'bg-gray-50 text-gray-600';
    }
};
</script>

<template>
    <AdminLayout title="Transfer Management">
        <div class="space-y-4 sm:space-y-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Transfers</h1>
                    <p class="text-gray-600 mt-1">Monitor and manage all money transfers</p>
                </div>
                <Link
                    href="/admin/transfers/create"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                >
                    <Plus :size="20" />
                    <span>New Transfer</span>
                </Link>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Total Transfers</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.total_transfers.toLocaleString() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <Send :size="24" class="text-blue-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Pending</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.pending_transfers }}</p>
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
                            <Send :size="24" class="text-purple-600" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <form @submit.prevent="searchTransfers" class="space-y-4">
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
                            <option value="internal">Internal</option>
                            <option value="external">External</option>
                            <option value="international">International</option>
                        </select>

                        <select
                            v-model="searchForm.status"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                        >
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                            <option value="scheduled">Scheduled</option>
                            <option value="failed">Failed</option>
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

            <!-- Transfers List -->
            <div class="space-y-4">
                <div
                    v-for="transfer in transfers.data"
                    :key="transfer.id"
                    class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow"
                >
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-4 flex-1">
                            <div
                                class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0"
                                :class="getTypeColor(transfer.transfer_type)"
                            >
                                <component :is="getTypeIcon(transfer.transfer_type)" :size="24" />
                            </div>

                            <div class="flex-1">
                                <div class="flex items-start justify-between mb-2">
                                    <div>
                                        <h3 class="font-semibold text-gray-900 text-lg capitalize">
                                            {{ transfer.transfer_type }} Transfer
                                        </h3>
                                        <p class="text-sm text-gray-600 mt-1">{{ transfer.description }}</p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-2xl font-bold text-gray-900">
                                            {{ formatCurrency(transfer.amount, transfer.from_currency) }}
                                        </div>
                                        <div v-if="transfer.fee > 0" class="text-xs text-gray-500 mt-1">
                                            Fee: {{ formatCurrency(transfer.fee, transfer.from_currency) }}
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                    <div>
                                        <div class="text-xs text-gray-500 mb-1">From</div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ transfer.fromAccount?.user?.name || 'Unknown' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ transfer.fromAccount?.account_number || '' }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-xs text-gray-500 mb-1">To</div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ transfer.toAccount?.user?.name || transfer.beneficiary?.name || 'Unknown' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ transfer.toAccount?.account_number || transfer.beneficiary?.bank_name || '' }}
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 mt-4">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium"
                                        :class="getStatusColor(transfer.status)"
                                    >
                                        <component :is="getStatusIcon(transfer.status)" :size="14" />
                                        {{ transfer.status.charAt(0).toUpperCase() + transfer.status.slice(1) }}
                                    </span>

                                    <span class="text-xs text-gray-500">
                                        <span v-if="transfer.scheduled_date">
                                            Scheduled: {{ formatDate(transfer.scheduled_date) }}
                                        </span>
                                        <span v-else-if="transfer.completed_at">
                                            Completed: {{ formatDate(transfer.completed_at) }}
                                        </span>
                                        <span v-else>
                                            Created: {{ formatDate(transfer.created_at) }}
                                        </span>
                                    </span>

                                    <span class="text-xs text-gray-500 ml-auto">Ref: {{ transfer.reference_number }}</span>

                                    <Link
                                        :href="`/admin/transfers/${transfer.id}`"
                                        class="text-red-600 hover:text-red-900 font-medium text-sm"
                                    >
                                        View Details
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="transfers.meta.last_page > 1" class="bg-white rounded-lg shadow-sm border border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        Showing {{ ((transfers.meta.current_page - 1) * transfers.meta.per_page) + 1 }}
                        to {{ Math.min(transfers.meta.current_page * transfers.meta.per_page, transfers.meta.total) }}
                        of {{ transfers.meta.total }} transfers
                    </div>
                    <div class="flex gap-2">
                        <Link
                            v-for="link in transfers.links"
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
    </AdminLayout>
</template>
