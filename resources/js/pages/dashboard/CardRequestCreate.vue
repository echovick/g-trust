<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { ArrowLeft, CreditCard, Wallet, CheckCircle } from 'lucide-vue-next';

interface Account {
    id: number;
    account_number: string;
    account_name: string;
    account_type: string;
    currency: string;
}

interface Props {
    accounts: Account[];
}

const props = defineProps<Props>();

const form = useForm({
    account_id: props.accounts.length > 0 ? props.accounts[0].id : null,
    card_type: 'debit',
    card_brand: 'visa',
    purpose: '',
});

const cardTypes = [
    {
        value: 'debit',
        label: 'Debit Card',
        description: 'Direct access to your account funds',
        icon: Wallet,
        color: 'text-blue-600 bg-blue-50',
    },
    {
        value: 'credit',
        label: 'Credit Card',
        description: 'Borrow money up to a credit limit',
        icon: CreditCard,
        color: 'text-purple-600 bg-purple-50',
    },
];

const cardBrands = [
    {
        value: 'visa',
        label: 'Visa',
        description: 'Accepted worldwide',
    },
    {
        value: 'mastercard',
        label: 'Mastercard',
        description: 'Global acceptance',
    },
    {
        value: 'amex',
        label: 'American Express',
        description: 'Premium benefits',
    },
    {
        value: 'discover',
        label: 'Discover',
        description: 'Cashback rewards',
    },
];

const submit = () => {
    form.post('/dashboard/card-requests');
};
</script>

<template>
    <DashboardLayout title="Request New Card">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <a
                    href="/dashboard/card-requests"
                    class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
                >
                    <ArrowLeft :size="20" class="text-gray-600" />
                </a>
                <div>
                    <p class="text-gray-600">Submit a request for a new card</p>
                </div>
            </div>

            <!-- No Accounts Notice -->
            <div v-if="accounts.length === 0" class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                <div class="flex items-start gap-3">
                    <div class="w-5 h-5 rounded-full bg-yellow-500 text-white flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">
                        !
                    </div>
                    <div class="text-sm text-yellow-900">
                        <p class="font-medium mb-1">No Active Accounts Found</p>
                        <p class="text-yellow-800 mb-3">You need an active account before you can request a card. Please create an account first.</p>
                        <a
                            href="/dashboard/account-requests/create"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors text-sm font-medium"
                        >
                            Request New Account
                        </a>
                    </div>
                </div>
            </div>

            <form v-else @submit.prevent="submit" class="space-y-8">
                <!-- Select Account -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Select Account *</h3>
                    <p class="text-sm text-gray-600 mb-4">Choose which account you want to link this card to</p>
                    <select
                        v-model="form.account_id"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-gray-900"
                    >
                        <option v-for="account in accounts" :key="account.id" :value="account.id">
                            {{ account.account_number }} - {{ account.account_name }} ({{ account.currency }} {{ account.account_type }})
                        </option>
                    </select>
                    <p v-if="form.errors.account_id" class="mt-2 text-sm text-red-600">
                        {{ form.errors.account_id }}
                    </p>
                </div>

                <!-- Card Type Selection -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Select Card Type *</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label
                            v-for="type in cardTypes"
                            :key="type.value"
                            :class="[
                                'relative cursor-pointer rounded-lg border-2 p-4 transition-all hover:shadow-md',
                                form.card_type === type.value
                                    ? 'border-red-500 bg-red-50'
                                    : 'border-gray-200 hover:border-gray-300'
                            ]"
                        >
                            <input
                                v-model="form.card_type"
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
                                            v-if="form.card_type === type.value"
                                            :size="20"
                                            class="text-red-500"
                                        />
                                    </div>
                                    <p class="text-sm text-gray-600">{{ type.description }}</p>
                                </div>
                            </div>
                        </label>
                    </div>
                    <p v-if="form.errors.card_type" class="mt-2 text-sm text-red-600">
                        {{ form.errors.card_type }}
                    </p>
                </div>

                <!-- Card Brand Selection -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Select Card Brand *</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <label
                            v-for="brand in cardBrands"
                            :key="brand.value"
                            :class="[
                                'relative cursor-pointer rounded-lg border-2 p-4 transition-all hover:shadow-md',
                                form.card_brand === brand.value
                                    ? 'border-red-500 bg-red-50'
                                    : 'border-gray-200 hover:border-gray-300'
                            ]"
                        >
                            <input
                                v-model="form.card_brand"
                                type="radio"
                                :value="brand.value"
                                class="sr-only"
                            />
                            <div class="text-center">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="font-semibold text-gray-900 uppercase">{{ brand.label }}</p>
                                    <CheckCircle
                                        v-if="form.card_brand === brand.value"
                                        :size="18"
                                        class="text-red-500"
                                    />
                                </div>
                                <p class="text-xs text-gray-600">{{ brand.description }}</p>
                            </div>
                        </label>
                    </div>
                    <p v-if="form.errors.card_brand" class="mt-2 text-sm text-red-600">
                        {{ form.errors.card_brand }}
                    </p>
                </div>

                <!-- Purpose -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Purpose (Optional)</h3>
                    <textarea
                        v-model="form.purpose"
                        rows="4"
                        placeholder="Briefly explain why you need this card..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 resize-none text-gray-900"
                    ></textarea>
                    <p v-if="form.errors.purpose" class="mt-1 text-sm text-red-600">
                        {{ form.errors.purpose }}
                    </p>
                    <p class="mt-1 text-sm text-gray-500">
                        Help us understand your needs (max 1000 characters)
                    </p>
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
                                <li>A unique card will be created and shipped to your address</li>
                                <li>You can track your request status in your dashboard</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-3">
                    <a
                        href="/dashboard/card-requests"
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
