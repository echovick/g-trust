<script setup lang="ts">
import { computed } from 'vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import {
    Users, Wallet, DollarSign, TrendingUp, Receipt, Send,
    Clock, AlertCircle, CheckCircle, ArrowUpRight, ArrowDownLeft
} from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';

interface Account {
    id: number;
    account_number: string;
    balance: number;
    currency: string;
    account_name: string;
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
    currency: string;
    transaction_type: string;
    transaction_date: string;
    status: string;
    account?: {
        account_number: string;
        user?: User;
    };
}

interface Transfer {
    id: number;
    amount: number;
    from_currency: string;
    description: string;
    status: string;
    created_at: string;
    fromAccount?: {
        account_number: string;
        user?: User;
    };
    toAccount?: {
        account_number: string;
        user?: User;
    };
}

interface Props {
    total_users: number;
    new_users_today: number;
    new_users_this_week: number;
    total_accounts: number;
    active_accounts: number;
    total_balance: number;
    total_available_balance: number;
    total_transactions: number;
    pending_transactions: number;
    today_transactions: number;
    today_transaction_volume: number;
    total_transfers: number;
    pending_transfers: number;
    today_transfers: number;
    today_transfer_volume: number;
    recent_transactions: Transaction[];
    recent_transfers: Transfer[];
    recent_users: User[];
    pending_approvals: {
        transactions: number;
        transfers: number;
    };
}

const props = defineProps<Props>();

