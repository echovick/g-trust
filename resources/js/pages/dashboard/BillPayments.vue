<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { Plus, Receipt, Calendar, Repeat, Trash2, CheckCircle, Clock, XCircle } from 'lucide-vue-next';
import DashboardLayout from '@/layouts/DashboardLayout.vue';

interface Account {
    id: number;
    account_name: string;
    account_number: string;
    account_type: string;
    currency: string;
}

interface BillPayment {
    id: number;
    payee_name: string;
    payee_account_number?: string;
    payee_type: 'utility' | 'credit_card' | 'loan' | 'insurance' | 'other';
    amount: number;
    currency: string;
    reference_number: string;
    memo?: string;
    status: 'pending' | 'completed' | 'failed' | 'cancelled';
    frequency: 'once' | 'weekly' | 'monthly' | 'quarterly' | 'annually';
    scheduled_date: string;
    next_payment_date?: string;
    completed_at?: string;
    auto_pay: boolean;
    account: Account;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedBillPayments {
    data: BillPayment[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: PaginationLink[];
}

interface Props {
    billPayments: PaginatedBillPayments;
    accounts: Account[];
}

const props = defineProps<Props>();

const deleteBillPayment = (id: number) => {
    if (confirm('Are you sure you want to delete this bill payment?')) {
        router.delete(`/dashboard/bill-payments/${id}`);
    }
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const getPayeeTypeLabel = (type: string) => {
    return type.replace('_', ' ').split(' ').map(word =>
        word.charAt(0).toUpperCase() + word.slice(1)
    ).join(' ');
};

const getStatusIcon = (status: string) => {
    switch (status) {
        case 'completed': return CheckCircle;
        case 'pending': return Clock;
        default: return XCircle;
    }
};

const getStatusColor = (status: string) => {
    switch (status) {
        case 'completed': return 'bg-green-50 text-green-700';
        case 'pending': return 'bg-yellow-50 text-yellow-700';
        default: return 'bg-red-50 text-red-700';
    }
};
</script>

<template>
    <DashboardLayout title="Bill Payments">
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600">Schedule and manage your bill payments</p>
                </div>
                <Link
                    href="/dashboard/bill-payments/create"
                    class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center gap-2"
                >
                    <Plus :size="20" />
                    New Payment
                </Link>
            </div>
        </div>

        <div v-if="billPayments.data.length > 0" class="space-y-4">
            <div v-for="payment in billPayments.data" :key="payment.id"
                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow"
            >
                <div class="flex items-start justify-between">
                    <div class="flex items-start gap-4 flex-1">
                        <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <Receipt :size="24" class="text-blue-500" />
                        </div>
                        <div class="flex-1">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <h3 class="font-semibold text-gray-900 text-lg">{{ payment.payee_name }}</h3>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-sm text-gray-500">{{ getPayeeTypeLabel(payment.payee_type) }}</span>
                                        <span class="text-gray-300">•</span>
                                        <span class="text-sm text-gray-500">{{ payment.account.account_name }}</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-gray-900">
                                        {{ payment.currency }} {{ payment.amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                                <div class="flex items-center gap-2 text-sm">
                                    <Calendar :size="16" class="text-gray-400" />
                                    <span class="text-gray-600">Scheduled:</span>
                                    <span class="font-medium text-gray-900">{{ formatDate(payment.scheduled_date) }}</span>
                                </div>

                                <div v-if="payment.frequency !== 'once'" class="flex items-center gap-2 text-sm">
                                    <Repeat :size="16" class="text-gray-400" />
                                    <span class="text-gray-600">Frequency:</span>
                                    <span class="font-medium text-gray-900 capitalize">{{ payment.frequency }}</span>
                                </div>

                                <div v-if="payment.next_payment_date" class="flex items-center gap-2 text-sm">
                                    <Calendar :size="16" class="text-gray-400" />
                                    <span class="text-gray-600">Next Payment:</span>
                                    <span class="font-medium text-gray-900">{{ formatDate(payment.next_payment_date) }}</span>
                                </div>
                            </div>

                            <div v-if="payment.memo" class="mt-3 text-sm text-gray-600">
                                <span class="font-medium">Memo:</span> {{ payment.memo }}
                            </div>

                            <div class="flex items-center gap-3 mt-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium"
                                    :class="getStatusColor(payment.status)"
                                >
                                    <component :is="getStatusIcon(payment.status)" :size="14" />
                                    {{ payment.status.charAt(0).toUpperCase() + payment.status.slice(1) }}
                                </span>

                                <span v-if="payment.auto_pay" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-purple-50 text-purple-700">
                                    <Repeat :size="14" />
                                    Auto-Pay Enabled
                                </span>

                                <span class="text-xs text-gray-500">Ref: {{ payment.reference_number }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="ml-4">
                        <button
                            @click="deleteBillPayment(payment.id)"
                            class="p-2 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                            title="Delete"
                        >
                            <Trash2 :size="18" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="billPayments.last_page > 1" class="bg-white rounded-xl shadow-sm border border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        Showing {{ ((billPayments.current_page - 1) * billPayments.per_page) + 1 }}
                        to {{ Math.min(billPayments.current_page * billPayments.per_page, billPayments.total) }}
                        of {{ billPayments.total }} bill payments
                    </div>
                    <div class="flex gap-2">
                        <Link
                            v-for="link in billPayments.links"
                            :key="link.label"
                            :href="link.url || '#'"
                            :class="[
                                'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                                link.active
                                    ? 'bg-red-500 text-white'
                                    : link.url
                                        ? 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                        : 'bg-gray-50 text-gray-400 cursor-not-allowed'
                            ]"
                            v-html="link.label"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <Receipt :size="32" class="text-gray-400" />
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No bill payments yet</h3>
            <p class="text-gray-600 mb-6">Schedule your first bill payment to get started</p>
            <Link
                href="/dashboard/bill-payments/create"
                class="inline-flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg font-medium transition-colors"
            >
                <Plus :size="20" />
                Schedule Payment
            </Link>
        </div>
    </DashboardLayout>
</template>
