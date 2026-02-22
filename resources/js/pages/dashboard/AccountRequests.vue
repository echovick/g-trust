<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { Plus, Clock, CheckCircle, XCircle, Wallet, Eye } from 'lucide-vue-next';

interface Account {
    id: number;
    account_number: string;
    account_name: string;
}

interface User {
    id: number;
    name: string;
}

interface AccountRequest {
    id: number;
    account_type: string;
    currency: string;
    account_name: string;
    purpose?: string;
    status: 'pending' | 'approved' | 'rejected';
    created_at: string;
    approved_at?: string;
    rejected_at?: string;
    rejection_reason?: string;
    createdAccount?: Account;
    approvedBy?: User;
}

interface PaginatedRequests {
    data: AccountRequest[];
    links: any[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

interface Props {
    requests: PaginatedRequests;
}

const props = defineProps<Props>();

const getStatusColor = (status: string) => {
    switch (status) {
        case 'approved': return 'bg-green-100 text-green-800';
        case 'pending': return 'bg-yellow-100 text-yellow-800';
        case 'rejected': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
};

const getStatusIcon = (status: string) => {
    switch (status) {
        case 'approved': return CheckCircle;
        case 'pending': return Clock;
        case 'rejected': return XCircle;
        default: return Clock;
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
</script>

<template>
    <DashboardLayout title="Account Requests">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <p class="text-gray-600">View and track your account requests</p>
                <Link
                    href="/dashboard/account-requests/create"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors"
                >
                    <Plus :size="20" />
                    <span class="hidden sm:inline">Request New Account</span>
                    <span class="sm:hidden">New Request</span>
                </Link>
            </div>

            <!-- Requests List -->
            <div v-if="requests.data.length > 0" class="space-y-4">
                <div
                    v-for="request in requests.data"
                    :key="request.id"
                    class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-12 h-12 bg-red-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <Wallet :size="24" class="text-red-500" />
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ request.account_name }}</h3>
                                    <p class="text-sm text-gray-600 capitalize">
                                        {{ request.account_type }} Account • {{ request.currency }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Requested On</p>
                                    <p class="text-sm font-medium text-gray-900">{{ formatDate(request.created_at) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Status</p>
                                    <span :class="[
                                        'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium',
                                        getStatusColor(request.status)
                                    ]">
                                        <component :is="getStatusIcon(request.status)" :size="14" />
                                        {{ request.status.charAt(0).toUpperCase() + request.status.slice(1) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Approved Info -->
                            <div v-if="request.status === 'approved' && request.createdAccount" class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                                <p class="text-sm font-medium text-green-900 mb-1">✓ Account Created Successfully</p>
                                <p class="text-sm text-green-800">
                                    Account Number: <span class="font-mono font-semibold">{{ request.createdAccount.account_number }}</span>
                                </p>
                                <p class="text-xs text-green-700 mt-1">Approved on {{ formatDate(request.approved_at!) }}</p>
                            </div>

                            <!-- Rejected Info -->
                            <div v-if="request.status === 'rejected'" class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                                <p class="text-sm font-medium text-red-900 mb-1">✗ Request Rejected</p>
                                <p v-if="request.rejection_reason" class="text-sm text-red-800">{{ request.rejection_reason }}</p>
                                <p class="text-xs text-red-700 mt-1">Rejected on {{ formatDate(request.rejected_at!) }}</p>
                            </div>

                            <!-- Pending Info -->
                            <div v-if="request.status === 'pending'" class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <p class="text-sm font-medium text-yellow-900">⏱ Pending Review</p>
                                <p class="text-xs text-yellow-700 mt-1">We'll notify you once your request has been reviewed</p>
                            </div>
                        </div>

                        <!-- View Details Button -->
                        <Link
                            :href="`/dashboard/account-requests/${request.id}`"
                            class="flex-shrink-0 p-2 hover:bg-gray-100 rounded-lg transition-colors"
                            title="View Details"
                        >
                            <Eye :size="20" class="text-gray-600" />
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <Wallet :size="32" class="text-gray-400" />
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No Account Requests Yet</h3>
                <p class="text-gray-600 mb-6">You haven't submitted any account requests</p>
                <Link
                    href="/dashboard/account-requests/create"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors"
                >
                    <Plus :size="20" />
                    Request New Account
                </Link>
            </div>

            <!-- Pagination -->
            <div v-if="requests.last_page > 1" class="flex justify-center gap-2">
                <Link
                    v-for="link in requests.links"
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
