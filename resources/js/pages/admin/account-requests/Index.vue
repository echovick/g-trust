<script setup lang="ts">
import { ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Search, ClipboardList, Eye, CheckCircle, Clock, XCircle } from 'lucide-vue-next';

interface User {
    id: number;
    name: string;
    email: string;
}

interface Account {
    id: number;
    account_number: string;
}

interface AccountRequest {
    id: number;
    user: User;
    account_type: string;
    currency: string;
    account_name: string;
    purpose?: string;
    status: 'pending' | 'approved' | 'rejected';
    created_at: string;
    approved_at?: string;
    rejected_at?: string;
    createdAccount?: Account;
    approvedBy?: User;
}

interface Props {
    requests: {
        data: AccountRequest[];
        links: any[];
        meta: any;
    };
    stats: {
        total_requests: number;
        pending_requests: number;
        approved_requests: number;
        rejected_requests: number;
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
    searchForm.get('/admin/account-requests', {
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
        case 'approved': return 'bg-green-50 text-green-700';
        case 'pending': return 'bg-yellow-50 text-yellow-700';
        case 'rejected': return 'bg-red-50 text-red-700';
        default: return 'bg-gray-50 text-gray-700';
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
    <AdminLayout title="Account Requests">
        <div class="space-y-4 sm:space-y-6">
            <!-- Header -->
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Account Requests</h1>
                <p class="text-gray-600 mt-1">Review and approve new account requests</p>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Total Requests</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.total_requests }}</p>
                        </div>
                        <ClipboardList :size="24" class="text-gray-400" />
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Pending</p>
                            <p class="text-2xl font-bold text-yellow-600 mt-1">{{ stats.pending_requests }}</p>
                        </div>
                        <Clock :size="24" class="text-yellow-400" />
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Approved</p>
                            <p class="text-2xl font-bold text-green-600 mt-1">{{ stats.approved_requests }}</p>
                        </div>
                        <CheckCircle :size="24" class="text-green-400" />
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Rejected</p>
                            <p class="text-2xl font-bold text-red-600 mt-1">{{ stats.rejected_requests }}</p>
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
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                    >
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
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

            <!-- Requests Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    User
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Account Details
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Requested
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
                            <tr v-for="request in requests.data" :key="request.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ request.user.name }}</div>
                                    <div class="text-sm text-gray-500">{{ request.user.email }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ request.account_name }}</div>
                                    <div class="text-sm text-gray-500 capitalize">
                                        {{ request.account_type }} • {{ request.currency }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ formatDate(request.created_at) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="[
                                        'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium',
                                        getStatusColor(request.status)
                                    ]">
                                        <component :is="getStatusIcon(request.status)" :size="14" />
                                        {{ request.status.charAt(0).toUpperCase() + request.status.slice(1) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <Link
                                        :href="`/admin/account-requests/${request.id}`"
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

                <!-- Pagination -->
                <div v-if="requests.meta.last_page > 1" class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing
                            <span class="font-medium">{{ ((requests.meta.current_page || 1) - 1) * (requests.meta.per_page || 20) + 1 }}</span>
                            to
                            <span class="font-medium">
                                {{ Math.min((requests.meta.current_page || 1) * (requests.meta.per_page || 20), requests.meta.total || 0) }}
                            </span>
                            of
                            <span class="font-medium">{{ requests.meta.total || 0 }}</span>
                            results
                        </div>
                        <div class="flex gap-2">
                            <Link
                                v-for="link in requests.links"
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
