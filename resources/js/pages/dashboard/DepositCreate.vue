<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { ArrowLeft, Upload, Camera, Info, X } from 'lucide-vue-next';

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
    amount: '',
    check_number: '',
    check_front_image: null as File | null,
    check_back_image: null as File | null,
    memo: '',
});

const frontPreview = ref<string | null>(null);
const backPreview = ref<string | null>(null);

const handleFrontImage = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (file) {
        form.check_front_image = file;
        frontPreview.value = URL.createObjectURL(file);
    }
};

const handleBackImage = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (file) {
        form.check_back_image = file;
        backPreview.value = URL.createObjectURL(file);
    }
};

const removeFrontImage = () => {
    form.check_front_image = null;
    frontPreview.value = null;
};

const removeBackImage = () => {
    form.check_back_image = null;
    backPreview.value = null;
};

const submit = () => {
    form.post('/dashboard/deposits');
};
</script>

<template>
    <DashboardLayout title="Deposit a Check">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <a
                    href="/dashboard/deposits"
                    class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
                >
                    <ArrowLeft :size="20" class="text-gray-600" />
                </a>
                <div>
                    <p class="text-gray-600">Take photos of your check to deposit funds</p>
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
                        <p class="text-yellow-800 mb-3">You need an active account before you can deposit a check. Please create an account first.</p>
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
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Deposit To *</h3>
                    <p class="text-sm text-gray-600 mb-4">Choose which account to deposit the check into</p>
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

                <!-- Amount -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Check Amount *</h3>
                    <p class="text-sm text-gray-600 mb-4">Enter the exact amount shown on the check</p>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">$</span>
                        <input
                            v-model="form.amount"
                            type="number"
                            step="0.01"
                            min="0.01"
                            required
                            placeholder="0.00"
                            class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-gray-900 text-lg"
                        />
                    </div>
                    <p v-if="form.errors.amount" class="mt-2 text-sm text-red-600">
                        {{ form.errors.amount }}
                    </p>
                </div>

                <!-- Check Number -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Check Number (Optional)</h3>
                    <input
                        v-model="form.check_number"
                        type="text"
                        placeholder="e.g., 1234"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-gray-900"
                    />
                    <p v-if="form.errors.check_number" class="mt-2 text-sm text-red-600">
                        {{ form.errors.check_number }}
                    </p>
                </div>

                <!-- Check Images -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Check Images *</h3>
                    <p class="text-sm text-gray-600 mb-6">Take clear photos of the front and back of your check</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Front Image -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Front of Check</label>
                            <div v-if="!frontPreview" class="relative">
                                <input
                                    type="file"
                                    accept="image/*"
                                    capture="environment"
                                    @change="handleFrontImage"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                />
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-red-400 hover:bg-red-50 transition-colors">
                                    <Camera :size="32" class="text-gray-400 mx-auto mb-3" />
                                    <p class="text-sm font-medium text-gray-700">Take photo or upload</p>
                                    <p class="text-xs text-gray-500 mt-1">PNG, JPG up to 5MB</p>
                                </div>
                            </div>
                            <div v-else class="relative">
                                <img :src="frontPreview" alt="Check front" class="w-full h-48 object-cover rounded-lg border border-gray-200" />
                                <button
                                    type="button"
                                    @click="removeFrontImage"
                                    class="absolute top-2 right-2 w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition-colors"
                                >
                                    <X :size="16" />
                                </button>
                            </div>
                            <p v-if="form.errors.check_front_image" class="mt-2 text-sm text-red-600">
                                {{ form.errors.check_front_image }}
                            </p>
                        </div>

                        <!-- Back Image -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Back of Check</label>
                            <div v-if="!backPreview" class="relative">
                                <input
                                    type="file"
                                    accept="image/*"
                                    capture="environment"
                                    @change="handleBackImage"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                />
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-red-400 hover:bg-red-50 transition-colors">
                                    <Camera :size="32" class="text-gray-400 mx-auto mb-3" />
                                    <p class="text-sm font-medium text-gray-700">Take photo or upload</p>
                                    <p class="text-xs text-gray-500 mt-1">PNG, JPG up to 5MB</p>
                                </div>
                            </div>
                            <div v-else class="relative">
                                <img :src="backPreview" alt="Check back" class="w-full h-48 object-cover rounded-lg border border-gray-200" />
                                <button
                                    type="button"
                                    @click="removeBackImage"
                                    class="absolute top-2 right-2 w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition-colors"
                                >
                                    <X :size="16" />
                                </button>
                            </div>
                            <p v-if="form.errors.check_back_image" class="mt-2 text-sm text-red-600">
                                {{ form.errors.check_back_image }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Memo -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Memo (Optional)</h3>
                    <textarea
                        v-model="form.memo"
                        rows="3"
                        placeholder="Add a note about this deposit..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 resize-none text-gray-900"
                    ></textarea>
                    <p v-if="form.errors.memo" class="mt-1 text-sm text-red-600">
                        {{ form.errors.memo }}
                    </p>
                    <p class="mt-1 text-sm text-gray-500">Max 500 characters</p>
                </div>

                <!-- Info Notice -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <Info :size="20" class="text-blue-600 mt-0.5 flex-shrink-0" />
                        <div class="text-sm text-blue-900">
                            <p class="font-medium mb-1">What happens next?</p>
                            <ul class="list-disc list-inside space-y-1 text-blue-800">
                                <li>Your deposit will be reviewed by our team</li>
                                <li>Funds are typically available within 1-2 business days</li>
                                <li>You'll be notified once the deposit is approved</li>
                                <li>Please keep the original check for 14 days after deposit</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-3">
                    <a
                        href="/dashboard/deposits"
                        class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium"
                    >
                        Cancel
                    </a>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {{ form.processing ? 'Submitting...' : 'Submit Deposit' }}
                    </button>
                </div>
            </form>
        </div>
    </DashboardLayout>
</template>
