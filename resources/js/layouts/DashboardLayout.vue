<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
    Home,
    Wallet,
    Receipt,
    Send,
    CreditCard,
    DollarSign,
    ArrowDownToLine,
    TrendingUp,
    PieChart,
    Settings,
    Bell,
    User,
    Menu,
    X,
    Landmark,
    LogOut,
    UserPlus,
} from 'lucide-vue-next';
import CurrencySelector from '@/components/dashboard/CurrencySelector.vue';

interface Props {
    title?: string;
    breadcrumbs?: Array<{ name: string; href?: string }>;
}

const props = withDefaults(defineProps<Props>(), {
    title: 'Dashboard',
});

const page = usePage();
const user = computed(() => page.props.auth?.user || { name: 'User', membership_tier: 'Standard' });
const unreadNotifications = computed(() => page.props.auth?.unreadNotifications || 0);

const showMobileMenu = ref(false);

const navItems = [
    { name: 'Dashboard', icon: Home, href: '/dashboard', current: 'dashboard' },
    { name: 'Accounts', icon: Wallet, href: '/dashboard/accounts', current: 'accounts' },
    { name: 'Transactions', icon: Receipt, href: '/dashboard/transactions', current: 'transactions' },
    { name: 'Transfers', icon: Send, href: '/dashboard/transfers', current: 'transfers' },
    { name: 'Beneficiaries', icon: UserPlus, href: '/dashboard/beneficiaries', current: 'beneficiaries' },
    { name: 'Payments', icon: DollarSign, href: '/dashboard/payments', current: 'payments' },
    { name: 'Cards', icon: CreditCard, href: '/dashboard/cards', current: 'cards' },
    { name: 'Deposits', icon: ArrowDownToLine, href: '/dashboard/deposits', current: 'deposits' },
    { name: 'Loans', icon: TrendingUp, href: '/dashboard/loans', current: 'loans' },
    { name: 'Investments', icon: PieChart, href: '/dashboard/investments', current: 'investments' },
];

const isActive = (itemCurrent: string) => {
    const currentRoute = page.url.split('/')[2] || 'dashboard';
    return currentRoute === itemCurrent;
};
</script>

