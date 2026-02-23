<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Wallet, PiggyBank, Building, ArrowRight, CreditCard, TrendingUp, Plus } from 'lucide-vue-next';
import DashboardLayout from '@/layouts/DashboardLayout.vue';

interface Card {
    id: number;
    card_number: string;
    card_type: string;
    status: string;
}

interface Transaction {
    id: number;
    transaction_type: string;
    description: string;
    amount: number;
    transaction_date: string;
}

interface Account {
    id: number;
    account_name: string;
    account_number: string;
    account_type: 'checking' | 'savings' | 'business';
    balance: number;
    available_balance: number;
    currency: string;
    is_active: boolean;
    is_primary: boolean;
    cards?: Card[];
    transactions?: Transaction[];
}

interface Props {
    accounts: Account[];
}

const props = defineProps<Props>();

const getAccountIcon = (type: string) => {
    switch (type) {
        case 'checking': return Wallet;
        case 'savings': return PiggyBank;
        case 'business': return Building;
        default: return Wallet;
    }
};

const getAccountColor = (type: string) => {
    switch (type) {
        case 'checking': return 'from-blue-500 to-blue-600';
        case 'savings': return 'from-green-500 to-green-600';
        case 'business': return 'from-purple-500 to-purple-600';
        default: return 'from-gray-500 to-gray-600';
    }
};
</script>

<template>
    <DashboardLayout title="My Accounts">
        <div class="mb-8 flex items-center justify-between">
            <p class="text-gray-600">Manage your bank accounts and view balances</p>
            <Link
                href="/dashboard/account-requests/create"
                class="inline-flex items-center gap-2 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors"
            >
                <Plus :size="20" />
                <span class="hidden sm:inline">Request New Account</span>
                <span class="sm:hidden">Request</span>
            </Link>
        </div>

        <div v-if="accounts.length > 0" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <Link
                v-for="account in accounts"
                :key="account.id"
                :href="`/dashboard/accounts/${account.id}`"
                class="block bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all overflow-hidden group"
            >
                <!-- Account Header -->
                <div class="bg-gradient-to-r p-6 text-white" :class="getAccountColor(account.account_type)">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                                <component :is="getAccountIcon(account.account_type)" :size="24" />
                            </div>
                            <div>
                                <h3 class="text-xl font-bold">{{ account.account_name }}</h3>
                                <p class="text-white/90 text-sm">{{ account.account_number }}</p>
                            </div>
                        </div>
                        <span v-if="account.is_primary" class="px-3 py-1 bg-white/20 rounded-full text-xs font-medium">
                            Primary
                        </span>
                    </div>

                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-bold">
                            {{ account.currency }} {{ account.balance.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
                        </span>
                    </div>
                    <p class="text-white/80 text-sm mt-1">Current Balance</p>
                </div>

                <!-- Account Details -->
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <div class="text-sm text-gray-600 mb-1">Available Balance</div>
                            <div class="text-lg font-semibold text-gray-900">
                                {{ account.currency }} {{ account.available_balance.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
                            </div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600 mb-1">Account Type</div>
                            <div class="text-lg font-semibold text-gray-900 capitalize">{{ account.account_type }}</div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div v-if="account.cards || account.transactions" class="flex items-center gap-4 pt-4 border-t text-sm">
                        <div v-if="account.cards && account.cards.length > 0" class="flex items-center gap-1.5 text-gray-600">
                            <CreditCard :size="16" />
                            <span>{{ account.cards.length }} {{ account.cards.length === 1 ? 'Card' : 'Cards' }}</span>
                        </div>
                        <div v-if="account.transactions && account.transactions.length > 0" class="flex items-center gap-1.5 text-gray-600">
                            <TrendingUp :size="16" />
                            <span>{{ account.transactions.length }} Recent Transactions</span>
                        </div>
                        <div class="ml-auto flex items-center gap-2 text-red-500 font-medium group-hover:gap-3 transition-all">
                            View Details
                            <ArrowRight :size="16" />
                        </div>
                    </div>
                </div>
            </Link>
        </div>

        <!-- Empty State -->
        <div v-else class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <Wallet :size="32" class="text-gray-400" />
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No accounts yet</h3>
            <p class="text-gray-600 mb-6">Get started by requesting your first account</p>
            <Link
                href="/dashboard/account-requests/create"
                class="inline-flex items-center gap-2 px-6 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors"
            >
                <Plus :size="20" />
                Request New Account
            </Link>
        </div>
    </DashboardLayout>
</template>
