<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { Plus, Clock, CheckCircle, XCircle, ArrowDownToLine, Eye, AlertCircle } from 'lucide-vue-next';

interface Account {
    id: number;
    account_number: string;
    account_name: string;
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
    rejection_reason?: string;
    memo?: string;
    created_at: string;
}

interface PaginatedDeposits {
    data: Deposit[];
    links: any[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

interface Props {
    deposits: PaginatedDeposits;
}

const props = defineProps<Props>();

const getStatusColor = (status: string) => {
    switch (status) {
        case 'approved':
        case 'completed':
            return 'bg-green-100 text-green-800';
        case 'pending':
            return 'bg-yellow-100 text-yellow-800';
        case 'processing':
            return 'bg-blue-100 text-blue-800';
        case 'rejected':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
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
    <DashboardLayout title="Deposits">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <p class="text-gray-600">Mobile check deposit and direct deposits</p>
                <Link
                    href="/dashboard/deposits/create"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors"
                >
                    <Plus :size="20" />
                    <span class="hidden sm:inline">New Deposit</span>
                    <span class="sm:hidden">Deposit</span>
                </Link>
            </div>

            <!-- Deposits List -->
            <div v-if="deposits.data.length > 0" class="space-y-4">
                <div
                    v-for="deposit in deposits.data"
                    :key="deposit.id"
                    class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-12 h-12 bg-purple-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <ArrowDownToLine :size="24" class="text-purple-500" />
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        {{ formatCurrency(deposit.amount, deposit.currency) }}
                                    </h3>
                                    <p class="text-sm text-gray-600 capitalize">
                                        {{ deposit.deposit_type }} Deposit
                                    </p>
                                </div>
                            </div>

                            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Account</p>
                                    <p class="text-sm font-medium text-gray-900">{{ deposit.account.account_number }}</p>
                                    <p class="text-xs text-gray-600">{{ deposit.account.account_name }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Reference</p>
                                    <p class="text-sm font-medium text-gray-900 font-mono">{{ deposit.reference_number }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Deposited On</p>
                                    <p class="text-sm font-medium text-gray-900">{{ formatDate(deposit.deposit_date) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Status</p>
                                    <span :class="[
                                        'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium',
                                        getStatusColor(deposit.status)
                                    ]">
                                        <component :is="getStatusIcon(deposit.status)" :size="14" />
                                        {{ deposit.status.charAt(0).toUpperCase() + deposit.status.slice(1) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Approved Info -->
                            <div v-if="deposit.status === 'approved' || deposit.status === 'completed'" class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                                <p class="text-sm font-medium text-green-900 mb-1">Deposit Approved</p>
                                <p v-if="deposit.available_date" class="text-xs text-green-700">
                                    Funds available: {{ formatDate(deposit.available_date) }}
                                </p>
                            </div>

                            <!-- Rejected Info -->
                            <div v-if="deposit.status === 'rejected'" class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                                <p class="text-sm font-medium text-red-900 mb-1">Deposit Rejected</p>
                                <p v-if="deposit.rejection_reason" class="text-sm text-red-800">{{ deposit.rejection_reason }}</p>
                            </div>

                            <!-- Pending Info -->
                            <div v-if="deposit.status === 'pending'" class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <p class="text-sm font-medium text-yellow-900">Pending Review</p>
                                <p class="text-xs text-yellow-700 mt-1">Your deposit is being reviewed. This typically takes 1-2 business days.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <div class="w-16 h-16 bg-purple-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <ArrowDownToLine :size="32" class="text-purple-400" />
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No Deposits Yet</h3>
                <p class="text-gray-600 mb-6">You haven't made any check deposits</p>
                <Link
                    href="/dashboard/deposits/create"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors"
                >
                    <Plus :size="20" />
                    Deposit a Check
                </Link>
            </div>

            <!-- Pagination -->
            <div v-if="deposits.last_page > 1" class="flex justify-center gap-2">
                <Link
                    v-for="link in deposits.links"
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
    </DashboardLayout>
</template>
