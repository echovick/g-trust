<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
    LayoutDashboard,
    Users,
    Wallet,
    Settings,
    Bell,
    User,
    Menu,
    X,
    Landmark,
    LogOut,
    Shield,
    Receipt,
    Send,
    CreditCard,
    Banknote,
    FileText,
} from 'lucide-vue-next';

interface Props {
    title?: string;
}

const props = withDefaults(defineProps<Props>(), {
    title: 'Admin Dashboard',
});

const page = usePage();
const user = computed(() => page.props.auth?.user || { name: 'Admin' });

const showMobileMenu = ref(false);

const navItems = [
    { name: 'Dashboard', icon: LayoutDashboard, href: '/admin', current: 'dashboard' },
    { name: 'Users', icon: Users, href: '/admin/users', current: 'users' },
    { name: 'Accounts', icon: Wallet, href: '/admin/accounts', current: 'accounts' },
    { name: 'Transactions', icon: Receipt, href: '/admin/transactions', current: 'transactions' },
    { name: 'Transfers', icon: Send, href: '/admin/transfers', current: 'transfers' },
    { name: 'Cards', icon: CreditCard, href: '/admin/cards', current: 'cards' },
    { name: 'Loans', icon: Banknote, href: '/admin/loans', current: 'loans' },
    { name: 'Bill Payments', icon: FileText, href: '/admin/bill-payments', current: 'bill-payments' },
];

const isActive = (itemCurrent: string) => {
    const path = page.url;
    if (itemCurrent === 'dashboard') {
        return path === '/admin' || path === '/admin/';
    }
    return path.includes(`/admin/${itemCurrent}`);
};
</script>

<template>
    <Head :title="title">
        <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    </Head>

    <div class="min-h-screen bg-gray-50">
        <!-- Top Navigation -->
        <nav class="bg-gradient-to-r from-red-600 to-red-700 border-b border-red-800 sticky top-0 z-50 shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Logo and Mobile Menu Toggle -->
                    <div class="flex items-center">
                        <button
                            @click="showMobileMenu = !showMobileMenu"
                            class="lg:hidden p-2 rounded-md text-white hover:bg-red-800 mr-2"
                        >
                            <Menu v-if="!showMobileMenu" :size="24" />
                            <X v-else :size="24" />
                        </button>
                        <Link href="/admin" class="flex items-center gap-2">
                            <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center">
                                <Shield :size="24" class="text-red-600" />
                            </div>
                            <span class="text-xl font-bold text-white">Admin Panel</span>
                        </Link>
                    </div>

                    <!-- Right Side - User -->
                    <div class="flex items-center gap-3">
                        <!-- Notifications -->
                        <Link href="/admin/notifications" class="p-2 rounded-full hover:bg-red-800 relative">
                            <Bell :size="20" class="text-white" />
                        </Link>

                        <!-- User Menu -->
                        <div class="flex items-center gap-3 pl-3 border-l border-red-500">
                            <div class="hidden sm:block text-right">
                                <p class="text-sm font-medium text-white">{{ user.name }}</p>
                                <p class="text-xs text-red-200">Administrator</p>
                            </div>
                            <Link
                                href="/settings/profile"
                                class="w-9 h-9 bg-red-800 rounded-full flex items-center justify-center hover:bg-red-900"
                            >
                                <User :size="18" class="text-white" />
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 lg:py-8">
            <div class="flex flex-col lg:flex-row gap-4 lg:gap-8">
                <!-- Sidebar Navigation -->
                <aside class="hidden lg:block w-64 flex-shrink-0">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden sticky top-24">
                        <nav class="py-4">
                            <Link
                                v-for="item in navItems"
                                :key="item.name"
                                :href="item.href"
                                :class="[
                                    isActive(item.current)
                                        ? 'bg-red-50 text-red-700 border-r-4 border-red-600'
                                        : 'text-gray-700 hover:bg-gray-50',
                                    'flex items-center gap-3 px-6 py-3 text-sm font-medium transition-colors',
                                ]"
                            >
                                <component :is="item.icon" :size="20" />
                                {{ item.name }}
                            </Link>
                        </nav>
                        <div class="border-t border-gray-200 p-4">
                            <Link
                                href="/dashboard"
                                class="flex items-center gap-2 text-gray-600 hover:text-gray-900 text-sm"
                            >
                                <Landmark :size="18" />
                                Back to User Dashboard
                            </Link>
                        </div>
                    </div>
                </aside>

                <!-- Mobile Menu -->
                <div
                    v-if="showMobileMenu"
                    class="lg:hidden fixed inset-0 z-40 backdrop-blur-sm bg-black/30 pt-16"
                    @click="showMobileMenu = false"
                >
                    <div class="w-64 bg-white h-full shadow-xl" @click.stop>
                        <nav class="py-4">
                            <Link
                                v-for="item in navItems"
                                :key="item.name"
                                :href="item.href"
                                :class="[
                                    isActive(item.current)
                                        ? 'bg-red-50 text-red-700 border-r-4 border-red-600'
                                        : 'text-gray-700 hover:bg-gray-50',
                                    'flex items-center gap-3 px-6 py-3 text-sm font-medium',
                                ]"
                                @click="showMobileMenu = false"
                            >
                                <component :is="item.icon" :size="20" />
                                {{ item.name }}
                            </Link>
                        </nav>
                        <div class="border-t border-gray-200 p-4">
                            <Link
                                href="/dashboard"
                                class="flex items-center gap-2 text-gray-600 hover:text-gray-900 text-sm"
                                @click="showMobileMenu = false"
                            >
                                <Landmark :size="18" />
                                Back to User Dashboard
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <main class="flex-1 min-w-0">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
