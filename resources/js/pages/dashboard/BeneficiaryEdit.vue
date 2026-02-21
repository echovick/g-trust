<script setup lang="ts">
import { router, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Globe, MapPin } from 'lucide-vue-next';
import DashboardLayout from '@/layouts/DashboardLayout.vue';

interface Beneficiary {
    id: number;
    name: string;
    nickname?: string;
    account_number: string;
    routing_number?: string;
    bank_name: string;
    bank_address?: string;
    swift_code?: string;
    iban?: string;
    country: string;
    beneficiary_type: 'domestic' | 'international';
    email?: string;
    phone?: string;
}

interface Props {
    beneficiary: Beneficiary;
}

const props = defineProps<Props>();

const form = useForm({
    name: props.beneficiary.name,
    nickname: props.beneficiary.nickname || '',
    account_number: props.beneficiary.account_number,
    routing_number: props.beneficiary.routing_number || '',
    bank_name: props.beneficiary.bank_name,
    bank_address: props.beneficiary.bank_address || '',
    swift_code: props.beneficiary.swift_code || '',
    iban: props.beneficiary.iban || '',
    country: props.beneficiary.country,
    beneficiary_type: props.beneficiary.beneficiary_type,
    email: props.beneficiary.email || '',
    phone: props.beneficiary.phone || '',
});

const submit = () => {
    form.put(`/dashboard/beneficiaries/${props.beneficiary.id}`);
};
</script>

<template>
    <DashboardLayout title="Edit Beneficiary">
        <div class="mb-8">
            <button
                @click="router.visit('/dashboard/beneficiaries')"
                class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-4 transition-colors"
            >
                <ArrowLeft :size="20" />
                Back to Beneficiaries
            </button>
            <p class="text-gray-600">Update beneficiary details</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
            <form @submit.prevent="submit" class="space-y-8">
                <!-- Beneficiary Type Selection -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Beneficiary Type</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="relative flex items-start p-4 border-2 rounded-lg cursor-pointer transition-all"
                            :class="form.beneficiary_type === 'domestic'
                                ? 'border-red-500 bg-red-50'
                                : 'border-gray-200 hover:border-gray-300'"
                        >
                            <input
                                type="radio"
                                v-model="form.beneficiary_type"
                                value="domestic"
                                class="sr-only"
                            />
                            <MapPin :size="24" class="text-green-500 mr-3 flex-shrink-0" />
                            <div class="flex-1">
                                <div class="font-semibold text-gray-900">Domestic</div>
                                <div class="text-sm text-gray-600">Within the same country</div>
                            </div>
                        </label>

                        <label class="relative flex items-start p-4 border-2 rounded-lg cursor-pointer transition-all"
                            :class="form.beneficiary_type === 'international'
                                ? 'border-red-500 bg-red-50'
                                : 'border-gray-200 hover:border-gray-300'"
                        >
                            <input
                                type="radio"
                                v-model="form.beneficiary_type"
                                value="international"
                                class="sr-only"
                            />
                            <Globe :size="24" class="text-blue-500 mr-3 flex-shrink-0" />
                            <div class="flex-1">
                                <div class="font-semibold text-gray-900">International</div>
                                <div class="text-sm text-gray-600">Cross-border transfers</div>
                            </div>
                        </label>
                    </div>
                    <div v-if="form.errors.beneficiary_type" class="mt-2 text-sm text-red-600">
                        {{ form.errors.beneficiary_type }}
                    </div>
                </div>

                <!-- Personal Information -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.name"
                                type="text"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-gray-900"
                                placeholder="John Doe"
                            />
                            <div v-if="form.errors.name" class="mt-2 text-sm text-red-600">
                                {{ form.errors.name }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nickname (Optional)
                            </label>
                            <input
                                v-model="form.nickname"
                                type="text"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-gray-900"
                                placeholder="Mom, Dad, Friend, etc."
                            />
                            <div v-if="form.errors.nickname" class="mt-2 text-sm text-red-600">
                                {{ form.errors.nickname }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Email (Optional)
                            </label>
                            <input
                                v-model="form.email"
                                type="email"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-gray-900"
                                placeholder="john@example.com"
                            />
                            <div v-if="form.errors.email" class="mt-2 text-sm text-red-600">
                                {{ form.errors.email }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Phone (Optional)
                            </label>
                            <input
                                v-model="form.phone"
                                type="tel"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-gray-900"
                                placeholder="+1 (555) 123-4567"
                            />
                            <div v-if="form.errors.phone" class="mt-2 text-sm text-red-600">
                                {{ form.errors.phone }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bank Information -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Bank Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Bank Name <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.bank_name"
                                type="text"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-gray-900"
                                placeholder="ABC Bank"
                            />
                            <div v-if="form.errors.bank_name" class="mt-2 text-sm text-red-600">
                                {{ form.errors.bank_name }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Country <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.country"
                                type="text"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-gray-900"
                                placeholder="United States"
                            />
                            <div v-if="form.errors.country" class="mt-2 text-sm text-red-600">
                                {{ form.errors.country }}
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Bank Address (Optional)
                            </label>
                            <textarea
                                v-model="form.bank_address"
                                rows="3"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-gray-900"
                                placeholder="123 Bank Street, City, State, ZIP"
                            ></textarea>
                            <div v-if="form.errors.bank_address" class="mt-2 text-sm text-red-600">
                                {{ form.errors.bank_address }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Account Number <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.account_number"
                                type="text"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-gray-900"
                                placeholder="1234567890"
                            />
                            <div v-if="form.errors.account_number" class="mt-2 text-sm text-red-600">
                                {{ form.errors.account_number }}
                            </div>
                        </div>

                        <div v-if="form.beneficiary_type === 'domestic'">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Routing Number
                            </label>
                            <input
                                v-model="form.routing_number"
                                type="text"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-gray-900"
                                placeholder="021000021"
                            />
                            <div v-if="form.errors.routing_number" class="mt-2 text-sm text-red-600">
                                {{ form.errors.routing_number }}
                            </div>
                        </div>

                        <div v-if="form.beneficiary_type === 'international'">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                SWIFT/BIC Code
                            </label>
                            <input
                                v-model="form.swift_code"
                                type="text"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-gray-900"
                                placeholder="ABCDUS33XXX"
                            />
                            <div v-if="form.errors.swift_code" class="mt-2 text-sm text-red-600">
                                {{ form.errors.swift_code }}
                            </div>
                        </div>

                        <div v-if="form.beneficiary_type === 'international'">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                IBAN (Optional)
                            </label>
                            <input
                                v-model="form.iban"
                                type="text"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-gray-900"
                                placeholder="GB82 WEST 1234 5698 7654 32"
                            />
                            <div v-if="form.errors.iban" class="mt-2 text-sm text-red-600">
                                {{ form.errors.iban }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notice -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-sm text-yellow-800">
                        <strong>Note:</strong> Changes to beneficiary details will require re-verification by our team.
                        This process usually takes 1-2 business days.
                    </p>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-4 pt-6 border-t">
                    <button
                        type="button"
                        @click="router.visit('/dashboard/beneficiaries')"
                        class="px-6 py-3 border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-3 bg-red-500 hover:bg-red-600 text-white rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {{ form.processing ? 'Updating...' : 'Update Beneficiary' }}
                    </button>
                </div>
            </form>
        </div>
    </DashboardLayout>
</template>
