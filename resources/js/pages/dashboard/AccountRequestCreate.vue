<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { ArrowLeft, Wallet, PiggyBank, Building, CheckCircle } from 'lucide-vue-next';

const form = useForm({
    account_type: 'checking',
    currency: 'USD',
    account_name: '',
    purpose: '',
});

const accountTypes = [
    {
        value: 'checking',
        label: 'Checking Account',
        description: 'For everyday transactions and bill payments',
        icon: Wallet,
        color: 'text-blue-600 bg-blue-50',
    },
    {
        value: 'savings',
        label: 'Savings Account',
        description: 'For saving money and earning interest',
        icon: PiggyBank,
        color: 'text-green-600 bg-green-50',
    },
    {
        value: 'business',
        label: 'Business Account',
        description: 'For business operations and commercial activities',
        icon: Building,
        color: 'text-purple-600 bg-purple-50',
    },
];

const currencies = [
    { code: 'USD', name: 'US Dollar', symbol: '$' },
    { code: 'EUR', name: 'Euro', symbol: '€' },
    { code: 'GBP', name: 'British Pound', symbol: '£' },
    { code: 'NGN', name: 'Nigerian Naira', symbol: '₦' },
];

const submit = () => {
    form.post('/dashboard/account-requests');
};
</script>

<template>
    <DashboardLayout title="Request New Account">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <a
                    href="/dashboard/account-requests"
                    class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
                >
                    <ArrowLeft :size="20" class="text-gray-600" />
                </a>
                <div>
                    <p class="text-gray-600">Submit a request for a new bank account</p>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-8">
                <!-- Account Type Selection -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Select Account Type *</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <label
                            v-for="type in accountTypes"
                            :key="type.value"
                            :class="[
                                'relative cursor-pointer rounded-lg border-2 p-4 transition-all hover:shadow-md',
                                form.account_type === type.value
                                    ? 'border-red-500 bg-red-50'
                                    : 'border-gray-200 hover:border-gray-300'
                            ]"
                        >
                            <input
                                v-model="form.account_type"
                                type="radio"
                                :value="type.value"
                                class="sr-only"
                            />
                            <div class="flex items-start gap-3">
                                <div :class="[type.color, 'w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0']">
                                    <component :is="type.icon" :size="24" />
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-1">
                                        <p class="font-semibold text-gray-900">{{ type.label }}</p>
                                        <CheckCircle
                                            v-if="form.account_type === type.value"
                                            :size="20"
                                            class="text-red-500"
                                        />
                                    </div>
                                    <p class="text-sm text-gray-600">{{ type.description }}</p>
                                </div>
                            </div>
                        </label>
                    </div>
                    <p v-if="form.errors.account_type" class="mt-2 text-sm text-red-600">
                        {{ form.errors.account_type }}
                    </p>
                </div>

                <!-- Account Details -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">
                    <h3 class="text-lg font-semibold text-gray-900">Account Details</h3>

                    <!-- Account Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Account Name *
                        </label>
                        <input
                            v-model="form.account_name"
                            type="text"
                            required
                            placeholder="e.g., My Checking Account"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                        />
                        <p v-if="form.errors.account_name" class="mt-1 text-sm text-red-600">
                            {{ form.errors.account_name }}
                        </p>
                        <p class="mt-1 text-sm text-gray-500">
                            Choose a name to help you identify this account
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
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                        >
                            <option v-for="currency in currencies" :key="currency.code" :value="currency.code">
                                {{ currency.symbol }} {{ currency.name }} ({{ currency.code }})
                            </option>
                        </select>
                        <p v-if="form.errors.currency" class="mt-1 text-sm text-red-600">
                            {{ form.errors.currency }}
                        </p>
                    </div>

                    <!-- Purpose -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Purpose (Optional)
                        </label>
                        <textarea
                            v-model="form.purpose"
                            rows="4"
                            placeholder="Briefly explain why you need this account..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 resize-none"
                        ></textarea>
                        <p v-if="form.errors.purpose" class="mt-1 text-sm text-red-600">
                            {{ form.errors.purpose }}
                        </p>
                        <p class="mt-1 text-sm text-gray-500">
                            Help us understand your needs (max 1000 characters)
                        </p>
                    </div>
                </div>

                <!-- Info Notice -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <div class="w-5 h-5 rounded-full bg-blue-500 text-white flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">
                            i
                        </div>
                        <div class="text-sm text-blue-900">
                            <p class="font-medium mb-1">What happens next?</p>
                            <ul class="list-disc list-inside space-y-1 text-blue-800">
                                <li>Your request will be reviewed by our team</li>
                                <li>You'll be notified via email once it's approved</li>
                                <li>A unique account number will be generated upon approval</li>
                                <li>You can track your request status in your dashboard</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-3">
                    <a
                        href="/dashboard/account-requests"
                        class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium"
                    >
                        Cancel
                    </a>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {{ form.processing ? 'Submitting...' : 'Submit Request' }}
                    </button>
                </div>
            </form>
        </div>
    </DashboardLayout>
</template>
