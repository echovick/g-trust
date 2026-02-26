<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link, router, useForm, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Search, Wallet, Trash2 } from 'lucide-vue-next';

interface User {
    id: number;
    name: string;
    email: string;
}

interface Account {
    id: number;
    account_number: string;
    account_name: string;
    account_type: string;
    currency: string;
    balance: number;
    available_balance: number;
    is_active: boolean;
    user: User;
}

interface Props {
    accounts: {
        data: Account[];
        links: any[];
        meta: {
            current_page: number;
            last_page: number;
            per_page: number;
            total: number;
        };
    };
    filters: {
        search?: string;
    };
}

const props = defineProps<Props>();
const page = usePage();

const flash = computed(() => (page.props.flash as any) ?? {});

const searchForm = useForm({
    search: props.filters.search || '',
});

const accountToDelete = ref<Account | null>(null);
const deleteProcessing = ref(false);

const searchAccounts = () => {
    router.get(
        '/admin/accounts',
        { search: searchForm.search },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
};

const confirmDelete = (account: Account) => {
    accountToDelete.value = account;
};

const deleteAccount = () => {
    if (!accountToDelete.value || deleteProcessing.value) return;
    deleteProcessing.value = true;
    router.delete(`/admin/accounts/${accountToDelete.value.id}`, {
        preserveScroll: true,
        onFinish: () => {
            deleteProcessing.value = false;
            accountToDelete.value = null;
        },
    });
};

const formatCurrency = (amount: number, currency: string) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency,
    }).format(amount);
};
</script>

<template>
    <AdminLayout title="Account Management">
        <div class="space-y-4 sm:space-y-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Accounts</h1>
                    <p class="text-gray-600 mt-1">Manage all bank accounts</p>
                </div>
                <Link
                    href="/admin/accounts/create"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                >
                    <Wallet :size="20" />
                    Create Account
                </Link>
            </div>

            <!-- Flash Messages -->
            <div v-if="flash.success" class="bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm">
                {{ flash.success }}
            </div>
            <div v-if="flash.error" class="bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 text-sm">
                {{ flash.error }}
            </div>

            <!-- Search Bar -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <form @submit.prevent="searchAccounts" class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1 relative">
                        <Search
                            :size="20"
                            class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"
                        />
                        <input
                            v-model="searchForm.search"
                            type="text"
                            placeholder="Search by account number, name, or user..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-gray-900"
                        />
                    </div>
                    <button
                        type="submit"
                        class="px-6 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition-colors whitespace-nowrap"
                    >
                        Search
                    </button>
                </form>
            </div>

            <!-- Accounts Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Account
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    User
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Type
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Balance
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="account in accounts?.data || []" :key="account.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-red-600 rounded-lg flex items-center justify-center">
                                            <Wallet :size="20" class="text-white" />
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ account.account_name }}</div>
                                            <div class="text-sm text-gray-500">{{ account.account_number }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ account.user.name }}</div>
                                    <div class="text-sm text-gray-500">{{ account.user.email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900 capitalize">{{ account.account_type }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ formatCurrency(account.balance, account.currency) }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Avail: {{ formatCurrency(account.available_balance, account.currency) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="[
                                            'px-2 py-1 text-xs rounded-full',
                                            account.is_active
                                                ? 'bg-green-100 text-green-800'
                                                : 'bg-red-100 text-red-800',
                                        ]"
                                    >
                                        {{ account.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-3">
                                        <Link
                                            :href="`/admin/accounts/${account.id}`"
                                            class="text-red-600 hover:text-red-900"
                                        >
                                            Manage
                                        </Link>
                                        <button
                                            @click="confirmDelete(account)"
                                            class="text-gray-400 hover:text-red-600 transition-colors"
                                            title="Delete account"
                                        >
                                            <Trash2 :size="16" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="accounts?.meta?.last_page > 1" class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing
                            <span class="font-medium">{{ ((accounts?.meta?.current_page || 1) - 1) * (accounts?.meta?.per_page || 20) + 1 }}</span>
                            to
                            <span class="font-medium">
                                {{ Math.min((accounts?.meta?.current_page || 1) * (accounts?.meta?.per_page || 20), accounts?.meta?.total || 0) }}
                            </span>
                            of
                            <span class="font-medium">{{ accounts?.meta?.total || 0 }}</span>
                            results
                        </div>
                        <div class="flex gap-2">
                            <Link
                                v-for="link in accounts?.links || []"
                                :key="link.label"
                                :href="link.url || '#'"
                                :class="[
                                    'px-3 py-1 rounded border',
                                    link.active
                                        ? 'bg-red-600 text-white border-red-600'
                                        : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50',
                                    !link.url ? 'opacity-50 cursor-not-allowed' : '',
                                ]"
                                v-html="link.label"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div
            v-if="accountToDelete"
            class="fixed inset-0 backdrop-blur-sm bg-black/30 z-50 flex items-center justify-center p-4"
            @click="accountToDelete = null"
        >
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6" @click.stop>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <Trash2 :size="20" class="text-red-600" />
                    </div>
                    <h2 class="text-lg font-semibold text-gray-900">Delete Account</h2>
                </div>
                <p class="text-sm text-gray-600 mb-2">
                    Are you sure you want to delete <span class="font-medium text-gray-900">{{ accountToDelete.account_name }}</span>?
                </p>
                <p class="text-sm text-gray-500 mb-4">Account number: {{ accountToDelete.account_number }}</p>
                <div class="text-sm text-amber-800 bg-amber-50 border border-amber-200 rounded p-3 mb-6 space-y-1">
                    <p><strong>This action cannot be undone.</strong></p>
                    <p>The account's current balance of <strong>{{ formatCurrency(accountToDelete.balance, accountToDelete.currency) }}</strong> will be permanently removed, along with all associated transaction history.</p>
                </div>
                <div class="flex justify-end gap-3">
                    <button
                        @click="accountToDelete = null"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                    >
                        Cancel
                    </button>
                    <button
                        @click="deleteAccount"
                        :disabled="deleteProcessing"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {{ deleteProcessing ? 'Deleting...' : 'Delete Account' }}
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
