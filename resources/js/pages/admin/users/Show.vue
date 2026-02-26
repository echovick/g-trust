<script setup lang="ts">
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { ArrowLeft, User, Mail, Calendar, Shield, Wallet, Lock, Pencil, Trash2, AlertCircle } from 'lucide-vue-next';

interface Account {
    id: number;
    account_number: string;
    account_type: string;
    currency: string;
    balance: number;
    available_balance: number;
    is_active: boolean;
}

interface UserData {
    id: number;
    name: string;
    email: string;
    is_admin: boolean;
    preferred_currency: string;
    created_at: string;
    accounts: Account[];
}

interface Props {
    user: UserData;
}

const props = defineProps<Props>();

const showPasswordReset = ref(false);
const showDeleteModal = ref(false);
const deleteInProgress = ref(false);

const deleteUser = () => {
    deleteInProgress.value = true;
    router.delete(`/admin/users/${props.user.id}`, {
        onFinish: () => {
            deleteInProgress.value = false;
            showDeleteModal.value = false;
        },
    });
};

const passwordForm = useForm({
    password: '',
    password_confirmation: '',
});

const resetPassword = () => {
    passwordForm.post(`/admin/users/${props.user.id}/reset-password`, {
        onSuccess: () => {
            passwordForm.reset();
            showPasswordReset.value = false;
        },
    });
};

const formatCurrency = (amount: number, currency: string) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency,
    }).format(amount);
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'long',
        day: 'numeric',
        year: 'numeric',
    });
};
</script>

<template>
    <AdminLayout :title="`User: ${user.name}`">
        <div class="space-y-4 sm:space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <a
                    href="/admin/users"
                    class="p-2 hover:bg-gray-100 rounded-lg transition-colors self-start"
                >
                    <ArrowLeft :size="20" class="text-gray-600" />
                </a>
                <div class="flex-1">
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">{{ user.name }}</h1>
                    <p class="text-gray-600 mt-1">User Details</p>
                </div>
                <div class="flex items-center gap-2">
                    <a
                        :href="`/admin/users/${user.id}/edit`"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                    >
                        <Pencil :size="18" />
                        <span class="hidden sm:inline">Edit User</span>
                        <span class="sm:hidden">Edit</span>
                    </a>
                    <button
                        @click="showPasswordReset = !showPasswordReset"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                    >
                        <Lock :size="18" />
                        <span class="hidden sm:inline">Reset Password</span>
                        <span class="sm:hidden">Reset</span>
                    </button>
                    <button
                        @click="showDeleteModal = true"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition-colors"
                    >
                        <Trash2 :size="18" />
                        <span class="hidden sm:inline">Delete</span>
                    </button>
                </div>
            </div>

            <!-- Password Reset Form -->
            <div v-if="showPasswordReset" class="bg-white rounded-lg shadow-sm border border-red-200 p-4 sm:p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Reset Password</h2>
                <form @submit.prevent="resetPassword" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            New Password *
                        </label>
                        <input
                            v-model="passwordForm.password"
                            type="password"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-gray-900"
                        />
                        <p v-if="passwordForm.errors.password" class="mt-1 text-sm text-red-600">
                            {{ passwordForm.errors.password }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Confirm New Password *
                        </label>
                        <input
                            v-model="passwordForm.password_confirmation"
                            type="password"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-gray-900"
                        />
                    </div>

                    <div class="flex justify-end gap-3">
                        <button
                            type="button"
                            @click="showPasswordReset = false"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="passwordForm.processing"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50"
                        >
                            {{ passwordForm.processing ? 'Resetting...' : 'Reset Password' }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- User Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">User Information</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <User :size="20" class="text-blue-600" />
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Full Name</p>
                            <p class="text-base font-medium text-gray-900">{{ user.name }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <Mail :size="20" class="text-green-600" />
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Email Address</p>
                            <p class="text-base font-medium text-gray-900">{{ user.email }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <Shield :size="20" class="text-purple-600" />
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Role</p>
                            <p class="text-base font-medium text-gray-900">
                                {{ user.is_admin ? 'Administrator' : 'User' }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <Calendar :size="20" class="text-yellow-600" />
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Member Since</p>
                            <p class="text-base font-medium text-gray-900">{{ formatDate(user.created_at) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Accounts -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Accounts</h2>
                </div>
                <div v-if="user.accounts.length > 0" class="divide-y divide-gray-200">
                    <div
                        v-for="account in user.accounts"
                        :key="account.id"
                        class="p-6 hover:bg-gray-50 transition-colors"
                    >
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-lg flex items-center justify-center">
                                    <Wallet :size="24" class="text-white" />
                                </div>
                                <div>
                                    <p class="text-base font-semibold text-gray-900 capitalize">
                                        {{ account.account_type }} Account
                                    </p>
                                    <p class="text-sm text-gray-500">{{ account.account_number }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-gray-900">
                                    {{ formatCurrency(account.balance, account.currency) }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    Available: {{ formatCurrency(account.available_balance, account.currency) }}
                                </p>
                                <span
                                    :class="[
                                        'inline-block mt-1 px-2 py-1 text-xs rounded-full',
                                        account.is_active
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-red-100 text-red-800',
                                    ]"
                                >
                                    {{ account.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else class="p-8 text-center text-gray-500">
                    <Wallet :size="48" class="mx-auto mb-3 text-gray-300" />
                    <p>No accounts found for this user</p>
                </div>
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
                        <h2 class="text-xl font-bold text-gray-900">Delete User</h2>
                        <p class="text-sm text-gray-600">This action cannot be undone</p>
                    </div>
                </div>

                <div class="mb-6">
                    <p class="text-sm text-gray-700 mb-3">
                        Are you sure you want to delete this user?
                    </p>
                    <div class="bg-gray-50 rounded-lg p-3 text-sm">
                        <p class="font-medium text-gray-900">{{ user.name }}</p>
                        <p class="text-gray-600">{{ user.email }}</p>
                    </div>
                    <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <p class="text-xs text-yellow-800">
                            <strong>Warning:</strong> This will permanently delete the user and all related records including accounts, transactions, cards, transfers, beneficiaries, loans, investments, deposits, and bill payments.
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
                        @click="deleteUser"
                        :disabled="deleteInProgress"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50"
                    >
                        {{ deleteInProgress ? 'Deleting...' : 'Delete User' }}
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
