<script setup lang="ts">
import { ref, computed } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Send, ArrowLeftRight, Globe, UserPlus } from 'lucide-vue-next';
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

interface Beneficiary {
    id: number;
    name: string;
    nickname?: string;
    account_number: string;
    bank_name: string;
    country: string;
    beneficiary_type: 'domestic' | 'international';
    is_verified: boolean;
}

interface Props {
    accounts: Account[];
    beneficiaries: Beneficiary[];
}

const props = defineProps<Props>();

const form = useForm({
    from_account_id: props.accounts[0]?.id || null,
    transfer_type: 'internal' as 'internal' | 'external' | 'international',
    amount: '',
    description: '',
    beneficiary_id: null as number | null,
    to_account_id: null as number | null,
    scheduled_date: null as string | null,
});

const selectedFromAccount = computed(() => {
    return props.accounts.find(acc => acc.id === form.from_account_id);
});

const availableBeneficiaries = computed(() => {
    if (form.transfer_type === 'internal') return [];
    if (form.transfer_type === 'external') {
        return props.beneficiaries.filter(b => b.beneficiary_type === 'domestic' && b.is_verified);
    }
    return props.beneficiaries.filter(b => b.beneficiary_type === 'international' && b.is_verified);
});

const availableToAccounts = computed(() => {
    if (form.transfer_type !== 'internal') return [];
    return props.accounts.filter(acc => acc.id !== form.from_account_id);
});

const estimatedFee = computed(() => {
    if (form.transfer_type === 'internal') return 0;
    if (form.transfer_type === 'external') return 2.5;
    return 15; // International
});

const totalAmount = computed(() => {
    const amount = parseFloat(form.amount) || 0;
    return amount + estimatedFee.value;
});

const submit = () => {
    form.post('/dashboard/transfers');
};

const getTransferTypeIcon = (type: string) => {
    switch (type) {
        case 'internal': return ArrowLeftRight;
        case 'external': return Send;
        case 'international': return Globe;
        default: return Send;
    }
};
</script>

