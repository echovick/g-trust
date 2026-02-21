<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Search, Wallet, ArrowRightLeft, DollarSign, Download, Trash2, Power, PowerOff, CheckSquare, Square } from 'lucide-vue-next';

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

const searchForm = useForm({
    search: props.filters.search || '',
});

const selectedAccounts = ref<number[]>([]);
const showTransferModal = ref(false);
const showBulkFundModal = ref(false);
const showBulkDeleteModal = ref(false);
const showBulkToggleModal = ref(false);
const bulkAction = ref<'activate' | 'deactivate'>('activate');

const bulkFundForm = useForm({
    account_ids: [] as number[],
    amount: 0,
    description: '',
});

const selectAll = computed({
    get: () => {
        return props.accounts?.data?.length > 0 &&
               selectedAccounts.value.length === props.accounts.data.length;
    },
    set: (value: boolean) => {
        if (value) {
            selectedAccounts.value = props.accounts.data.map(a => a.id);
        } else {
            selectedAccounts.value = [];
        }
    }
});

const toggleAccountSelection = (accountId: number) => {
    const index = selectedAccounts.value.indexOf(accountId);
    if (index > -1) {
        selectedAccounts.value.splice(index, 1);
    } else {
        selectedAccounts.value.push(accountId);
    }
};

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

const bulkToggleStatus = () => {
    router.post('/admin/accounts/bulk/toggle-status', {
        account_ids: selectedAccounts.value,
        is_active: bulkAction.value === 'activate',
    }, {
        onSuccess: () => {
            selectedAccounts.value = [];
            showBulkToggleModal.value = false;
        },
    });
};

const bulkFund = () => {
    bulkFundForm.account_ids = selectedAccounts.value;
    bulkFundForm.post('/admin/accounts/bulk/fund', {
        onSuccess: () => {
            selectedAccounts.value = [];
            showBulkFundModal.value = false;
            bulkFundForm.reset();
        },
    });
};

const bulkDelete = () => {
    if (confirm(`Are you sure you want to delete ${selectedAccounts.value.length} account(s)? This cannot be undone.`)) {
        router.post('/admin/accounts/bulk/destroy', {
            account_ids: selectedAccounts.value,
        }, {
            onSuccess: () => {
                selectedAccounts.value = [];
                showBulkDeleteModal.value = false;
            },
        });
    }
};

