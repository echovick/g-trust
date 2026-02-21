<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Send, Plus, ArrowLeftRight, Globe, Calendar, CheckCircle, Clock, XCircle } from 'lucide-vue-next';
import DashboardLayout from '@/layouts/DashboardLayout.vue';

interface Account {
    id: number;
    account_name: string;
    account_number: string;
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
    currency: string;
    fee: number;
    exchange_rate?: number;
    description: string;
    reference_number: string;
    status: 'pending' | 'completed' | 'failed' | 'scheduled';
    scheduled_date?: string;
    completed_at?: string;
    fromAccount?: Account;
    toAccount?: Account;
    beneficiary?: Beneficiary;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedTransfers {
    data: Transfer[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: PaginationLink[];
}

interface Props {
    transfers: PaginatedTransfers;
}

const props = defineProps<Props>();

const getTransferIcon = (type: string) => {
    switch (type) {
        case 'internal': return ArrowLeftRight;
        case 'external': return Send;
        case 'international': return Globe;
        default: return Send;
    }
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

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};
</script>

<template>
    <DashboardLayout title="Transfers">
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600">View and manage your money transfers</p>
                </div>
                <Link
                    href="/dashboard/transfers/create"
                    class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center gap-2"
                >
                    <Plus :size="20" />
                    New Transfer
                </Link>
            </div>
        </div>

        <div v-if="transfers.data.length > 0" class="space-y-4">
            <div v-for="transfer in transfers.data" :key="transfer.id"
                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow"
            >
                <div class="flex items-start justify-between">
                    <div class="flex items-start gap-4 flex-1">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0"
                            :class="transfer.transfer_type === 'internal'
                                ? 'bg-blue-50'
                                : transfer.transfer_type === 'external'
                                    ? 'bg-green-50'
                                    : 'bg-purple-50'"
                        >
                            <component :is="getTransferIcon(transfer.transfer_type)" :size="24"
                                :class="transfer.transfer_type === 'internal'
                                    ? 'text-blue-500'
                                    : transfer.transfer_type === 'external'
                                        ? 'text-green-500'
                                        : 'text-purple-500'"
                            />
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
                                        {{ transfer.currency }} {{ transfer.amount.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
                                    </div>
                                    <div v-if="transfer.fee > 0" class="text-xs text-gray-500 mt-1">
                                        Fee: {{ transfer.currency }} {{ transfer.fee.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                <div>
                                    <div class="text-xs text-gray-500 mb-1">From</div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ transfer.fromAccount?.account_name || 'Unknown' }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ transfer.fromAccount?.account_number || '' }}
                                    </div>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-500 mb-1">To</div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ transfer.toAccount?.account_name || transfer.beneficiary?.name || 'Unknown' }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ transfer.toAccount?.account_number || transfer.beneficiary?.bank_name || '' }}
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 mt-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium"
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
                                </span>

                                <span class="text-xs text-gray-500 ml-auto">Ref: {{ transfer.reference_number }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="transfers.last_page > 1" class="bg-white rounded-xl shadow-sm border border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        Showing {{ ((transfers.current_page - 1) * transfers.per_page) + 1 }}
                        to {{ Math.min(transfers.current_page * transfers.per_page, transfers.total) }}
                        of {{ transfers.total }} transfers
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

        <!-- Empty State -->
        <div v-else class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <Send :size="32" class="text-gray-400" />
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No transfers yet</h3>
            <p class="text-gray-600 mb-6">You haven't made any transfers yet</p>
            <Link
                href="/dashboard/transfers/create"
                class="inline-flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg font-medium transition-colors"
            >
                <Plus :size="20" />
                Create Your First Transfer
            </Link>
        </div>
    </DashboardLayout>
</template>