<template>
    <DashboardLayout title="New Transfer">
        <div class="mb-8">
            <button
                @click="router.visit('/dashboard/transfers')"
                class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-4 transition-colors"
            >
                <ArrowLeft :size="20" />
                Back to Transfers
            </button>
            <p class="text-gray-600">Send money to your accounts or beneficiaries</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
            <form @submit.prevent="submit" class="space-y-8">
                <!-- Transfer Type Selection -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Transfer Type</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <label class="relative flex flex-col items-center p-6 border-2 rounded-lg cursor-pointer transition-all"
                            :class="form.transfer_type === 'internal'
                                ? 'border-red-500 bg-red-50'
                                : 'border-gray-200 hover:border-gray-300'"
                        >
                            <input
                                type="radio"
                                v-model="form.transfer_type"
                                value="internal"
                                @change="() => { form.beneficiary_id = null; form.to_account_id = null; }"
                                class="sr-only"
                            />
                            <ArrowLeftRight :size="32" class="text-blue-500 mb-3" />
                            <div class="font-semibold text-gray-900 mb-1">Internal Transfer</div>
                            <div class="text-sm text-gray-600 text-center">Between your accounts</div>
                            <div class="text-xs text-green-600 font-medium mt-2">Free</div>
                        </label>

                        <label class="relative flex flex-col items-center p-6 border-2 rounded-lg cursor-pointer transition-all"
                            :class="form.transfer_type === 'external'
                                ? 'border-red-500 bg-red-50'
                                : 'border-gray-200 hover:border-gray-300'"
                        >
                            <input
                                type="radio"
                                v-model="form.transfer_type"
                                value="external"
                                @change="() => { form.to_account_id = null; }"
                                class="sr-only"
                            />
                            <Send :size="32" class="text-green-500 mb-3" />
                            <div class="font-semibold text-gray-900 mb-1">External Transfer</div>
                            <div class="text-sm text-gray-600 text-center">To domestic beneficiaries</div>
                            <div class="text-xs text-gray-600 font-medium mt-2">Fee: $2.50</div>
                        </label>

                        <label class="relative flex flex-col items-center p-6 border-2 rounded-lg cursor-pointer transition-all"
                            :class="form.transfer_type === 'international'
                                ? 'border-red-500 bg-red-50'
                                : 'border-gray-200 hover:border-gray-300'"
                        >
                            <input
                                type="radio"
                                v-model="form.transfer_type"
                                value="international"
                                @change="() => { form.to_account_id = null; }"
                                class="sr-only"
                            />
                            <Globe :size="32" class="text-purple-500 mb-3" />
                            <div class="font-semibold text-gray-900 mb-1">International Transfer</div>
                            <div class="text-sm text-gray-600 text-center">Cross-border payments</div>
                            <div class="text-xs text-gray-600 font-medium mt-2">Fee: $15.00</div>
                        </label>
                    </div>
                    <div v-if="form.errors.transfer_type" class="mt-2 text-sm text-red-600">
                        {{ form.errors.transfer_type }}
                    </div>
                </div>

                <!-- From Account -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">From</h3>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Select Account <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.from_account_id"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-gray-900"
                        >
                            <option v-for="account in accounts" :key="account.id" :value="account.id">
                                {{ account.account_name }} ({{ account.account_number }}) -
                                {{ account.currency }} {{ account.available_balance.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
                            </option>
                        </select>
                        <div v-if="form.errors.from_account_id" class="mt-2 text-sm text-red-600">
                            {{ form.errors.from_account_id }}
                        </div>
                    </div>
                </div>

                <!-- To Account/Beneficiary -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">To</h3>

                    <!-- Internal Transfer - Select Account -->
                    <div v-if="form.transfer_type === 'internal'">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Select Destination Account <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.to_account_id"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-gray-900"
                        >
                            <option value="" disabled>Choose an account</option>
                            <option v-for="account in availableToAccounts" :key="account.id" :value="account.id">
                                {{ account.account_name }} ({{ account.account_number }})
                            </option>
                        </select>
                        <div v-if="form.errors.to_account_id" class="mt-2 text-sm text-red-600">
                            {{ form.errors.to_account_id }}
                        </div>
                    </div>

                    <!-- External/International Transfer - Select Beneficiary -->
                    <div v-else>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Select Beneficiary <span class="text-red-500">*</span>
                        </label>

                        <div v-if="availableBeneficiaries.length > 0">
                            <select
                                v-model="form.beneficiary_id"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-gray-900"
                            >
                                <option value="" disabled>Choose a beneficiary</option>
                                <option v-for="beneficiary in availableBeneficiaries" :key="beneficiary.id" :value="beneficiary.id">
                                    {{ beneficiary.name }}{{ beneficiary.nickname ? ` (${beneficiary.nickname})` : '' }} -
                                    {{ beneficiary.bank_name }}, {{ beneficiary.country }}
                                </option>
                            </select>
                        </div>
                        <div v-else class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <p class="text-sm text-yellow-800 mb-3">
                                <strong>No verified beneficiaries found.</strong>
                                You need to add and verify a {{ form.transfer_type }} beneficiary first.
                            </p>
                            <button
                                type="button"
                                @click="router.visit('/dashboard/beneficiaries/create')"
                                class="inline-flex items-center gap-2 text-sm font-medium text-yellow-900 hover:text-yellow-700"
                            >
                                <UserPlus :size="16" />
                                Add Beneficiary
                            </button>
                        </div>
                        <div v-if="form.errors.beneficiary_id" class="mt-2 text-sm text-red-600">
                            {{ form.errors.beneficiary_id }}
                        </div>
                    </div>
                </div>

                <!-- Transfer Details -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Transfer Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Amount <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">
                                    {{ selectedFromAccount?.currency || 'USD' }}
                                </span>
                                <input
                                    v-model="form.amount"
                                    type="number"
                                    step="0.01"
                                    min="0.01"
                                    :max="selectedFromAccount?.available_balance"
                                    required
                                    class="w-full pl-16 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-gray-900"
                                    placeholder="0.00"
                                />
                            </div>
                            <div v-if="selectedFromAccount" class="mt-2 text-sm text-gray-600">
                                Available balance: {{ selectedFromAccount.currency }} {{ selectedFromAccount.available_balance.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
                            </div>
                            <div v-if="form.errors.amount" class="mt-2 text-sm text-red-600">
                                {{ form.errors.amount }}
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Description <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                v-model="form.description"
                                required
                                rows="3"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-gray-900"
                                placeholder="What is this transfer for?"
                            ></textarea>
                            <div v-if="form.errors.description" class="mt-2 text-sm text-red-600">
                                {{ form.errors.description }}
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="flex items-start gap-3">
                                <input
                                    type="checkbox"
                                    :checked="form.scheduled_date !== null"
                                    @change="(e) => {
                                        const target = e.target as HTMLInputElement;
                                        form.scheduled_date = target.checked ? new Date().toISOString().split('T')[0] : null;
                                    }"
                                    class="mt-1 w-4 h-4 text-red-500 border-gray-300 rounded focus:ring-red-500"
                                />
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900">Schedule for later</div>
                                    <div class="text-sm text-gray-600">Process this transfer on a future date</div>
                                </div>
                            </label>

                            <div v-if="form.scheduled_date !== null" class="mt-3">
                                <input
                                    v-model="form.scheduled_date"
                                    type="date"
                                    :min="new Date().toISOString().split('T')[0]"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-gray-900"
                                />
                            </div>
                            <div v-if="form.errors.scheduled_date" class="mt-2 text-sm text-red-600">
                                {{ form.errors.scheduled_date }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transfer Summary -->
                <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                    <h4 class="font-semibold text-gray-900 mb-4">Transfer Summary</h4>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Transfer Amount</span>
                            <span class="font-medium text-gray-900">
                                {{ selectedFromAccount?.currency || 'USD' }} {{ (parseFloat(form.amount) || 0).toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Transfer Fee</span>
                            <span class="font-medium text-gray-900">
                                {{ selectedFromAccount?.currency || 'USD' }} {{ estimatedFee.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-base font-semibold pt-3 border-t">
                            <span class="text-gray-900">Total Amount</span>
                            <span class="text-gray-900">
                                {{ selectedFromAccount?.currency || 'USD' }} {{ totalAmount.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-4 pt-6 border-t">
                    <button
                        type="button"
                        @click="router.visit('/dashboard/transfers')"
                        class="px-6 py-3 border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        :disabled="form.processing || (form.transfer_type !== 'internal' && availableBeneficiaries.length === 0)"
                        class="px-6 py-3 bg-red-500 hover:bg-red-600 text-white rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                    >
                        <Send :size="20" />
                        {{ form.processing ? 'Processing...' : form.scheduled_date ? 'Schedule Transfer' : 'Send Transfer' }}
                    </button>
                </div>
            </form>
        </div>
    </DashboardLayout>
</template>
