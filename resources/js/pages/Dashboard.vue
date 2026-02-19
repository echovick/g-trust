<script setup lang="ts">
import { computed, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import {
    Wallet,
    TrendingUp,
    Send,
    ArrowDownToLine,
    DollarSign,
    PiggyBank,
    Building,
    Receipt,
    Target,
} from 'lucide-vue-next';
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import AccountCard from '@/components/dashboard/AccountCard.vue';
import TransactionRow from '@/components/dashboard/TransactionRow.vue';
import StatWidget from '@/components/dashboard/StatWidget.vue';
import CurrencyAmount from '@/components/dashboard/CurrencyAmount.vue';
import { useCurrency } from '@/composables/useCurrency';

interface Account {
    id: number;
    account_name: string;
    account_number: string;
    account_type: 'checking' | 'savings' | 'business';
    balance: number;
    available_balance: number;
    currency: 'USD' | 'EUR' | 'GBP';
    is_active: boolean;
    is_primary: boolean;
}

interface Transaction {
    id: number;
    description: string;
    amount: number;
    currency: 'USD' | 'EUR' | 'GBP';
    category?: string;
    transaction_date: string;
    transaction_type: string;
    status: 'pending' | 'completed' | 'failed' | 'cancelled';
    account?: Account;
}

interface Props {
    accounts?: Account[];
    recentTransactions?: Transaction[];
    totalBalance?: number;
    monthlySpending?: number;
    preferredCurrency?: 'USD' | 'EUR' | 'GBP';
}

const props = withDefaults(defineProps<Props>(), {
    accounts: () => [],
    recentTransactions: () => [],
    totalBalance: 0,
    monthlySpending: 0,
    preferredCurrency: 'USD',
});

const { setCurrency, formatCurrency, currentCurrency } = useCurrency();

// Set user's preferred currency on mount
onMounted(() => {
    if (props.preferredCurrency) {
        setCurrency(props.preferredCurrency);
    }
});

// Get account icon based on type
const getAccountIcon = (type: string) => {
    switch (type) {
        case 'checking':
            return Wallet;
        case 'savings':
            return PiggyBank;
        case 'business':
            return Building;
        default:
            return Wallet;
    }
};

// Add icons to accounts
const accountsWithIcons = computed(() =>
    (props.accounts || []).map(account => ({
        ...account,
        icon: getAccountIcon(account.account_type),
    }))
);

const quickActions = [
    {
        name: 'Transfer Money',
        icon: Send,
        color: 'bg-blue-500',
        href: '/dashboard/transfers/create',
    },
    {
        name: 'Pay Bills',
        icon: DollarSign,
        color: 'bg-green-500',
        href: '/dashboard/payments',
    },
    {
        name: 'Deposit Check',
        icon: ArrowDownToLine,
        color: 'bg-purple-500',
        href: '/dashboard/deposits',
    },
    {
        name: 'View Transactions',
        icon: Receipt,
        color: 'bg-orange-500',
        href: '/dashboard/transactions',
    },
];

// Calculate spending progress
const monthlyBudget = 4500;
const spendingPercentage = computed(() =>
    Math.min(Math.round((props.monthlySpending / monthlyBudget) * 100), 100)
);

// Savings goal (mock data for now)
const savingsGoal = {
    name: 'Vacation Fund',
    current: 7500,
    target: 10000,
};

const savingsPercentage = computed(() =>
    Math.round((savingsGoal.current / savingsGoal.target) * 100)
);
</script>

<template>
    <DashboardLayout title="Dashboard">
        <!-- Welcome Section -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Welcome back!</h1>
            <p class="text-gray-600">Here's what's happening with your accounts today.</p>
        </div>

        <!-- Summary Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <StatWidget
                title="Total Balance"
                :value="formatCurrency(totalBalance, currentCurrency)"
                :icon="Wallet"
                icon-color="text-red-500"
                icon-background="bg-red-50"
                :trend="{ value: 2.5, label: 'from last month' }"
            />
            <StatWidget
                title="Monthly Spending"
                :value="formatCurrency(monthlySpending, currentCurrency)"
                :icon="DollarSign"
                icon-color="text-blue-500"
                icon-background="bg-blue-50"
                :subtitle="`${spendingPercentage}% of budget`"
            />
            <StatWidget
                title="Active Accounts"
                :value="(accounts || []).length"
                :icon="Building"
                icon-color="text-green-500"
                icon-background="bg-green-50"
            />
        </div>

        <!-- Account Cards -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900">Your Accounts</h2>
                <Link
                    href="/dashboard/accounts"
                    class="text-sm text-red-500 hover:text-red-600 font-medium"
                >
                    View All →
                </Link>
            </div>

            <div v-if="accountsWithIcons.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <Link
                    v-for="account in accountsWithIcons"
                    :key="account.id"
                    :href="`/dashboard/accounts/${account.id}`"
                >
                    <AccountCard :account="account" />
                </Link>
            </div>

            <div v-else class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
                <Wallet :size="48" class="text-gray-300 mx-auto mb-4" />
                <p class="text-gray-600 mb-4">You don't have any accounts yet</p>
                <button class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-full font-medium transition-colors">
                    Open an Account
                </button>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Quick Actions</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <Link
                    v-for="action in quickActions"
                    :key="action.name"
                    :href="action.href"
                    class="flex flex-col items-center gap-3 p-4 rounded-xl hover:bg-gray-50 transition-colors group"
                >
                    <div :class="[
                        action.color,
                        'w-14 h-14 rounded-full flex items-center justify-center text-white group-hover:scale-110 transition-transform'
                    ]">
                        <component :is="action.icon" :size="24" />
                    </div>
                    <span class="text-sm font-medium text-gray-700 text-center">{{ action.name }}</span>
                </Link>
            </div>
        </div>

        <!-- Main Dashboard Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Recent Transactions -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Recent Transactions</h2>
                    <Link
                        href="/dashboard/transactions"
                        class="text-sm text-red-500 hover:text-red-600 font-medium"
                    >
                        View All →
                    </Link>
                </div>

                <div v-if="recentTransactions.length > 0" class="space-y-0">
                    <TransactionRow
                        v-for="transaction in recentTransactions"
                        :key="transaction.id"
                        :transaction="transaction"
                        @click="() => {}"
                    />
                </div>

                <div v-else class="text-center py-12">
                    <Receipt :size="48" class="text-gray-300 mx-auto mb-4" />
                    <p class="text-gray-600">No recent transactions</p>
                </div>
            </div>

            <!-- Financial Summary Sidebar -->
            <div class="space-y-6">
                <!-- Monthly Spending Widget -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Monthly Spending</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">This Month</span>
                            <span class="font-semibold text-gray-900">
                                <CurrencyAmount :amount="monthlySpending" />
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div
                                class="bg-red-500 h-2.5 rounded-full transition-all"
                                :style="{ width: `${spendingPercentage}%` }"
                            ></div>
                        </div>
                        <p class="text-xs text-gray-500">
                            {{ spendingPercentage }}% of monthly budget ({{ formatCurrency(monthlyBudget, currentCurrency) }})
                        </p>
                    </div>
                </div>

                <!-- Savings Goal Widget -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <Target :size="20" class="text-green-500" />
                        <h3 class="text-lg font-bold text-gray-900">Savings Goal</h3>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">{{ savingsGoal.name }}</span>
                            <span class="font-semibold text-gray-900">
                                <CurrencyAmount :amount="savingsGoal.current" /> / <CurrencyAmount :amount="savingsGoal.target" />
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div
                                class="bg-green-500 h-2.5 rounded-full transition-all"
                                :style="{ width: `${savingsPercentage}%` }"
                            ></div>
                        </div>
                        <p class="text-xs text-gray-500">
                            {{ savingsPercentage }}% complete • <CurrencyAmount :amount="savingsGoal.target - savingsGoal.current" /> remaining
                        </p>
                    </div>
                </div>

                <!-- Credit Score Widget -->
                <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center gap-2 mb-3">
                        <TrendingUp :size="20" />
                        <h3 class="text-lg font-bold">Credit Score</h3>
                    </div>
                    <div class="text-4xl font-bold mb-2">782</div>
                    <p class="text-blue-100 text-sm mb-4">Excellent • Updated today</p>
                    <a href="#" class="inline-flex items-center text-sm text-white font-medium hover:underline">
                        View Details →
                    </a>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>
