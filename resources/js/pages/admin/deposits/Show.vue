<script setup lang="ts">
import { ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { ArrowLeft, User as UserIcon, Wallet, CheckCircle, XCircle, Clock, Image, AlertCircle } from 'lucide-vue-next';

interface User {
    id: number;
    name: string;
    email: string;
}

interface Account {
    id: number;
    account_number: string;
    account_name: string;
    user: User;
}

interface Deposit {
    id: number;
    account: Account;
    deposit_type: string;
    amount: string;
    currency: string;
    reference_number: string;
    check_number?: string;
    check_front_image?: string;
    check_back_image?: string;
    memo?: string;
    status: 'pending' | 'processing' | 'approved' | 'rejected' | 'completed';
    deposit_date: string;
    available_date?: string;
    rejection_reason?: string;
    created_at: string;
}

interface Props {
    deposit: Deposit;
}

const props = defineProps<Props>();

const showRejectModal = ref(false);
const showApproveModal = ref(false);
const showImageModal = ref(false);
const activeImage = ref('');

const rejectForm = useForm({
    rejection_reason: '',
});

const approve = () => {
    router.post(`/admin/deposits/${props.deposit.id}/approve`, {}, {
        onSuccess: () => {
            showApproveModal.value = false;
        }
    });
};

const reject = () => {
    rejectForm.post(`/admin/deposits/${props.deposit.id}/reject`, {
        onSuccess: () => {
            showRejectModal.value = false;
            rejectForm.reset();
        }
    });
};

const openImage = (imageUrl: string) => {
    activeImage.value = imageUrl;
    showImageModal.value = true;
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'long',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const formatCurrency = (amount: string, currency: string) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency,
    }).format(parseFloat(amount));
};

const getStatusColor = (status: string) => {
    switch (status) {
        case 'approved':
        case 'completed':
            return 'text-green-700 bg-green-50';
        case 'pending':
            return 'text-yellow-700 bg-yellow-50';
        case 'processing':
            return 'text-blue-700 bg-blue-50';
        case 'rejected':
            return 'text-red-700 bg-red-50';
        default:
            return 'text-gray-700 bg-gray-50';
    }
};

const getStatusIcon = (status: string) => {
    switch (status) {
        case 'approved':
        case 'completed':
            return CheckCircle;
        case 'pending':
            return Clock;
        case 'processing':
            return AlertCircle;
        case 'rejected':
            return XCircle;
        default:
            return Clock;
    }
};
</script>

