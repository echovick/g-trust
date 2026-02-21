<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { ArrowLeft, Download, CreditCard, TrendingUp, Receipt, Calendar } from 'lucide-vue-next';
import DashboardLayout from '@/layouts/DashboardLayout.vue';

interface Card {
    id: number;
    card_number: string;
    card_type: string;
    status: string;
    expiry_date: string;
}

interface Transaction {
    id: number;
    transaction_type: 'debit' | 'credit';
    description: string;
    amount: number;
    currency: string;
    balance_after: number;
    transaction_date: string;
    category: string;
    status: string;
}

interface PaginatedTransactions {
    data: Transaction[];
    current_page?: number;
    last_page?: number;
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
    created_at: string;
    cards?: Card[];
    transactions?: PaginatedTransactions;
}

interface Props {
    account: Account;
}

const props = defineProps<Props>();

const showExportModal = ref(false);

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const getAccountTypeColor = (type: string) => {
    switch (type) {
        case 'checking': return 'from-blue-500 to-blue-600';
        case 'savings': return 'from-green-500 to-green-600';
        case 'business': return 'from-purple-500 to-purple-600';
        default: return 'from-gray-500 to-gray-600';
    }
};
</script>

<template>
    <DashboardLayout :title="`${account.account_name} Details`">
        <div class="mb-8">
            <button
                @click="router.visit('/dashboard/accounts')"
                class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-4 transition-colors"
            >
                <ArrowLeft :size="20" />
                Back to Accounts
            </button>
            <p class="text-gray-600">Account details and transaction history</p>
        </div>

        <!-- Account Summary Card -->
        <div class="bg-gradient-to-r rounded-xl shadow-lg p-8 mb-6 text-white" :class="getAccountTypeColor(account.account_type)">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold mb-2">{{ account.account_name }}</h2>
                    <p class="text-white/90">{{ account.account_number }}</p>
                </div>
                <div class="flex gap-2">
                    <span v-if="account.is_primary" class="px-3 py-1 bg-white/20 rounded-full text-xs font-medium">
                        Primary
                    </span>
                    <span class="px-3 py-1 bg-white/20 rounded-full text-xs font-medium capitalize">
                        {{ account.account_type }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <div class="text-white/80 text-sm mb-1">Current Balance</div>
                    <div class="text-3xl font-bold">
                        {{ account.currency }} {{ account.balance.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
                    </div>
                </div>
                <div>
                    <div class="text-white/80 text-sm mb-1">Available Balance</div>
                    <div class="text-2xl font-bold">
                        {{ account.currency }} {{ account.available_balance.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
                    </div>
                </div>
                <div>
                    <div class="text-white/80 text-sm mb-1">Account Since</div>
                    <div class="text-xl font-semibold">
                        {{ formatDate(account.created_at) }}
                    </div>
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button
                    @click="showExportModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg text-sm font-medium transition-colors"
                >
                    <Download :size="18" />
                    Export Statement
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Transactions -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Recent Transactions</h3>
                        <a href="/dashboard/transactions" class="text-sm font-medium text-red-500 hover:text-red-600">
                            View All →
                        </a>
                    </div>

                    <div v-if="account.transactions?.data && account.transactions.data.length > 0" class="space-y-3">
                        <div v-for="transaction in account.transactions.data" :key="transaction.id"
                            class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0"
                        >
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center"
                                    :class="transaction.transaction_type === 'debit' ? 'bg-red-50' : 'bg-green-50'"
                                >
                                    <Receipt :size="20"
                                        :class="transaction.transaction_type === 'debit' ? 'text-red-500' : 'text-green-500'"
                                    />
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ transaction.description }}</div>
                                    <div class="text-xs text-gray-500">{{ formatDate(transaction.transaction_date) }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-semibold"
                                    :class="transaction.transaction_type === 'debit' ? 'text-red-600' : 'text-green-600'"
                                >
                                    {{ transaction.transaction_type === 'debit' ? '-' : '+' }}{{ transaction.currency }}
                                    {{ transaction.amount.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    Balance: {{ transaction.currency }} {{ transaction.balance_after.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="text-center py-12">
                        <Receipt :size="48" class="text-gray-300 mx-auto mb-4" />
                        <p class="text-gray-600">No transactions yet</p>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Cards -->
                <div v-if="account.cards && account.cards.length > 0" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-gray-900">Cards</h3>
                        <a href="/dashboard/cards" class="text-sm font-medium text-red-500 hover:text-red-600">
                            Manage →
                        </a>
                    </div>
                    <div class="space-y-3">
                        <div v-for="card in account.cards" :key="card.id" class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                            <CreditCard :size="20" class="text-gray-600" />
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-900 capitalize">{{ card.card_type }} Card</div>
                                <div class="text-xs text-gray-500">•••• {{ card.card_number.slice(-4) }}</div>
                            </div>
                            <span class="text-xs px-2 py-1 rounded-full"
                                :class="card.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'"
                            >
                                {{ card.status }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="font-bold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-2">
                        <a href="/dashboard/transfers/create" class="flex items-center gap-3 p-3 bg-white rounded-lg hover:bg-gray-50 transition-colors">
                            <TrendingUp :size="20" class="text-blue-500" />
                            <span class="text-sm font-medium text-gray-900">Make Transfer</span>
                        </a>
                        <a href="/dashboard/bill-payments/create" class="flex items-center gap-3 p-3 bg-white rounded-lg hover:bg-gray-50 transition-colors">
                            <Calendar :size="20" class="text-green-500" />
                            <span class="text-sm font-medium text-gray-900">Pay Bills</span>
                        </a>
                        <button
                            @click="showExportModal = true"
                            class="w-full flex items-center gap-3 p-3 bg-white rounded-lg hover:bg-gray-50 transition-colors"
                        >
                            <Download :size="20" class="text-purple-500" />
                            <span class="text-sm font-medium text-gray-900">Export Statement</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Export Modal -->
        <div v-if="showExportModal" class="fixed inset-0 backdrop-blur-sm bg-black/30 z-50 flex items-center justify-center p-4"
            @click.self="showExportModal = false"
        >
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Export Statement</h3>
                <p class="text-sm text-gray-600 mb-6">This feature will be available soon. You'll be able to export your account statements in PDF or CSV format.</p>
                <button
                    @click="showExportModal = false"
                    class="w-full px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-medium transition-colors"
                >
                    Close
                </button>
            </div>
        </div>
    </DashboardLayout>
</template>
