<script setup lang="ts">
import { ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import {
    ArrowLeft, CheckCircle, XCircle, Send, ArrowLeftRight, Globe,
    User, Wallet, Calendar, Hash, Clock
} from 'lucide-vue-next';

interface Account {
    id: number;
    account_number: string;
    account_name: string;
    balance: number;
    currency: string;
}

interface User {
    id: number;
    name: string;
    email: string;
}

interface Beneficiary {
    id: number;
    name: string;
    bank_name: string;
    account_number: string;
}

interface Transfer {
    id: number;
    from_account_id: number;
    to_account_id?: number;
    beneficiary_id?: number;
    transfer_type: 'internal' | 'external' | 'international';
    amount: number;
    from_currency: string;
    to_currency: string;
    exchange_rate?: number;
    converted_amount?: number;
    fee: number;
    reference_number: string;
    description: string;
    status: 'pending' | 'completed' | 'failed' | 'scheduled';
    scheduled_date?: string;
    completed_at?: string;
    created_at: string;
    fromAccount?: Account & { user?: User };
    toAccount?: Account & { user?: User };
    beneficiary?: Beneficiary;
}

interface Props {
    transfer: Transfer;
}

const props = defineProps<Props>();

const showCancelModal = ref(false);

const cancelForm = useForm({
    reason: '',
});

const approveTransfer = () => {
    if (confirm('Are you sure you want to approve this transfer? This will execute the transfer immediately.')) {
        router.post(`/admin/transfers/${props.transfer.id}/approve`, {}, {
            preserveState: false,
        });
    }
};

const cancelTransfer = () => {
    cancelForm.post(`/admin/transfers/${props.transfer.id}/cancel`, {
        onSuccess: () => {
            showCancelModal.value = false;
            cancelForm.reset();
        },
    });
};

const formatCurrency = (amount: number, currency: string) => {
    return `${currency} ${amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleString('en-US', {
        month: 'long',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const getStatusColor = (status: string) => {
    switch (status) {
        case 'completed': return 'bg-green-50 text-green-700 border-green-200';
        case 'pending': return 'bg-yellow-50 text-yellow-700 border-yellow-200';
        case 'scheduled': return 'bg-blue-50 text-blue-700 border-blue-200';
        case 'failed': return 'bg-red-50 text-red-700 border-red-200';
        default: return 'bg-gray-50 text-gray-700 border-gray-200';
    }
};

const getTypeIcon = (type: string) => {
    switch (type) {
        case 'internal': return ArrowLeftRight;
        case 'external': return Send;
        case 'international': return Globe;
        default: return Send;
    }
};

const getTypeColor = (type: string) => {
    switch (type) {
        case 'internal': return 'bg-blue-50 text-blue-600';
        case 'external': return 'bg-green-50 text-green-600';
        case 'international': return 'bg-purple-50 text-purple-600';
        default: return 'bg-gray-50 text-gray-600';
    }
};
</script>

<template>
    <AdminLayout title="Transfer Details">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <Link href="/admin/transfers" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                    <ArrowLeft :size="20" />
                </Link>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Transfer Details</h1>
                    <p class="text-gray-600 mt-1">{{ transfer.reference_number }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Transfer Overview -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="flex items-start justify-between mb-6">
                            <div class="flex items-start gap-4">
                                <div
                                    class="w-16 h-16 rounded-full flex items-center justify-center"
                                    :class="getTypeColor(transfer.transfer_type)"
                                >
                                    <component :is="getTypeIcon(transfer.transfer_type)" :size="32" />
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900">
                                        {{ formatCurrency(transfer.amount, transfer.from_currency) }}
                                    </h2>
                                    <p class="text-gray-600 mt-1">{{ transfer.description }}</p>
                                    <p class="text-sm text-gray-500 mt-1 capitalize">
                                        {{ transfer.transfer_type }} Transfer
                                    </p>
                                </div>
                            </div>
                            <span
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium border"
                                :class="getStatusColor(transfer.status)"
                            >
                                {{ transfer.status.charAt(0).toUpperCase() + transfer.status.slice(1) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                                    <Hash :size="16" />
                                    <span>Reference Number</span>
                                </div>
                                <p class="font-mono text-sm font-medium text-gray-900">{{ transfer.reference_number }}</p>
                            </div>

                            <div>
                                <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                                    <Calendar :size="16" />
                                    <span>Created Date</span>
                                </div>
                                <p class="text-sm font-medium text-gray-900">{{ formatDate(transfer.created_at) }}</p>
                            </div>

                            <div v-if="transfer.fee > 0">
                                <div class="text-sm text-gray-500 mb-1">Transfer Fee</div>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ formatCurrency(transfer.fee, transfer.from_currency) }}
                                </p>
                            </div>

                            <div v-if="transfer.exchange_rate && transfer.exchange_rate !== 1">
                                <div class="text-sm text-gray-500 mb-1">Exchange Rate</div>
                                <p class="text-sm font-medium text-gray-900">
                                    1 {{ transfer.from_currency }} = {{ transfer.exchange_rate }} {{ transfer.to_currency }}
                                </p>
                            </div>

                            <div v-if="transfer.converted_amount && transfer.from_currency !== transfer.to_currency">
                                <div class="text-sm text-gray-500 mb-1">Converted Amount</div>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ formatCurrency(transfer.converted_amount, transfer.to_currency) }}
                                </p>
                            </div>

                            <div v-if="transfer.scheduled_date">
                                <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                                    <Clock :size="16" />
                                    <span>Scheduled For</span>
                                </div>
                                <p class="text-sm font-medium text-gray-900">{{ formatDate(transfer.scheduled_date) }}</p>
                            </div>

                            <div v-if="transfer.completed_at">
                                <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                                    <CheckCircle :size="16" />
                                    <span>Completed At</span>
                                </div>
                                <p class="text-sm font-medium text-gray-900">{{ formatDate(transfer.completed_at) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Transfer Flow -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Transfer Flow</h3>
                        <div class="space-y-4">
                            <!-- From Account -->
                            <div class="flex items-start gap-4 p-4 bg-red-50 rounded-lg border-2 border-red-200">
                                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <Wallet :size="24" class="text-red-600" />
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <p class="text-sm font-medium text-gray-500 mb-1">From Account</p>
                                            <p class="font-semibold text-gray-900">{{ transfer.fromAccount?.account_name }}</p>
                                            <p class="text-sm text-gray-600">{{ transfer.fromAccount?.account_number }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm text-gray-500">Current Balance</p>
                                            <p class="font-semibold text-gray-900">
                                                {{ formatCurrency(transfer.fromAccount?.balance || 0, transfer.fromAccount?.currency || 'USD') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-3 pt-3 border-t border-red-200 flex items-center gap-2">
                                        <User :size="16" class="text-gray-500" />
                                        <span class="text-sm text-gray-700">{{ transfer.fromAccount?.user?.name }}</span>
                                        <span class="text-sm text-gray-500">•</span>
                                        <span class="text-sm text-gray-500">{{ transfer.fromAccount?.user?.email }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Arrow Down -->
                            <div class="flex justify-center">
                                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                                    <ArrowLeftRight :size="24" class="text-gray-600" />
                                </div>
                            </div>

                            <!-- To Account/Beneficiary -->
                            <div class="flex items-start gap-4 p-4 bg-green-50 rounded-lg border-2 border-green-200">
                                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <Wallet :size="24" class="text-green-600" />
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <p class="text-sm font-medium text-gray-500 mb-1">To {{ transfer.toAccount ? 'Account' : 'Beneficiary' }}</p>
                                            <p class="font-semibold text-gray-900">
                                                {{ transfer.toAccount?.account_name || transfer.beneficiary?.name }}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                {{ transfer.toAccount?.account_number || transfer.beneficiary?.account_number }}
                                            </p>
                                            <p v-if="transfer.beneficiary" class="text-sm text-gray-500 mt-1">
                                                {{ transfer.beneficiary.bank_name }}
                                            </p>
                                        </div>
                                        <div v-if="transfer.toAccount" class="text-right">
                                            <p class="text-sm text-gray-500">Current Balance</p>
                                            <p class="font-semibold text-gray-900">
                                                {{ formatCurrency(transfer.toAccount?.balance || 0, transfer.toAccount?.currency || 'USD') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div v-if="transfer.toAccount?.user" class="mt-3 pt-3 border-t border-green-200 flex items-center gap-2">
                                        <User :size="16" class="text-gray-500" />
                                        <span class="text-sm text-gray-700">{{ transfer.toAccount.user.name }}</span>
                                        <span class="text-sm text-gray-500">•</span>
                                        <span class="text-sm text-gray-500">{{ transfer.toAccount.user.email }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions Sidebar -->
                <div class="space-y-6">
                    <!-- Admin Actions -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Admin Actions</h3>
                        <div class="space-y-3">
                            <button
                                v-if="transfer.status === 'pending'"
                                @click="approveTransfer"
                                class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium"
                            >
                                <CheckCircle :size="20" />
                                Approve Transfer
                            </button>

                            <button
                                v-if="['pending', 'scheduled'].includes(transfer.status)"
                                @click="showCancelModal = true"
                                class="w-full flex items-center justify-center gap-2 px-4 py-3 border-2 border-red-600 text-red-600 rounded-lg hover:bg-red-50 transition-colors font-medium"
                            >
                                <XCircle :size="20" />
                                Cancel Transfer
                            </button>

                            <p v-if="transfer.status === 'completed'" class="text-sm text-gray-500 text-center py-4">
                                This transfer has been completed successfully.
                            </p>

                            <p v-if="transfer.status === 'failed'" class="text-sm text-gray-500 text-center py-4">
                                This transfer has failed and cannot be modified.
                            </p>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Links</h3>
                        <div class="space-y-2">
                            <Link
                                :href="`/admin/users/${transfer.fromAccount?.user?.id}`"
                                class="block text-sm text-red-600 hover:text-red-700 font-medium"
                            >
                                View Sender Profile →
                            </Link>
                            <Link
                                v-if="transfer.toAccount?.user"
                                :href="`/admin/users/${transfer.toAccount.user.id}`"
                                class="block text-sm text-red-600 hover:text-red-700 font-medium"
                            >
                                View Receiver Profile →
                            </Link>
                            <Link
                                :href="`/admin/accounts/${transfer.from_account_id}`"
                                class="block text-sm text-red-600 hover:text-red-700 font-medium"
                            >
                                View Source Account →
                            </Link>
                            <Link
                                v-if="transfer.to_account_id"
                                :href="`/admin/accounts/${transfer.to_account_id}`"
                                class="block text-sm text-red-600 hover:text-red-700 font-medium"
                            >
                                View Destination Account →
                            </Link>
                        </div>
                    </div>

                    <!-- Activity Log (placeholder) -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Activity Log</h3>
                        <div class="space-y-3">
                            <div class="flex items-start gap-3 text-sm">
                                <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                                <div>
                                    <p class="font-medium text-gray-900">Transfer Created</p>
                                    <p class="text-gray-500">{{ formatDate(transfer.created_at) }}</p>
                                </div>
                            </div>
                            <div v-if="transfer.status === 'completed'" class="flex items-start gap-3 text-sm">
                                <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                                <div>
                                    <p class="font-medium text-gray-900">Transfer Completed</p>
                                    <p class="text-gray-500">{{ formatDate(transfer.completed_at!) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cancel Modal -->
        <div
            v-if="showCancelModal"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
            @click.self="showCancelModal = false"
        >
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Cancel Transfer</h3>
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                    <p class="text-sm text-red-800">
                        <strong>Warning:</strong> Cancelling this transfer will prevent it from being processed.
                        This action cannot be undone.
                    </p>
                </div>
                <form @submit.prevent="cancelTransfer" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Reason for Cancellation <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            v-model="cancelForm.reason"
                            rows="4"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                            placeholder="Explain why this transfer is being cancelled..."
                        ></textarea>
                        <div v-if="cancelForm.errors.reason" class="mt-2 text-sm text-red-600">
                            {{ cancelForm.errors.reason }}
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button
                            type="button"
                            @click="showCancelModal = false"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                        >
                            Close
                        </button>
                        <button
                            type="submit"
                            :disabled="cancelForm.processing"
                            class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors disabled:opacity-50"
                        >
                            {{ cancelForm.processing ? 'Cancelling...' : 'Cancel Transfer' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
