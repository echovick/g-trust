<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    Wallet,
    TrendingUp,
    Send,
    ArrowDownToLine,
    ArrowUpFromLine,
    DollarSign,
    PiggyBank,
    Building,
    Eye,
    EyeOff,
    Bell,
    User,
    LogOut,
    Settings,
    CreditCard,
    FileText,
    Menu,
    X,
    Landmark,
    Home,
    Receipt
} from 'lucide-vue-next';
import { ref } from 'vue';

const showBalance = ref(true);
const showMobileMenu = ref(false);

const accounts = [
    {
        name: 'Checking Account',
        number: '****4829',
        balance: 12450.50,
        type: 'checking',
        icon: Wallet,
    },
    {
        name: 'Savings Account',
        number: '****7391',
        balance: 45780.25,
        type: 'savings',
        icon: PiggyBank,
    },
    {
        name: 'Business Account',
        number: '****2156',
        balance: 28300.75,
        type: 'business',
        icon: Building,
    },
];

const recentTransactions = [
    {
        description: 'Amazon Purchase',
        date: 'Today, 2:30 PM',
        amount: -89.99,
        category: 'Shopping',
    },
    {
        description: 'Salary Deposit',
        date: 'Feb 15, 2026',
        amount: 5500.00,
        category: 'Income',
    },
    {
        description: 'Electric Bill',
        date: 'Feb 14, 2026',
        amount: -125.50,
        category: 'Utilities',
    },
    {
        description: 'Restaurant',
        date: 'Feb 13, 2026',
        amount: -56.30,
        category: 'Dining',
    },
    {
        description: 'Transfer to Savings',
        date: 'Feb 12, 2026',
        amount: -1000.00,
        category: 'Transfer',
    },
];

const quickActions = [
    { name: 'Transfer Money', icon: Send, color: 'bg-blue-500', href: '#' },
    { name: 'Pay Bills', icon: DollarSign, color: 'bg-green-500', href: '#' },
    { name: 'Deposit Check', icon: ArrowDownToLine, color: 'bg-purple-500', href: '#' },
    { name: 'Send Money', icon: ArrowUpFromLine, color: 'bg-orange-500', href: '#' },
];

const navItems = [
    { name: 'Dashboard', icon: Home, href: '/dashboard', active: true },
    { name: 'Accounts', icon: Wallet, href: '#', active: false },
    { name: 'Cards', icon: CreditCard, href: '/cards', active: false },
    { name: 'Transactions', icon: Receipt, href: '#', active: false },
    { name: 'Settings', icon: Settings, href: '#', active: false },
];
</script>

