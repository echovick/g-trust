<script setup lang="ts">
import { ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { ArrowLeft, User as UserIcon, Wallet, CheckCircle, XCircle, Clock, AlertTriangle } from 'lucide-vue-next';

interface User {
    id: number;
    name: string;
    email: string;
}

interface Account {
    id: number;
    account_number: string;
    account_name: string;
}

interface AccountRequest {
    id: number;
    user: User;
    account_type: string;
    currency: string;
    account_name: string;
    purpose?: string;
    status: 'pending' | 'approved' | 'rejected';
    created_at: string;
    approved_at?: string;
    rejected_at?: string;
    rejection_reason?: string;
    createdAccount?: Account;
    approvedBy?: User;
}

interface Props {
    accountRequest: AccountRequest;
}

const props = defineProps<Props>();

const showRejectModal = ref(false);
const showApproveModal = ref(false);

const rejectForm = useForm({
    rejection_reason: '',
});

const approve = () => {
    router.post(`/admin/account-requests/${props.accountRequest.id}/approve`, {}, {
        onSuccess: () => {
            showApproveModal.value = false;
        }
    });
};

const reject = () => {
    rejectForm.post(`/admin/account-requests/${props.accountRequest.id}/reject`, {
        onSuccess: () => {
            showRejectModal.value = false;
            rejectForm.reset();
        }
    });
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

const getStatusColor = (status: string) => {
    switch (status) {
        case 'approved': return 'text-green-700 bg-green-50';
        case 'pending': return 'text-yellow-700 bg-yellow-50';
        case 'rejected': return 'text-red-700 bg-red-50';
        default: return 'text-gray-700 bg-gray-50';
    }
};

const getStatusIcon = (status: string) => {
    switch (status) {
        case 'approved': return CheckCircle;
        case 'pending': return Clock;
        case 'rejected': return XCircle;
        default: return Clock;
    }
};
</script>

<template>
    <AdminLayout title="Account Request Details">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <a
                    href="/admin/account-requests"
                    class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
                >
                    <ArrowLeft :size="20" class="text-gray-600" />
                </a>
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900">Account Request Details</h1>
                    <p class="text-gray-600 mt-1">Review and manage this account request</p>
                </div>
                <div>
                    <span :class="[
                        'inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium',
                        getStatusColor(accountRequest.status)
                    ]">
                        <component :is="getStatusIcon(accountRequest.status)" :size="18" />
                        {{ accountRequest.status.charAt(0).toUpperCase() + accountRequest.status.slice(1) }}
                    </span>
                </div>
            </div>

            <!-- Requester Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Requester Information</h2>
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center flex-shrink-0">
                        <UserIcon :size="24" class="text-gray-500" />
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900">{{ accountRequest.user.name }}</h3>
                        <p class="text-sm text-gray-600">{{ accountRequest.user.email }}</p>
                        <a
                            :href="`/admin/users/${accountRequest.user.id}`"
                            class="text-sm text-red-600 hover:text-red-700 mt-2 inline-block"
                        >
                            View User Profile →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Account Request Details -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Account Request Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Account Name</p>
                        <p class="font-semibold text-gray-900">{{ accountRequest.account_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Account Type</p>
                        <p class="font-semibold text-gray-900 capitalize">{{ accountRequest.account_type }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Currency</p>
                        <p class="font-semibold text-gray-900">{{ accountRequest.currency }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Requested On</p>
                        <p class="font-semibold text-gray-900">{{ formatDate(accountRequest.created_at) }}</p>
                    </div>
                </div>

                <div v-if="accountRequest.purpose" class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-600 mb-2">Purpose / Reason</p>
                    <p class="text-gray-900">{{ accountRequest.purpose }}</p>
                </div>
            </div>

            <!-- Approved Info -->
            <div v-if="accountRequest.status === 'approved'" class="bg-green-50 border border-green-200 rounded-lg p-6">
                <div class="flex items-start gap-3">
                    <CheckCircle :size="24" class="text-green-600 flex-shrink-0" />
                    <div class="flex-1">
                        <h3 class="font-semibold text-green-900 mb-2">Request Approved</h3>
                        <div class="space-y-2 text-sm text-green-800">
                            <p>This request was approved on <span class="font-semibold">{{ formatDate(accountRequest.approved_at!) }}</span></p>
                            <p v-if="accountRequest.approvedBy">
                                Approved by: <span class="font-semibold">{{ accountRequest.approvedBy.name }}</span>
                            </p>
                            <p v-if="accountRequest.createdAccount" class="mt-4 p-3 bg-white rounded border border-green-200">
                                <span class="font-semibold text-green-900">Account Created:</span><br>
                                Account Number: <span class="font-mono font-semibold">{{ accountRequest.createdAccount.account_number }}</span><br>
                                Account Name: {{ accountRequest.createdAccount.account_name }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rejected Info -->
            <div v-if="accountRequest.status === 'rejected'" class="bg-red-50 border border-red-200 rounded-lg p-6">
                <div class="flex items-start gap-3">
                    <XCircle :size="24" class="text-red-600 flex-shrink-0" />
                    <div class="flex-1">
                        <h3 class="font-semibold text-red-900 mb-2">Request Rejected</h3>
                        <div class="space-y-2 text-sm text-red-800">
                            <p>This request was rejected on <span class="font-semibold">{{ formatDate(accountRequest.rejected_at!) }}</span></p>
                            <div v-if="accountRequest.rejection_reason" class="mt-3 p-3 bg-white rounded border border-red-200">
                                <p class="font-semibold text-red-900 mb-1">Rejection Reason:</p>
                                <p class="text-red-800">{{ accountRequest.rejection_reason }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Actions -->
            <div v-if="accountRequest.status === 'pending'" class="flex justify-end gap-3">
                <button
                    @click="showRejectModal = true"
                    class="px-6 py-3 border border-red-600 text-red-600 rounded-lg hover:bg-red-50 transition-colors font-medium"
                >
                    Reject Request
                </button>
                <button
                    @click="showApproveModal = true"
                    class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium"
                >
                    Approve Request
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
                <h2 class="text-xl font-bold text-gray-900 mb-4">Approve Account Request</h2>
                <p class="text-sm text-gray-600 mb-6">
                    Are you sure you want to approve this account request? A new account will be created with a unique account number.
                </p>
                <div class="bg-blue-50 border border-blue-200 rounded p-3 mb-6">
                    <p class="text-sm text-blue-900 font-medium">Account Details:</p>
                    <ul class="text-sm text-blue-800 mt-1 space-y-1">
                        <li>• User: {{ accountRequest.user.name }}</li>
                        <li>• Type: {{ accountRequest.account_type }}</li>
                        <li>• Currency: {{ accountRequest.currency }}</li>
                        <li>• Name: {{ accountRequest.account_name }}</li>
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
                <h2 class="text-xl font-bold text-gray-900 mb-4">Reject Account Request</h2>
                <p class="text-sm text-gray-600 mb-4">
                    Please provide a reason for rejecting this account request. The user will be notified.
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
                            placeholder="e.g., Incomplete information, does not meet requirements..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 resize-none"
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
                            {{ rejectForm.processing ? 'Rejecting...' : 'Reject Request' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
