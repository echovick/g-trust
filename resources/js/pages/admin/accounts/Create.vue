<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { ArrowLeft, User, Wallet, DollarSign, Check } from 'lucide-vue-next';

interface User {
    id: number;
    name: string;
    email: string;
}

interface Props {
    users: User[];
}

const props = defineProps<Props>();

const form = useForm({
    user_id: '',
    account_type: 'checking',
    currency: 'USD',
    account_name: '',
    initial_balance: 0,
    is_primary: false,
});

const selectedUser = computed(() => {
    if (!form.user_id) return null;
    return props.users.find(u => u.id === parseInt(form.user_id as string));
});

// Auto-fill account name based on user and account type
const autoFillAccountName = () => {
    if (selectedUser.value && form.account_type) {
        const typeName = form.account_type.charAt(0).toUpperCase() + form.account_type.slice(1);
        form.account_name = `${selectedUser.value.name} ${typeName} Account`;
    }
};

const submitForm = () => {
    form.post('/admin/accounts', {
        onSuccess: () => {
            form.reset();
        },
    });
};

const currencies = [
    { code: 'USD', name: 'US Dollar' },
    { code: 'EUR', name: 'Euro' },
    { code: 'GBP', name: 'British Pound' },
    { code: 'NGN', name: 'Nigerian Naira' },
];

const accountTypes = [
    { value: 'checking', label: 'Checking Account', description: 'For everyday transactions' },
    { value: 'savings', label: 'Savings Account', description: 'For saving and earning interest' },
    { value: 'business', label: 'Business Account', description: 'For business operations' },
];
</script>

<template>
    <AdminLayout title="Create Account">
        <div class="space-y-6">
            <div class="flex items-center gap-4">
                <a
                    href="/admin/accounts"
                    class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
                >
                    <ArrowLeft :size="20" class="text-gray-600" />
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Create New Account</h1>
                    <p class="text-gray-600 mt-1">Create a new bank account for a user</p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 max-w-3xl">
                <form @submit.prevent="submitForm" class="space-y-6">
                    <!-- User Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Account Holder *
                        </label>
                        <select
                            v-model="form.user_id"
                            @change="autoFillAccountName"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-gray-900"
                        >
                            <option value="" disabled>Select a user...</option>
                            <option v-for="user in users" :key="user.id" :value="user.id">
                                {{ user.name }} ({{ user.email }})
                            </option>
                        </select>
                        <p v-if="form.errors.user_id" class="mt-1 text-sm text-red-600">
                            {{ form.errors.user_id }}
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
                                    @change="autoFillAccountName"
                                    class="sr-only"
                                />
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-medium text-gray-900">{{ type.label }}</span>
                                    <div
                                        v-if="form.account_type === type.value"
                                        class="w-5 h-5 bg-red-500 rounded-full flex items-center justify-center"
                                    >
                                        <Check :size="14" class="text-white" />
                                    </div>
                                </div>
                                <span class="text-sm text-gray-600">{{ type.description }}</span>
                            </label>
                        </div>
                        <p v-if="form.errors.account_type" class="mt-1 text-sm text-red-600">
                            {{ form.errors.account_type }}
                        </p>
                    </div>

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
                            placeholder="e.g., John Doe Checking Account"
                        />
                        <p v-if="form.errors.account_name" class="mt-1 text-sm text-red-600">
                            {{ form.errors.account_name }}
                        </p>
                    </div>

                    <!-- Currency -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Currency *
                        </label>
                        <select
                            v-model="form.currency"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-gray-900"
                        >
                            <option v-for="currency in currencies" :key="currency.code" :value="currency.code">
                                {{ currency.code }} - {{ currency.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.currency" class="mt-1 text-sm text-red-600">
                            {{ form.errors.currency }}
                        </p>
                        <p class="mt-1 text-sm text-gray-500">
                            Note: Currency cannot be changed after account creation
                        </p>
                    </div>

                    <!-- Initial Balance -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Initial Balance (Optional)
                        </label>
                        <div class="relative">
                            <DollarSign
                                :size="20"
                                class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"
                            />
                            <input
                                v-model="form.initial_balance"
                                type="number"
                                step="0.01"
                                min="0"
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-gray-900"
                                placeholder="0.00"
                            />
                        </div>
                        <p v-if="form.errors.initial_balance" class="mt-1 text-sm text-red-600">
                            {{ form.errors.initial_balance }}
                        </p>
                        <p class="mt-1 text-sm text-gray-500">
                            An initial deposit transaction will be created if amount > 0
                        </p>
                    </div>

                    <!-- Primary Account Checkbox -->
                    <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg">
                        <input
                            v-model="form.is_primary"
                            type="checkbox"
                            id="is_primary"
                            class="mt-1 w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500"
                        />
                        <div class="flex-1">
                            <label for="is_primary" class="text-sm font-medium text-gray-900 cursor-pointer">
                                Set as Primary Account
                            </label>
                            <p class="text-sm text-gray-600 mt-1">
                                This will be the default account for the user. If checked, any existing primary account will be updated.
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
                            {{ form.processing ? 'Creating Account...' : 'Create Account' }}
                        </button>
                        <a
                            href="/admin/accounts"
                            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium"
                        >
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