<template>
    <Head title="Dashboard">
        <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    </Head>

    <div class="min-h-screen bg-gray-50">
        <!-- Top Navigation Bar -->
        <nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Logo and Brand -->
                    <div class="flex items-center">
                        <button
                            @click="showMobileMenu = !showMobileMenu"
                            class="lg:hidden p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 mr-2"
                        >
                            <Menu v-if="!showMobileMenu" :size="24" />
                            <X v-else :size="24" />
                        </button>
                        <Link href="/" class="flex items-center gap-2">
                            <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center">
                                <Landmark :size="24" class="text-white" />
                            </div>
                            <span class="text-xl font-bold text-gray-900">G-Trust Bank</span>
                        </Link>
                    </div>

                    <!-- Desktop Navigation -->
                    <div class="hidden lg:flex items-center space-x-1">
                        <Link
                            v-for="item in navItems"
                            :key="item.name"
                            :href="item.href"
                            :class="[
                                'flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                                item.active
                                    ? 'bg-red-50 text-red-600'
                                    : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'
                            ]"
                        >
                            <component :is="item.icon" :size="18" />
                            {{ item.name }}
                        </Link>
                    </div>

                    <!-- Right Side Actions -->
                    <div class="flex items-center gap-3">
                        <button class="p-2 rounded-full hover:bg-gray-100 relative">
                            <Bell :size="20" class="text-gray-600" />
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                        <div class="flex items-center gap-3 pl-3 border-l">
                            <div class="hidden sm:block text-right">
                                <p class="text-sm font-medium text-gray-900">John Doe</p>
                                <p class="text-xs text-gray-500">Premium Member</p>
                            </div>
                            <button class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300 transition-colors">
                                <User :size="20" class="text-gray-700" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation -->
            <div v-if="showMobileMenu" class="lg:hidden border-t border-gray-200 bg-white">
                <div class="px-4 py-3 space-y-1">
                    <Link
                        v-for="item in navItems"
                        :key="item.name"
                        :href="item.href"
                        :class="[
                            'flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-colors',
                            item.active
                                ? 'bg-red-50 text-red-600'
                                : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'
                        ]"
                    >
                        <component :is="item.icon" :size="20" />
                        {{ item.name }}
                    </Link>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Welcome Section -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Welcome back, John!</h1>
                <p class="text-gray-600">Here's what's happening with your accounts today.</p>
            </div>

            <!-- Account Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div
                    v-for="account in accounts"
                    :key="account.number"
                    class="bg-gradient-to-br from-red-600 to-red-700 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-shadow"
                >
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                            <component :is="account.icon" :size="24" />
                        </div>
                        <button @click="showBalance = !showBalance" class="text-white/80 hover:text-white">
                            <component :is="showBalance ? Eye : EyeOff" :size="20" />
                        </button>
                    </div>
                    <p class="text-white/80 text-sm mb-1">{{ account.name }}</p>
                    <p class="text-white/60 text-xs mb-3">{{ account.number }}</p>
                    <div class="text-3xl font-bold">
                        {{ showBalance ? `$${account.balance.toLocaleString('en-US', { minimumFractionDigits: 2 })}` : '••••••' }}
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Quick Actions</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <a
                        v-for="action in quickActions"
                        :key="action.name"
                        :href="action.href"
                        class="flex flex-col items-center gap-3 p-4 rounded-xl hover:bg-gray-50 transition-colors group"
                    >
                        <div :class="[action.color, 'w-14 h-14 rounded-full flex items-center justify-center text-white group-hover:scale-110 transition-transform']">
                            <component :is="action.icon" :size="24" />
                        </div>
                        <span class="text-sm font-medium text-gray-700 text-center">{{ action.name }}</span>
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Recent Transactions -->
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-900">Recent Transactions</h2>
                        <a href="#" class="text-sm text-red-500 hover:text-red-600 font-medium">View All →</a>
                    </div>
                    <div class="space-y-4">
                        <div
                            v-for="(transaction, index) in recentTransactions"
                            :key="index"
                            class="flex items-center justify-between py-3 border-b last:border-0 hover:bg-gray-50 -mx-2 px-2 rounded-lg transition-colors"
                        >
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ transaction.description }}</p>
                                <p class="text-sm text-gray-500">{{ transaction.date }} • {{ transaction.category }}</p>
                            </div>
                            <div :class="[
                                'font-semibold text-lg',
                                transaction.amount > 0 ? 'text-green-600' : 'text-gray-900'
                            ]">
                                {{ transaction.amount > 0 ? '+' : '' }}${{ Math.abs(transaction.amount).toFixed(2) }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financial Summary -->
                <div class="space-y-6">
                    <!-- Monthly Spending -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Monthly Spending</h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">This Month</span>
                                <span class="font-semibold text-gray-900">$2,847.32</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-red-500 h-2.5 rounded-full" style="width: 65%"></div>
                            </div>
                            <p class="text-xs text-gray-500">65% of monthly budget ($4,500)</p>
                        </div>
                    </div>

                    <!-- Savings Goal -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Savings Goal</h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Vacation Fund</span>
                                <span class="font-semibold text-gray-900">$7,500 / $10,000</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-green-500 h-2.5 rounded-full" style="width: 75%"></div>
                            </div>
                            <p class="text-xs text-gray-500">75% complete • $2,500 remaining</p>
                        </div>
                    </div>

                    <!-- Credit Score -->
                    <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center gap-2 mb-3">
                            <TrendingUp :size="20" />
                            <h3 class="text-lg font-bold">Credit Score</h3>
                        </div>
                        <div class="text-4xl font-bold mb-2">782</div>
                        <p class="text-blue-100 text-sm">Excellent • Updated today</p>
                        <a href="#" class="inline-block mt-4 text-sm text-white font-medium hover:underline">
                            View Details →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