<template>
    <Head :title="title">
        <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    </Head>

    <div class="min-h-screen bg-gray-50">
        <!-- Top Navigation -->
        <nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Logo and Mobile Menu Toggle -->
                    <div class="flex items-center">
                        <button
                            @click="showMobileMenu = !showMobileMenu"
                            class="lg:hidden p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 mr-2"
                        >
                            <Menu :size="24" />
                        </button>
                        <Link href="/" class="flex items-center gap-2">
                            <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center">
                                <Landmark :size="24" class="text-white" />
                            </div>
                            <span class="text-xl font-bold text-gray-900 hidden sm:block">G-Trust</span>
                        </Link>
                    </div>

                    <!-- Right Side - Currency, Notifications, User -->
                    <div class="flex items-center gap-3">
                        <!-- Currency Selector -->
                        <CurrencySelector />

                        <!-- Notifications -->
                        <Link href="/dashboard/notifications" class="p-2 rounded-full hover:bg-gray-100 relative">
                            <Bell :size="20" class="text-gray-600" />
                            <span v-if="unreadNotifications > 0" class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </Link>

                        <!-- User Menu -->
                        <div class="flex items-center gap-3 pl-3 border-l">
                            <div class="hidden sm:block text-right">
                                <p class="text-sm font-medium text-gray-900">{{ user.name }}</p>
                                <p class="text-xs text-gray-500">{{ user.membership_tier }} Member</p>
                            </div>
                            <Link
                                href="/dashboard/settings"
                                class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300 transition-colors"
                            >
                                <User :size="20" class="text-gray-700" />
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

        </nav>

        <!-- Mobile Navigation Drawer -->
        <Transition
            enter-active-class="transition-opacity duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-300"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showMobileMenu" class="fixed inset-0 bg-black/30 backdrop-blur-sm z-50 lg:hidden" @click="showMobileMenu = false"></div>
        </Transition>

        <Transition
            enter-active-class="transition-transform duration-300"
            enter-from-class="-translate-x-full"
            enter-to-class="translate-x-0"
            leave-active-class="transition-transform duration-300"
            leave-from-class="translate-x-0"
            leave-to-class="-translate-x-full"
        >
            <div v-if="showMobileMenu" class="fixed top-0 left-0 bottom-0 w-80 bg-white z-50 lg:hidden shadow-xl overflow-y-auto">
                <div class="p-4">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-6">
                        <Link href="/" class="flex items-center gap-2">
                            <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center">
                                <Landmark :size="24" class="text-white" />
                            </div>
                            <span class="text-xl font-bold text-gray-900">G-Trust</span>
                        </Link>
                        <button
                            @click="showMobileMenu = false"
                            class="p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100"
                        >
                            <X :size="24" />
                        </button>
                    </div>

                    <!-- User Info -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center">
                                <User :size="24" class="text-white" />
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ user.name }}</p>
                                <p class="text-xs text-gray-500">{{ user.membership_tier }} Member</p>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <nav class="space-y-1">
                        <Link
                            v-for="item in navItems"
                            :key="item.name"
                            :href="item.href"
                            @click="showMobileMenu = false"
                            :class="[
                                'flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-colors',
                                isActive(item.current)
                                    ? 'bg-red-50 text-red-600'
                                    : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'
                            ]"
                        >
                            <component :is="item.icon" :size="20" />
                            {{ item.name }}
                        </Link>

                        <!-- Divider -->
                        <div class="border-t border-gray-200 my-4"></div>

                        <!-- Settings and Logout -->
                        <Link
                            href="/dashboard/settings"
                            @click="showMobileMenu = false"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors"
                        >
                            <Settings :size="20" />
                            Settings
                        </Link>
                        <Link
                            href="/logout"
                            method="post"
                            as="button"
                            class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors"
                        >
                            <LogOut :size="20" />
                            Logout
                        </Link>
                    </nav>
                </div>
            </div>
        </Transition>

        <!-- Main Layout with Sidebar -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="lg:grid lg:grid-cols-12 lg:gap-8">
                <!-- Desktop Sidebar -->
                <aside class="hidden lg:block lg:col-span-3">
                    <nav class="space-y-1 sticky top-24">
                        <Link
                            v-for="item in navItems"
                            :key="item.name"
                            :href="item.href"
                            :class="[
                                'flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-colors',
                                isActive(item.current)
                                    ? 'bg-red-50 text-red-600'
                                    : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'
                            ]"
                        >
                            <component :is="item.icon" :size="20" />
                            {{ item.name }}
                        </Link>

                        <!-- Divider -->
                        <div class="border-t border-gray-200 my-4"></div>

                        <!-- Settings -->
                        <Link
                            href="/dashboard/settings"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors"
                        >
                            <Settings :size="20" />
                            Settings
                        </Link>

                        <!-- Logout -->
                        <Link
                            href="/logout"
                            method="post"
                            as="button"
                            class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors"
                        >
                            <LogOut :size="20" />
                            Logout
                        </Link>
                    </nav>
                </aside>

                <!-- Main Content -->
                <main class="lg:col-span-9">
                    <!-- Breadcrumbs -->
                    <nav v-if="breadcrumbs && breadcrumbs.length > 0" class="mb-6" aria-label="Breadcrumb">
                        <ol class="flex items-center space-x-2 text-sm text-gray-500">
                            <li v-for="(crumb, index) in breadcrumbs" :key="index" class="flex items-center">
                                <Link
                                    v-if="crumb.href"
                                    :href="crumb.href"
                                    class="hover:text-gray-700"
                                >
                                    {{ crumb.name }}
                                </Link>
                                <span v-else class="text-gray-900 font-medium">{{ crumb.name }}</span>
                                <span v-if="index < breadcrumbs.length - 1" class="mx-2">/</span>
                            </li>
                        </ol>
                    </nav>

                    <!-- Page Title -->
                    <div class="mb-8">
                        <h1 class="text-3xl font-bold text-gray-900">{{ title }}</h1>
                    </div>

                    <!-- Page Content -->
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
