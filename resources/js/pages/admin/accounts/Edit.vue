<script setup lang="ts">
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { ArrowLeft, Trash2, Check, AlertCircle } from 'lucide-vue-next';

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
    is_primary: boolean;
    user: User;
}

interface Props {
    account: Account;
}

const props = defineProps<Props>();

const form = useForm({
    account_name: props.account.account_name,
    account_type: props.account.account_type,
    is_active: props.account.is_active,
    is_primary: props.account.is_primary,
});

const showDeleteModal = ref(false);
const deleteInProgress = ref(false);

const submitForm = () => {
    form.put(`/admin/accounts/${props.account.id}`, {
        preserveScroll: true,
    });
};

const deleteAccount = () => {
    deleteInProgress.value = true;
    router.delete(`/admin/accounts/${props.account.id}`, {
        onFinish: () => {
            deleteInProgress.value = false;
            showDeleteModal.value = false;
        },
    });
};

const accountTypes = [
    { value: 'checking', label: 'Checking Account', description: 'For everyday transactions' },
    { value: 'savings', label: 'Savings Account', description: 'For saving and earning interest' },
    { value: 'business', label: 'Business Account', description: 'For business operations' },
];

const formatCurrency = (amount: number, currency: string) => {
    return `${currency} ${amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
};
</script>

<template>
    <AdminLayout :title="`Edit Account: ${account.account_number}`">
        <div class="space-y-6">
            <div class="flex items-center gap-4">
                <a
                    :href="`/admin/accounts/${account.id}`"
                    class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
                >
                    <ArrowLeft :size="20" class="text-gray-600" />
                </a>
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900">Edit Account</h1>
                    <p class="text-gray-600 mt-1">{{ account.account_number }}</p>
                </div>
                <button
                    @click="showDeleteModal = true"
                    class="px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition-colors flex items-center gap-2"
                >
                    <Trash2 :size="18" />
                    Delete Account
                </button>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 max-w-3xl">
                <!-- Account Holder Info (Read-only) -->
                <div class="mb-6 pb-6 border-b">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Account Holder</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm font-medium text-gray-900">{{ account.user.name }}</p>
                        <p class="text-sm text-gray-600">{{ account.user.email }}</p>
                        <p class="text-xs text-gray-500 mt-2">
                            Account Number: <span class="font-mono">{{ account.account_number }}</span>
                        </p>
                        <p class="text-xs text-gray-500">
                            Currency: <span class="font-medium">{{ account.currency }}</span> (cannot be changed)
                        </p>
                    </div>
                </div>

                <!-- Balance Info (Read-only) -->
                <div class="mb-6 pb-6 border-b">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Balance Information</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-green-50 rounded-lg p-4">
                            <p class="text-xs text-green-700 mb-1">Current Balance</p>
                            <p class="text-lg font-bold text-green-900">
                                {{ formatCurrency(account.balance, account.currency) }}
                            </p>
                        </div>
                        <div class="bg-blue-50 rounded-lg p-4">
                            <p class="text-xs text-blue-700 mb-1">Available Balance</p>
                            <p class="text-lg font-bold text-blue-900">
                                {{ formatCurrency(account.available_balance, account.currency) }}
                            </p>
                        </div>
                    </div>
                </div>

                <form @submit.prevent="submitForm" class="space-y-6">
                    <!-- Account Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Account Name *
                        </label>
                        <input
                            v-model="form.account_name"
                            type="text"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-gray-900"
                        />
                        <p v-if="form.errors.account_name" class="mt-1 text-sm text-red-600">
                            {{ form.errors.account_name }}
                        </p>
                    </div>

                    <!-- Account Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Account Type *
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <label
                                v-for="type in accountTypes"
                                :key="type.value"
                                class="relative flex flex-col p-4 border-2 rounded-lg cursor-pointer transition-all hover:border-red-300"
                                :class="form.account_type === type.value ? 'border-red-500 bg-red-50' : 'border-gray-200'"
                            >
                                <input
                                    v-model="form.account_type"
                                    type="radio"
                                    :value="type.value"
                                    class="sr-only"
                                />
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-medium text-gray-900 text-sm">{{ type.label }}</span>
                                    <div
                                        v-if="form.account_type === type.value"
                                        class="w-5 h-5 bg-red-500 rounded-full flex items-center justify-center"
                                    >
                                        <Check :size="14" class="text-white" />
                                    </div>
                                </div>
                                <span class="text-xs text-gray-600">{{ type.description }}</span>
                            </label>
                        </div>
                        <p v-if="form.errors.account_type" class="mt-1 text-sm text-red-600">
                            {{ form.errors.account_type }}
                        </p>
                    </div>

                    <!-- Active Status -->
                    <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg">
                        <input
                            v-model="form.is_active"
                            type="checkbox"
                            id="is_active"
                            class="mt-1 w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
                        />
                        <div class="flex-1">
                            <label for="is_active" class="text-sm font-medium text-gray-900 cursor-pointer">
                                Account Active
                            </label>
                            <p class="text-sm text-gray-600 mt-1">
                                Inactive accounts cannot be used for transactions. Primary accounts cannot be deactivated.
                            </p>
                        </div>
                    </div>

                    <!-- Primary Account -->
                    <div class="flex items-start gap-3 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                        <input
                            v-model="form.is_primary"
                            type="checkbox"
                            id="is_primary"
                            class="mt-1 w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500"
                        />
                        <div class="flex-1">
                            <label for="is_primary" class="text-sm font-medium text-gray-900 cursor-pointer flex items-center gap-2">
                                <span>Set as Primary Account</span>
                                <AlertCircle :size="16" class="text-yellow-600" />
                            </label>
                            <p class="text-sm text-gray-600 mt-1">
                                This will be the default account for the user. Setting this will automatically unset any other primary account.
                            </p>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center gap-3 pt-4 border-t">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 font-medium"
                        >
                            {{ form.processing ? 'Saving Changes...' : 'Save Changes' }}
                        </button>
                        <a
                            :href="`/admin/accounts/${account.id}`"
                            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium"
                        >
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div
            v-if="showDeleteModal"
            class="fixed inset-0 backdrop-blur-sm bg-black/30 z-50 flex items-center justify-center p-4"
            @click="showDeleteModal = false"
        >
            <div
                class="bg-white rounded-lg shadow-xl max-w-md w-full p-6"
                @click.stop
            >
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <AlertCircle :size="24" class="text-red-600" />
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Delete Account</h2>
                        <p class="text-sm text-gray-600">This action cannot be undone</p>
                    </div>
                </div>

                <div class="mb-6">
                    <p class="text-sm text-gray-700 mb-3">
                        Are you sure you want to delete this account?
                    </p>
                    <div class="bg-gray-50 rounded-lg p-3 text-sm">
                        <p class="font-medium text-gray-900">{{ account.account_name }}</p>
                        <p class="text-gray-600">{{ account.account_number }}</p>
                        <p class="text-gray-600 mt-2">
                            Balance: {{ formatCurrency(account.balance, account.currency) }}
                        </p>
                    </div>
                    <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <p class="text-xs text-yellow-800">
                            <strong>Note:</strong> You cannot delete accounts with non-zero balances,
                            primary accounts, or accounts with transactions in the last 30 days.
                        </p>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button
                        @click="showDeleteModal = false"
                        :disabled="deleteInProgress"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                    >
                        Cancel
                    </button>
                    <button
                        @click="deleteAccount"
                        :disabled="deleteInProgress"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50"
                    >
                        {{ deleteInProgress ? 'Deleting...' : 'Delete Account' }}
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