const totalPendingApprovals = computed(() => {
    return props.pending_approvals.transactions + props.pending_approvals.transfers;
});

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
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Dashboard</h1>
                    <p class="text-gray-600 mt-1">Overview of your banking system</p>
                </div>
                <div v-if="totalPendingApprovals > 0" class="flex items-center gap-2 px-4 py-2 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <AlertCircle :size="20" class="text-yellow-600" />
                    <span class="text-sm font-medium text-yellow-800">{{ totalPendingApprovals }} pending approvals</span>
                </div>
            </div>

            <!-- Primary Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <Link href="/admin/users" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Total Users</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ total_users.toLocaleString() }}</p>
                            <p class="text-xs text-green-600 mt-1">+{{ new_users_today }} today</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <Users :size="24" class="text-blue-600" />
                        </div>
                    </div>
                </Link>

                <Link href="/admin/accounts" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Total Accounts</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ total_accounts.toLocaleString() }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ active_accounts }} active</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <Wallet :size="24" class="text-green-600" />
                        </div>
                    </div>
                </Link>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Total Balance</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">
                                {{ formatCurrency(total_balance) }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">{{ formatCurrency(total_available_balance) }} available</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                            <DollarSign :size="24" class="text-purple-600" />
                        </div>
                    </div>
                </div>

                <Link href="/admin/transactions" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Today's Volume</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">
                                {{ formatCurrency(today_transaction_volume) }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">{{ today_transactions }} transactions</p>
                        </div>
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                            <TrendingUp :size="24" class="text-red-600" />
                        </div>
                    </div>
                </Link>
            </div>

            <!-- Secondary Stats - Transactions & Transfers -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <Link href="/admin/transactions" class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-sm p-6 text-white hover:shadow-lg transition-shadow">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <p class="text-blue-100 text-sm">Transactions</p>
                            <p class="text-3xl font-bold mt-1">{{ total_transactions.toLocaleString() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                            <Receipt :size="24" />
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="flex items-center gap-2">
                            <Clock :size="16" />
                            {{ pending_transactions }} Pending
                        </span>
                        <span>{{ today_transactions }} Today</span>
                    </div>
                </Link>

                <Link href="/admin/transfers" class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-sm p-6 text-white hover:shadow-lg transition-shadow">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <p class="text-green-100 text-sm">Transfers</p>
                            <p class="text-3xl font-bold mt-1">{{ total_transfers.toLocaleString() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                            <Send :size="24" />
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="flex items-center gap-2">
                            <Clock :size="16" />
                            {{ pending_transfers }} Pending
                        </span>
                        <span>{{ formatCurrency(today_transfer_volume) }} Today</span>
                    </div>
                </Link>
            </div>

            <!-- Pending Approvals Alert -->
            <div v-if="totalPendingApprovals > 0" class="bg-yellow-50 border-2 border-yellow-200 rounded-lg p-6">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <AlertCircle :size="24" class="text-yellow-600" />
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Pending Approvals Required</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <Link v-if="pending_approvals.transactions > 0" href="/admin/transactions?status=pending" class="flex items-center justify-between p-3 bg-white rounded-lg border border-yellow-200 hover:border-yellow-300 transition-colors">
                                <span class="text-sm font-medium text-gray-900">Pending Transactions</span>
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-sm font-semibold rounded-full">{{ pending_approvals.transactions }}</span>
                            </Link>
                            <Link v-if="pending_approvals.transfers > 0" href="/admin/transfers?status=pending" class="flex items-center justify-between p-3 bg-white rounded-lg border border-yellow-200 hover:border-yellow-300 transition-colors">
                                <span class="text-sm font-medium text-gray-900">Pending Transfers</span>
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-sm font-semibold rounded-full">{{ pending_approvals.transfers }}</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
                <!-- Recent Transactions -->
                <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Recent Transactions</h2>
                        <Link href="/admin/transactions" class="text-sm text-red-600 hover:text-red-700 font-medium">
                            View All →
                        </Link>
                    </div>
                    <div class="divide-y divide-gray-200">
                        <Link
                            v-for="transaction in recent_transactions"
                            :key="transaction.id"
                            :href="`/admin/transactions/${transaction.id}`"
                            class="p-4 hover:bg-gray-50 block"
                        >
                            <div class="flex justify-between items-start">
                                <div class="flex items-start gap-3 flex-1">
                                    <div
                                        class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
                                        :class="transaction.transaction_type === 'credit' ? 'bg-green-50' : 'bg-red-50'"
                                    >
                                        <ArrowDownLeft
                                            v-if="transaction.transaction_type === 'credit'"
                                            :size="16"
                                            class="text-green-600"
                                        />
                                        <ArrowUpRight v-else :size="16" class="text-red-600" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ transaction.description }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ transaction.account?.user?.name }} - {{ transaction.account?.account_number }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p
                                        :class="[
                                            'text-sm font-semibold',
                                            transaction.transaction_type === 'credit'
                                                ? 'text-green-600'
                                                : 'text-red-600',
                                        ]"
                                    >
                                        {{ transaction.transaction_type === 'credit' ? '+' : '-' }}
                                        {{ transaction.currency }} {{ transaction.amount.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ formatDate(transaction.transaction_date) }}
                                    </p>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- Recent Users -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Recent Users</h2>
                        <Link href="/admin/users" class="text-sm text-red-600 hover:text-red-700 font-medium">
                            View All →
                        </Link>
                    </div>
                    <div class="divide-y divide-gray-200">
                        <Link
                            v-for="user in recent_users"
                            :key="user.id"
                            :href="`/admin/users/${user.id}`"
                            class="p-4 hover:bg-gray-50 block"
                        >
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <Users :size="20" class="text-purple-600" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ user.name }}</p>
                                    <p class="text-xs text-gray-500 mt-1 truncate">{{ user.email }}</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ user.accounts?.length || 0 }} accounts • {{ formatDate(user.created_at) }}
                                    </p>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Recent Transfers -->
            <div v-if="recent_transfers.length > 0" class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Transfers</h2>
                    <Link href="/admin/transfers" class="text-sm text-red-600 hover:text-red-700 font-medium">
                        View All →
                    </Link>
                </div>
                <div class="divide-y divide-gray-200">
                    <Link
                        v-for="transfer in recent_transfers"
                        :key="transfer.id"
                        :href="`/admin/transfers/${transfer.id}`"
                        class="p-4 hover:bg-gray-50 block"
                    >
                        <div class="flex justify-between items-start">
                            <div class="flex items-start gap-3 flex-1">
                                <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center flex-shrink-0">
                                    <Send :size="16" class="text-blue-600" />
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ transfer.description }}</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ transfer.fromAccount?.user?.name }} → {{ transfer.toAccount?.user?.name || 'External' }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-gray-900">
                                    {{ transfer.from_currency }} {{ transfer.amount.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">{{ formatDate(transfer.created_at) }}</p>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
