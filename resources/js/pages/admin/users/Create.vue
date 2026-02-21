<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { ArrowLeft } from 'lucide-vue-next';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    is_admin: false,
    preferred_currency: 'USD',
    create_account: true,
    account_type: 'checking',
    account_currency: 'USD',
});

const submit = () => {
    form.post('/admin/users', {
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <AdminLayout title="Create User">
        <div class="space-y-4 sm:space-y-6">
            <div class="flex items-start gap-4">
                <a
                    href="/admin/users"
                    class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
                >
                    <ArrowLeft :size="20" class="text-gray-600" />
                </a>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Create New User</h1>
                    <p class="text-gray-600 mt-1">Add a new user to the system</p>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- User Information -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">User Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Full Name *
                            </label>
                            <input
                                v-model="form.name"
                                type="text"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-gray-900"
                                placeholder="John Doe"
                            />
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address *
                            </label>
                            <input
                                v-model="form.email"
                                type="email"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-gray-900"
                                placeholder="john@example.com"
                            />
                            <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                                {{ form.errors.email }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Password *
                            </label>
                            <input
                                v-model="form.password"
                                type="password"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-gray-900"
                            />
                            <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">
                                {{ form.errors.password }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Confirm Password *
                            </label>
                            <input
                                v-model="form.password_confirmation"
                                type="password"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-gray-900"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Preferred Currency *
                            </label>
                            <select
                                v-model="form.preferred_currency"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-gray-900"
                            >
                                <option value="USD">USD - US Dollar</option>
                                <option value="EUR">EUR - Euro</option>
                                <option value="GBP">GBP - British Pound</option>
                                <option value="NGN">NGN - Nigerian Naira</option>
                            </select>
                        </div>

                        <div class="flex items-center">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input
                                    v-model="form.is_admin"
                                    type="checkbox"
                                    class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500"
                                />
                                <span class="text-sm font-medium text-gray-700">
                                    Administrator
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Account Information -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <input
                            v-model="form.create_account"
                            type="checkbox"
                            id="create_account"
                            class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500"
                        />
                        <label for="create_account" class="text-lg font-semibold text-gray-900 cursor-pointer">
                            Create Account for User
                        </label>
                    </div>

                    <div v-if="form.create_account" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Account Type *
                            </label>
                            <select
                                v-model="form.account_type"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-gray-900"
                            >
                                <option value="checking">Checking Account</option>
                                <option value="savings">Savings Account</option>
                                <option value="business">Business Account</option>
                            </select>
                            <p v-if="form.errors.account_type" class="mt-1 text-sm text-red-600">
                                {{ form.errors.account_type }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Account Currency *
                            </label>
                            <select
                                v-model="form.account_currency"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-gray-900"
                            >
                                <option value="USD">USD - US Dollar</option>
                                <option value="EUR">EUR - Euro</option>
                                <option value="GBP">GBP - British Pound</option>
                                <option value="NGN">NGN - Nigerian Naira</option>
                            </select>
                            <p v-if="form.errors.account_currency" class="mt-1 text-sm text-red-600">
                                {{ form.errors.account_currency }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row justify-end gap-3">
                    <a
                        href="/admin/users"
                        class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-center"
                    >
                        Cancel
                    </a>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50"
                    >
                        {{ form.processing ? 'Creating...' : 'Create User' }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
