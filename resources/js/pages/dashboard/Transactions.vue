<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import {
    Receipt, ArrowUpRight, ArrowDownLeft, Filter, Download,
    ShoppingBag, Utensils, Car, Home, CreditCard, TrendingUp
} from 'lucide-vue-next';
import DashboardLayout from '@/layouts/DashboardLayout.vue';

interface Account {
    id: number;
    account_name: string;
    account_number: string;
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
    merchant_name?: string;
    transaction_date: string;
    account?: Account;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedTransactions {
    data: Transaction[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: PaginationLink[];
}

interface Props {
    transactions: PaginatedTransactions;
}

const props = defineProps<Props>();

const selectedFilter = ref('all');

const getCategoryIcon = (category: string) => {
    switch (category) {
        case 'shopping': return ShoppingBag;
        case 'food_dining': return Utensils;
        case 'transportation': return Car;
        case 'bills_utilities': return Home;
        case 'transfer': return TrendingUp;
        default: return Receipt;
    }
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

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<template>
    <DashboardLayout title="Transactions">
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600">View all your account transactions</p>
                </div>
                <div class="flex gap-3">
                    <button class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        <Filter :size="18" />
                        Filter
                    </button>
                    <button class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        <Download :size="18" />
                        Export
                    </button>
                </div>
            </div>
        </div>

        <div v-if="transactions.data.length > 0" class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Date & Time
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Description
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Category
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Account
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Amount
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="transaction in transactions.data" :key="transaction.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ formatDate(transaction.transaction_date).split(',')[0] }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ formatDate(transaction.transaction_date).split(',')[1] }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0"
                                        :class="transaction.transaction_type === 'debit' ? 'bg-red-50' : 'bg-green-50'"
                                    >
                                        <component :is="getCategoryIcon(transaction.category)" :size="20"
                                            :class="transaction.transaction_type === 'debit' ? 'text-red-500' : 'text-green-500'"
                                        />
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ transaction.description }}</div>
                                        <div v-if="transaction.merchant_name" class="text-xs text-gray-500">
                                            {{ transaction.merchant_name }}
                                        </div>
                                        <div class="text-xs text-gray-400 mt-0.5">Ref: {{ transaction.reference_number }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-600 capitalize">
                                    {{ transaction.category.replace('_', ' ') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div v-if="transaction.account" class="text-sm">
                                    <div class="font-medium text-gray-900">{{ transaction.account.account_name }}</div>
                                    <div class="text-xs text-gray-500">{{ transaction.account.account_number }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="text-sm font-semibold"
                                    :class="transaction.transaction_type === 'debit' ? 'text-red-600' : 'text-green-600'"
                                >
                                    {{ transaction.transaction_type === 'debit' ? '-' : '+' }}{{ transaction.currency }}
                                    {{ transaction.amount.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    Balance: {{ transaction.currency }} {{ transaction.balance_after.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium"
                                    :class="getStatusColor(transaction.status)"
                                >
                                    {{ transaction.status.charAt(0).toUpperCase() + transaction.status.slice(1) }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="transactions.last_page > 1" class="px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        Showing {{ ((transactions.current_page - 1) * transactions.per_page) + 1 }}
                        to {{ Math.min(transactions.current_page * transactions.per_page, transactions.total) }}
                        of {{ transactions.total }} transactions
                    </div>
                    <div class="flex gap-2">
                        <Link
                            v-for="link in transactions.links"
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
                <Receipt :size="32" class="text-gray-400" />
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No transactions yet</h3>
            <p class="text-gray-600">Your transaction history will appear here</p>
        </div>
    </DashboardLayout>
</template>
