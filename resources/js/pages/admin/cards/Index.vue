<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { CreditCard, Search, Filter, X, Snowflake, Lock, Eye } from 'lucide-vue-next';

interface Card {
    id: number;
    card_number: string;
    card_holder_name: string;
    card_type: string;
    status: string;
    daily_limit: number;
    currency: string;
    expiry_date: string;
    contactless_enabled: boolean;
    online_transactions_enabled: boolean;
    international_transactions_enabled: boolean;
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
    cards: {
        data: Card[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
    stats: {
        total_cards: number;
        active_cards: number;
        frozen_cards: number;
        blocked_cards: number;
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
    searchForm.get('/admin/cards', {
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
        active: 'bg-green-100 text-green-800',
        frozen: 'bg-blue-100 text-blue-800',
        blocked: 'bg-red-100 text-red-800',
        lost: 'bg-gray-100 text-gray-800',
        expired: 'bg-yellow-100 text-yellow-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const getCardTypeColor = (type: string) => {
    const colors: Record<string, string> = {
        debit: 'bg-indigo-100 text-indigo-800',
        credit: 'bg-purple-100 text-purple-800',
        prepaid: 'bg-teal-100 text-teal-800',
    };
    return colors[type] || 'bg-gray-100 text-gray-800';
};

const maskCardNumber = (cardNumber: string) => {
    return cardNumber.replace(/\d(?=\d{4})/g, '*');
};

const hasActiveFilters = computed(() => {
    return searchForm.search || searchForm.type || searchForm.status;
});
</script>

<template>
    <Head title="Card Management" />

    <AdminLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Card Management</h1>
                    <p class="mt-1 text-sm text-gray-500">Manage and monitor all customer cards</p>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <CreditCard class="h-6 w-6 text-gray-400" />
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Cards</dt>
                                    <dd class="text-lg font-semibold text-gray-900">{{ stats.total_cards }}</dd>
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
                                    <dt class="text-sm font-medium text-gray-500 truncate">Active Cards</dt>
                                    <dd class="text-lg font-semibold text-gray-900">{{ stats.active_cards }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <Snowflake class="h-6 w-6 text-blue-400" />
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Frozen Cards</dt>
                                    <dd class="text-lg font-semibold text-gray-900">{{ stats.frozen_cards }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <Lock class="h-6 w-6 text-red-400" />
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Blocked Cards</dt>
                                    <dd class="text-lg font-semibold text-gray-900">{{ stats.blocked_cards }}</dd>
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
                                placeholder="Search by card number, holder name, or email..."
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
                            <label class="block text-sm font-medium text-gray-700 mb-1">Card Type</label>
                            <select
                                v-model="searchForm.type"
                                @change="search"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm rounded-md"
                            >
                                <option value="">All Types</option>
                                <option value="debit">Debit</option>
                                <option value="credit">Credit</option>
                                <option value="prepaid">Prepaid</option>
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
                                <option value="active">Active</option>
                                <option value="frozen">Frozen</option>
                                <option value="blocked">Blocked</option>
                                <option value="lost">Lost</option>
                                <option value="expired">Expired</option>
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

            <!-- Cards Table -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Card
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Account Holder
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Daily Limit
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Expiry
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="card in (cards?.data || [])" :key="card.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <CreditCard class="h-5 w-5 text-gray-400 mr-3" />
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ maskCardNumber(card.card_number) }}</div>
                                        <div class="text-sm text-gray-500">{{ card.card_holder_name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ card.account.user.name }}</div>
                                <div class="text-sm text-gray-500">{{ card.account.account_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="getCardTypeColor(card.card_type)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full uppercase">
                                    {{ card.card_type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="getStatusColor(card.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full uppercase">
                                    {{ card.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ card.currency }} {{ card.daily_limit.toLocaleString() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ new Date(card.expiry_date).toLocaleDateString() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <Link
                                    :href="`/admin/cards/${card.id}`"
                                    class="text-orange-600 hover:text-orange-900 inline-flex items-center"
                                >
                                    <Eye class="h-4 w-4 mr-1" />
                                    View
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="!cards || cards.data.length === 0">
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                No cards found
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div v-if="cards && cards.last_page > 1" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <button
                            :disabled="cards.current_page === 1"
                            @click="router.get(`/admin/cards?page=${cards.current_page - 1}`)"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
                        >
                            Previous
                        </button>
                        <button
                            :disabled="cards.current_page === cards.last_page"
                            @click="router.get(`/admin/cards?page=${cards.current_page + 1}`)"
                            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
                        >
                            Next
                        </button>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing
                                <span class="font-medium">{{ (cards.current_page - 1) * cards.per_page + 1 }}</span>
                                to
                                <span class="font-medium">{{ Math.min(cards.current_page * cards.per_page, cards.total) }}</span>
                                of
                                <span class="font-medium">{{ cards.total }}</span>
                                results
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <button
                                    :disabled="cards.current_page === 1"
                                    @click="router.get(`/admin/cards?page=${cards.current_page - 1}`)"
                                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50"
                                >
                                    Previous
                                </button>
                                <button
                                    :disabled="cards.current_page === cards.last_page"
                                    @click="router.get(`/admin/cards?page=${cards.current_page + 1}`)"
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
