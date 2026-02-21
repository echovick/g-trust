<script setup lang="ts">
import { computed } from 'vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Users, Wallet, DollarSign, TrendingUp } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';

interface Account {
    id: number;
    account_number: string;
    balance: number;
    currency: string;
}

interface User {
    id: number;
    name: string;
    email: string;
    created_at: string;
    accounts?: Account[];
}

interface Transaction {
    id: number;
    description: string;
    amount: number;
    transaction_type: string;
    transaction_date: string;
    account?: {
        account_number: string;
        user?: User;
    };
}

interface Props {
    total_users: number;
    total_accounts: number;
    total_balance: number;
    recent_transactions: Transaction[];
    recent_users: User[];
}

const props = defineProps<Props>();

const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(amount);
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });
};
</script>

<template>
    <AdminLayout title="Admin Dashboard">
        <div class="space-y-4 sm:space-y-6">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Dashboard</h1>
                <p class="text-gray-600 mt-1">Overview of your banking system</p>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Total Users</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ total_users }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <Users :size="24" class="text-blue-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Total Accounts</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ total_accounts }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <Wallet :size="24" class="text-green-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Total Balance</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">
                                {{ formatCurrency(total_balance) }}
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                            <DollarSign :size="24" class="text-purple-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Growth</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">+12.5%</p>
                        </div>
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                            <TrendingUp :size="24" class="text-red-600" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
                <!-- Recent Transactions -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Recent Transactions</h2>
                    </div>
                    <div class="divide-y divide-gray-200">
                        <div
                            v-for="transaction in recent_transactions"
                            :key="transaction.id"
                            class="p-4 hover:bg-gray-50"
                        >
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ transaction.description }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ transaction.account?.user?.name }} -
                                        {{ transaction.account?.account_number }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p
                                        :class="[
                                            'text-sm font-medium',
                                            transaction.transaction_type === 'credit'
                                                ? 'text-green-600'
                                                : 'text-red-600',
                                        ]"
                                    >
                                        {{ transaction.transaction_type === 'credit' ? '+' : '-' }}
                                        {{ formatCurrency(transaction.amount) }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ formatDate(transaction.transaction_date) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Users -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Recent Users</h2>
                    </div>
                    <div class="divide-y divide-gray-200">
                        <Link
                            v-for="user in recent_users"
                            :key="user.id"
                            :href="`/admin/users/${user.id}`"
                            class="p-4 hover:bg-gray-50 block"
                        >
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ user.name }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ user.email }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-500">
                                        {{ user.accounts?.length || 0 }} accounts
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ formatDate(user.created_at) }}
                                    </p>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