<template>
    <AdminLayout title="Deposit Details">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <a
                    href="/admin/deposits"
                    class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
                >
                    <ArrowLeft :size="20" class="text-gray-600" />
                </a>
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900">Deposit Details</h1>
                    <p class="text-gray-600 mt-1">Review and manage this check deposit</p>
                </div>
                <div>
                    <span :class="[
                        'inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium',
                        getStatusColor(deposit.status)
                    ]">
                        <component :is="getStatusIcon(deposit.status)" :size="18" />
                        {{ deposit.status.charAt(0).toUpperCase() + deposit.status.slice(1) }}
                    </span>
                </div>
            </div>

            <!-- Depositor Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Depositor Information</h2>
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center flex-shrink-0">
                        <UserIcon :size="24" class="text-gray-500" />
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900">{{ deposit.account.user.name }}</h3>
                        <p class="text-sm text-gray-600">{{ deposit.account.user.email }}</p>
                        <a
                            :href="`/admin/users/${deposit.account.user.id}`"
                            class="text-sm text-red-600 hover:text-red-700 mt-2 inline-block"
                        >
                            View User Profile →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Account Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Deposit Account</h2>
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <Wallet :size="24" class="text-blue-600" />
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900">{{ deposit.account.account_name }}</h3>
                        <p class="text-sm text-gray-600 font-mono">{{ deposit.account.account_number }}</p>
                        <a
                            :href="`/admin/accounts/${deposit.account.id}`"
                            class="text-sm text-red-600 hover:text-red-700 mt-2 inline-block"
                        >
                            View Account Details →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Deposit Details -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Deposit Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Amount</p>
                        <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(deposit.amount, deposit.currency) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Reference Number</p>
                        <p class="font-semibold text-gray-900 font-mono">{{ deposit.reference_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Deposit Type</p>
                        <p class="font-semibold text-gray-900 capitalize">{{ deposit.deposit_type }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Check Number</p>
                        <p class="font-semibold text-gray-900">{{ deposit.check_number || 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Deposit Date</p>
                        <p class="font-semibold text-gray-900">{{ formatDate(deposit.deposit_date) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Available Date</p>
                        <p class="font-semibold text-gray-900">{{ deposit.available_date ? formatDate(deposit.available_date) : 'Pending approval' }}</p>
                    </div>
                </div>

                <div v-if="deposit.memo" class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-600 mb-2">Memo</p>
                    <p class="text-gray-900">{{ deposit.memo }}</p>
                </div>
            </div>

            <!-- Check Images -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Check Images</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm font-medium text-gray-700 mb-2">Front of Check</p>
                        <div
                            v-if="deposit.check_front_image"
                            class="cursor-pointer rounded-lg overflow-hidden border border-gray-200 hover:shadow-md transition-shadow"
                            @click="openImage(`/storage/${deposit.check_front_image}`)"
                        >
                            <img
                                :src="`/storage/${deposit.check_front_image}`"
                                alt="Check front"
                                class="w-full h-48 object-cover"
                            />
                            <div class="px-3 py-2 bg-gray-50 text-xs text-gray-500 text-center">
                                Click to enlarge
                            </div>
                        </div>
                        <div v-else class="flex items-center justify-center h-48 bg-gray-100 rounded-lg border border-gray-200">
                            <div class="text-center">
                                <Image :size="32" class="text-gray-400 mx-auto mb-2" />
                                <p class="text-sm text-gray-500">No image available</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-700 mb-2">Back of Check</p>
                        <div
                            v-if="deposit.check_back_image"
                            class="cursor-pointer rounded-lg overflow-hidden border border-gray-200 hover:shadow-md transition-shadow"
                            @click="openImage(`/storage/${deposit.check_back_image}`)"
                        >
                            <img
                                :src="`/storage/${deposit.check_back_image}`"
                                alt="Check back"
                                class="w-full h-48 object-cover"
                            />
                            <div class="px-3 py-2 bg-gray-50 text-xs text-gray-500 text-center">
                                Click to enlarge
                            </div>
                        </div>
                        <div v-else class="flex items-center justify-center h-48 bg-gray-100 rounded-lg border border-gray-200">
                            <div class="text-center">
                                <Image :size="32" class="text-gray-400 mx-auto mb-2" />
                                <p class="text-sm text-gray-500">No image available</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Approved Info -->
            <div v-if="deposit.status === 'approved' || deposit.status === 'completed'" class="bg-green-50 border border-green-200 rounded-lg p-6">
                <div class="flex items-start gap-3">
                    <CheckCircle :size="24" class="text-green-600 flex-shrink-0" />
                    <div class="flex-1">
                        <h3 class="font-semibold text-green-900 mb-2">Deposit Approved</h3>
                        <div class="space-y-2 text-sm text-green-800">
                            <p>This deposit has been approved and the account has been credited.</p>
                            <p v-if="deposit.available_date">
                                Funds available: <span class="font-semibold">{{ formatDate(deposit.available_date) }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rejected Info -->
            <div v-if="deposit.status === 'rejected'" class="bg-red-50 border border-red-200 rounded-lg p-6">
                <div class="flex items-start gap-3">
                    <XCircle :size="24" class="text-red-600 flex-shrink-0" />
                    <div class="flex-1">
                        <h3 class="font-semibold text-red-900 mb-2">Deposit Rejected</h3>
                        <div class="space-y-2 text-sm text-red-800">
                            <div v-if="deposit.rejection_reason" class="mt-3 p-3 bg-white rounded border border-red-200">
                                <p class="font-semibold text-red-900 mb-1">Rejection Reason:</p>
                                <p class="text-red-800">{{ deposit.rejection_reason }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Actions -->
            <div v-if="deposit.status === 'pending'" class="flex justify-end gap-3">
                <button
                    @click="showRejectModal = true"
                    class="px-6 py-3 border border-red-600 text-red-600 rounded-lg hover:bg-red-50 transition-colors font-medium"
                >
                    Reject Deposit
                </button>
                <button
                    @click="showApproveModal = true"
                    class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium"
                >
                    Approve Deposit
                </button>
            </div>
        </div>

        <!-- Approve Confirmation Modal -->
        <div
            v-if="showApproveModal"
            class="fixed inset-0 backdrop-blur-sm bg-black/30 z-50 flex items-center justify-center p-4"
            @click="showApproveModal = false"
        >
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6" @click.stop>
                <h2 class="text-xl font-bold text-gray-900 mb-4">Approve Deposit</h2>
                <p class="text-sm text-gray-600 mb-6">
                    Are you sure you want to approve this deposit? The account will be credited with the deposit amount.
                </p>
                <div class="bg-blue-50 border border-blue-200 rounded p-3 mb-6">
                    <p class="text-sm text-blue-900 font-medium">Deposit Details:</p>
                    <ul class="text-sm text-blue-800 mt-1 space-y-1">
                        <li>• User: {{ deposit.account.user.name }}</li>
                        <li>• Amount: {{ formatCurrency(deposit.amount, deposit.currency) }}</li>
                        <li>• Account: {{ deposit.account.account_number }}</li>
                        <li>• Reference: {{ deposit.reference_number }}</li>
                    </ul>
                </div>
                <div class="flex justify-end gap-3">
                    <button
                        @click="showApproveModal = false"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50"
                    >
                        Cancel
                    </button>
                    <button
                        @click="approve"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700"
                    >
                        Confirm Approval
                    </button>
                </div>
            </div>
        </div>

        <!-- Reject Confirmation Modal -->
        <div
            v-if="showRejectModal"
            class="fixed inset-0 backdrop-blur-sm bg-black/30 z-50 flex items-center justify-center p-4"
            @click="showRejectModal = false"
        >
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6" @click.stop>
                <h2 class="text-xl font-bold text-gray-900 mb-4">Reject Deposit</h2>
                <p class="text-sm text-gray-600 mb-4">
                    Please provide a reason for rejecting this deposit. The user will be notified.
                </p>
                <form @submit.prevent="reject">
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Rejection Reason *
                        </label>
                        <textarea
                            v-model="rejectForm.rejection_reason"
                            required
                            rows="4"
                            placeholder="e.g., Check image is unclear, amount does not match check..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 resize-none text-gray-900"
                        ></textarea>
                        <p v-if="rejectForm.errors.rejection_reason" class="mt-1 text-sm text-red-600">
                            {{ rejectForm.errors.rejection_reason }}
                        </p>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button
                            type="button"
                            @click="showRejectModal = false"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="rejectForm.processing"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50"
                        >
                            {{ rejectForm.processing ? 'Rejecting...' : 'Reject Deposit' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Image Lightbox Modal -->
        <div
            v-if="showImageModal"
            class="fixed inset-0 backdrop-blur-sm bg-black/70 z-50 flex items-center justify-center p-4"
            @click="showImageModal = false"
        >
            <div class="max-w-4xl w-full" @click.stop>
                <img
                    :src="activeImage"
                    alt="Check image"
                    class="w-full rounded-lg shadow-2xl"
                />
                <p class="text-center text-white text-sm mt-3">Click outside to close</p>
            </div>
        </div>
    </AdminLayout>
</template>