const bulkExport = () => {
    const params = new URLSearchParams();
    if (selectedAccounts.value.length > 0) {
        selectedAccounts.value.forEach(id => params.append('account_ids[]', id.toString()));
    }
    window.location.href = `/admin/accounts/bulk/export?${params.toString()}`;
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
                <div class="flex flex-col sm:flex-row gap-3">
                    <Link
                        href="/admin/accounts/create"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                    >
                        <Wallet :size="20" />
                        <span class="hidden sm:inline">Create Account</span>
                        <span class="sm:hidden">Create</span>
                    </Link>
                    <button
                        @click="showTransferModal = true"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                    >
                        <ArrowRightLeft :size="20" />
                        <span class="hidden sm:inline">Intra-Account Transfer</span>
                        <span class="sm:hidden">Transfer</span>
                    </button>
                </div>
            </div>

            <!-- Bulk Actions Bar -->
            <div v-if="selectedAccounts.length > 0" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <CheckSquare :size="20" class="text-blue-600" />
                        <span class="text-sm font-medium text-blue-900">
                            {{ selectedAccounts.length }} account(s) selected
                        </span>
                        <button
                            @click="selectedAccounts = []"
                            class="text-sm text-blue-600 hover:text-blue-800 underline"
                        >
                            Clear selection
                        </button>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button
                            @click="bulkAction = 'activate'; showBulkToggleModal = true"
                            class="inline-flex items-center gap-2 px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors"
                        >
                            <Power :size="16" />
                            Activate
                        </button>
                        <button
                            @click="bulkAction = 'deactivate'; showBulkToggleModal = true"
                            class="inline-flex items-center gap-2 px-3 py-2 bg-orange-600 text-white text-sm rounded-lg hover:bg-orange-700 transition-colors"
                        >
                            <PowerOff :size="16" />
                            Deactivate
                        </button>
                        <button
                            @click="showBulkFundModal = true"
                            class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors"
                        >
                            <DollarSign :size="16" />
                            Fund
                        </button>
                        <button
                            @click="bulkExport"
                            class="inline-flex items-center gap-2 px-3 py-2 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700 transition-colors"
                        >
                            <Download :size="16" />
                            Export
                        </button>
                        <button
                            @click="showBulkDeleteModal = true"
                            class="inline-flex items-center gap-2 px-3 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors"
                        >
                            <Trash2 :size="16" />
                            Delete
                        </button>
                    </div>
                </div>
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
                                <th class="px-6 py-3 text-left">
                                    <input
                                        type="checkbox"
                                        v-model="selectAll"
                                        class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500"
                                    />
                                </th>
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
                            <tr v-for="account in accounts?.data || []" :key="account.id"
                                class="hover:bg-gray-50"
                                :class="selectedAccounts.includes(account.id) ? 'bg-blue-50' : ''"
                            >
                                <td class="px-6 py-4">
                                    <input
                                        type="checkbox"
                                        :checked="selectedAccounts.includes(account.id)"
                                        @change="toggleAccountSelection(account.id)"
                                        class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500"
                                    />
                                </td>
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
                                    <Link
                                        :href="`/admin/accounts/${account.id}`"
                                        class="text-red-600 hover:text-red-900"
                                    >
                                        Manage
                                    </Link>
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

        <!-- Bulk Fund Modal -->
        <div
            v-if="showBulkFundModal"
            class="fixed inset-0 backdrop-blur-sm bg-black/30 z-50 flex items-center justify-center p-4"
            @click="showBulkFundModal = false"
        >
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6" @click.stop>
                <h2 class="text-xl font-bold text-gray-900 mb-4">Bulk Fund Accounts</h2>
                <p class="text-sm text-gray-600 mb-4">
                    Fund {{ selectedAccounts.length }} selected account(s) with the same amount
                </p>
                <form @submit.prevent="bulkFund" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Amount *</label>
                        <input
                            v-model="bulkFundForm.amount"
                            type="number"
                            step="0.01"
                            min="0.01"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                            placeholder="0.00"
                        />
                        <p v-if="bulkFundForm.errors.amount" class="mt-1 text-sm text-red-600">
                            {{ bulkFundForm.errors.amount }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                        <textarea
                            v-model="bulkFundForm.description"
                            required
                            rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                            placeholder="e.g., Monthly allowance deposit"
                        ></textarea>
                        <p v-if="bulkFundForm.errors.description" class="mt-1 text-sm text-red-600">
                            {{ bulkFundForm.errors.description }}
                        </p>
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <button
                            type="button"
                            @click="showBulkFundModal = false"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="bulkFundForm.processing"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
                        >
                            {{ bulkFundForm.processing ? 'Processing...' : 'Fund Accounts' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bulk Toggle Status Modal -->
        <div
            v-if="showBulkToggleModal"
            class="fixed inset-0 backdrop-blur-sm bg-black/30 z-50 flex items-center justify-center p-4"
            @click="showBulkToggleModal = false"
        >
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6" @click.stop>
                <h2 class="text-xl font-bold text-gray-900 mb-4">
                    {{ bulkAction === 'activate' ? 'Activate' : 'Deactivate' }} Accounts
                </h2>
                <p class="text-sm text-gray-600 mb-4">
                    Are you sure you want to {{ bulkAction }} {{ selectedAccounts.length }} account(s)?
                </p>
                <p class="text-sm text-yellow-800 bg-yellow-50 border border-yellow-200 rounded p-3 mb-4">
                    <strong>Note:</strong> Primary accounts cannot be deactivated and will be skipped.
                </p>
                <div class="flex justify-end gap-3">
                    <button
                        @click="showBulkToggleModal = false"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50"
                    >
                        Cancel
                    </button>
                    <button
                        @click="bulkToggleStatus"
                        :class="[
                            'px-4 py-2 text-white rounded-lg',
                            bulkAction === 'activate'
                                ? 'bg-green-600 hover:bg-green-700'
                                : 'bg-orange-600 hover:bg-orange-700'
                        ]"
                    >
                        {{ bulkAction === 'activate' ? 'Activate' : 'Deactivate' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Bulk Delete Modal -->
        <div
            v-if="showBulkDeleteModal"
            class="fixed inset-0 backdrop-blur-sm bg-black/30 z-50 flex items-center justify-center p-4"
            @click="showBulkDeleteModal = false"
        >
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6" @click.stop>
                <h2 class="text-xl font-bold text-gray-900 mb-4">Delete Accounts</h2>
                <p class="text-sm text-gray-600 mb-4">
                    Are you sure you want to delete {{ selectedAccounts.length }} account(s)?
                </p>
                <div class="text-sm text-red-800 bg-red-50 border border-red-200 rounded p-3 mb-4 space-y-2">
                    <p><strong>Warning:</strong> This action cannot be undone.</p>
                    <p>Accounts will be skipped if they:</p>
                    <ul class="list-disc list-inside ml-2 space-y-1">
                        <li>Have non-zero balance</li>
                        <li>Are set as primary</li>
                        <li>Have transactions in the last 30 days</li>
                    </ul>
                </div>
                <div class="flex justify-end gap-3">
                    <button
                        @click="showBulkDeleteModal = false"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50"
                    >
                        Cancel
                    </button>
                    <button
                        @click="bulkDelete"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                    >
                        Delete Accounts
                    </button>
                </div>
            </div>
        </div>

        <!-- Transfer Modal -->
        <div
            v-if="showTransferModal"
            class="fixed inset-0 backdrop-blur-sm bg-black/30 z-50 flex items-center justify-center p-4"
            @click="showTransferModal = false"
        >
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6" @click.stop>
                <h2 class="text-xl font-bold text-gray-900 mb-4">Intra-Account Transfer</h2>
                <p class="text-sm text-gray-600 mb-4">
                    Click on an account below to view transfer options
                </p>
                <button
                    @click="showTransferModal = false"
                    class="w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors"
                >
                    Close
                </button>
            </div>
        </div>
    </AdminLayout>
</template>
