<script setup lang="ts">
import { router, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Receipt, Zap, CreditCard, DollarSign, Shield, FileText } from 'lucide-vue-next';
import DashboardLayout from '@/layouts/DashboardLayout.vue';

interface Account {
    id: number;
    account_name: string;
    account_number: string;
    account_type: string;
    balance: number;
    available_balance: number;
    currency: string;
}

interface Props {
    accounts: Account[];
}

const props = defineProps<Props>();

const form = useForm({
    account_id: props.accounts[0]?.id || null,
    payee_name: '',
    payee_account_number: '',
    payee_type: 'utility' as 'utility' | 'credit_card' | 'loan' | 'insurance' | 'other',
    amount: '',
    memo: '',
    frequency: 'once' as 'once' | 'weekly' | 'monthly' | 'quarterly' | 'annually',
    scheduled_date: new Date().toISOString().split('T')[0],
    auto_pay: false,
});

const submit = () => {
    form.post('/dashboard/bill-payments');
};

const getPayeeTypeIcon = (type: string) => {
    switch (type) {
        case 'utility': return Zap;
        case 'credit_card': return CreditCard;
        case 'loan': return DollarSign;
        case 'insurance': return Shield;
        default: return FileText;
    }
};
</script>

<template>
    <DashboardLayout title="Schedule Bill Payment">
        <div class="mb-8">
            <button
                @click="router.visit('/dashboard/bill-payments')"
                class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-4 transition-colors"
            >
                <ArrowLeft :size="20" />
                Back to Bill Payments
            </button>
            <p class="text-gray-600">Set up a one-time or recurring bill payment</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
            <form @submit.prevent="submit" class="space-y-8">
                <!-- Payee Type Selection -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Payee Type</label>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                        <label v-for="type in ['utility', 'credit_card', 'loan', 'insurance', 'other']" :key="type"
                            class="relative flex flex-col items-center p-4 border-2 rounded-lg cursor-pointer transition-all"
                            :class="form.payee_type === type
                                ? 'border-red-500 bg-red-50'
                                : 'border-gray-200 hover:border-gray-300'"
                        >
                            <input
                                type="radio"
                                v-model="form.payee_type"
                                :value="type"
                                class="sr-only"
                            />
                            <component :is="getPayeeTypeIcon(type)" :size="24" class="text-gray-700 mb-2" />
                            <span class="text-sm font-medium text-gray-900 capitalize text-center">
                                {{ type.replace('_', ' ') }}
                            </span>
                        </label>
                    </div>
                    <div v-if="form.errors.payee_type" class="mt-2 text-sm text-red-600">
                        {{ form.errors.payee_type }}
                    </div>
                </div>

                <!-- Payee Information -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Payee Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Payee Name <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.payee_name"
                                type="text"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-gray-900"
                                placeholder="Electric Company, Bank, etc."
                            />
                            <div v-if="form.errors.payee_name" class="mt-2 text-sm text-red-600">
                                {{ form.errors.payee_name }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Payee Account Number (Optional)
                            </label>
                            <input
                                v-model="form.payee_account_number"
                                type="text"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-gray-900"
                                placeholder="Your account number with payee"
                            />
                            <div v-if="form.errors.payee_account_number" class="mt-2 text-sm text-red-600">
                                {{ form.errors.payee_account_number }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                From Account <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="form.account_id"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-gray-900"
                            >
                                <option v-for="account in accounts" :key="account.id" :value="account.id">
                                    {{ account.account_name }} ({{ account.account_number }}) -
                                    {{ account.currency }} {{ account.available_balance.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
                                </option>
                            </select>
                            <div v-if="form.errors.account_id" class="mt-2 text-sm text-red-600">
                                {{ form.errors.account_id }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Amount <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.amount"
                                type="number"
                                step="0.01"
                                min="0.01"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-gray-900"
                                placeholder="0.00"
                            />
                            <div v-if="form.errors.amount" class="mt-2 text-sm text-red-600">
                                {{ form.errors.amount }}
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Memo (Optional)
                            </label>
                            <textarea
                                v-model="form.memo"
                                rows="3"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-gray-900"
                                placeholder="Add a note about this payment..."
                            ></textarea>
                            <div v-if="form.errors.memo" class="mt-2 text-sm text-red-600">
                                {{ form.errors.memo }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Schedule Settings -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Schedule Settings</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Frequency <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="form.frequency"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-gray-900"
                            >
                                <option value="once">One-time</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                                <option value="quarterly">Quarterly</option>
                                <option value="annually">Annually</option>
                            </select>
                            <div v-if="form.errors.frequency" class="mt-2 text-sm text-red-600">
                                {{ form.errors.frequency }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ form.frequency === 'once' ? 'Payment Date' : 'First Payment Date' }} <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.scheduled_date"
                                type="date"
                                required
                                :min="new Date().toISOString().split('T')[0]"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-gray-900"
                            />
                            <div v-if="form.errors.scheduled_date" class="mt-2 text-sm text-red-600">
                                {{ form.errors.scheduled_date }}
                            </div>
                        </div>
                    </div>

                    <!-- Auto-pay Toggle -->
                    <div v-if="form.frequency !== 'once'" class="mt-6">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input
                                type="checkbox"
                                v-model="form.auto_pay"
                                class="mt-1 w-4 h-4 text-red-500 border-gray-300 rounded focus:ring-red-500"
                            />
                            <div>
                                <div class="font-medium text-gray-900">Enable Auto-Pay</div>
                                <div class="text-sm text-gray-600">
                                    Automatically pay this bill on the scheduled date without manual approval
                                </div>
                            </div>
                        </label>
                        <div v-if="form.errors.auto_pay" class="mt-2 text-sm text-red-600">
                            {{ form.errors.auto_pay }}
                        </div>
                    </div>
                </div>

                <!-- Notice -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-sm text-blue-800">
                        <strong>Note:</strong> Bill payments will be processed on the scheduled date.
                        Make sure you have sufficient funds in your account before the payment date.
                    </p>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-4 pt-6 border-t">
                    <button
                        type="button"
                        @click="router.visit('/dashboard/bill-payments')"
                        class="px-6 py-3 border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-3 bg-red-500 hover:bg-red-600 text-white rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {{ form.processing ? 'Scheduling...' : 'Schedule Payment' }}
                    </button>
                </div>
            </form>
        </div>
    </DashboardLayout>
</template>
