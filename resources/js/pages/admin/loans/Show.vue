<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { ArrowLeft, Banknote, CheckCircle, XCircle, Edit, AlertTriangle, Calendar, DollarSign, TrendingUp, User, Wallet } from 'lucide-vue-next';

interface Loan {
    id: number;
    loan_number: string;
    loan_type: string;
    loan_amount: number;
    outstanding_balance: number;
    total_paid: number;
    interest_rate: number;
    monthly_payment: number;
    status: string;
    currency: string;
    term_months: number;
    remaining_months: number;
    origination_date: string;
    maturity_date: string;
    next_payment_date: string;
    auto_pay: boolean;
    user: {
        id: number;
        name: string;
        email: string;
    };
    linkedAccount?: {
        id: number;
        account_number: string;
        account_name: string;
        balance: number;
        available_balance: number;
    };
}

interface PaymentSchedule {
    payment_number: number;
    payment_date: string;
    payment_amount: number;
    principal: number;
    interest: number;
    balance: number;
    status: string;
}

interface Props {
    loan: Loan;
    payment_schedule: PaymentSchedule[];
}

const props = defineProps<Props>();

const showApproveModal = ref(false);
const showAdjustModal = ref(false);
const showWriteOffModal = ref(false);

const approveForm = useForm({
    disburse_to_account_id: props.loan.linkedAccount?.id || null,
});

const adjustForm = useForm({
    new_monthly_payment: props.loan.monthly_payment,
    reason: '',
});

const writeOffForm = useForm({
    reason: '',
});

const approve = () => {
    approveForm.post(`/admin/loans/${props.loan.id}/approve`, {
        preserveScroll: true,
        onSuccess: () => {
            showApproveModal.value = false;
        },
    });
};

const adjustPayment = () => {
    adjustForm.post(`/admin/loans/${props.loan.id}/adjust-payment`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            showAdjustModal.value = false;
            adjustForm.reset('reason');
        },
    });
};

const writeOff = () => {
    writeOffForm.post(`/admin/loans/${props.loan.id}/write-off`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            showWriteOffModal.value = false;
            writeOffForm.reset();
        },
    });
};

const toggleAutoPay = () => {
    router.post(`/admin/loans/${props.loan.id}/toggle-auto-pay`, {}, {
        preserveScroll: true,
    });
};

const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
        pending: 'bg-yellow-100 text-yellow-800',
        active: 'bg-green-100 text-green-800',
        paid_off: 'bg-blue-100 text-blue-800',
        defaulted: 'bg-red-100 text-red-800',
        cancelled: 'bg-gray-100 text-gray-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const formatCurrency = (amount: number, currency: string) => {
    return `${currency} ${amount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
};

const formatPercentage = (value: number) => {
    return `${value.toFixed(2)}%`;
};

const calculateProgress = () => {
    return ((props.loan.term_months - props.loan.remaining_months) / props.loan.term_months) * 100;
};
</script>

<template>
    <Head :title="`Loan ${loan.loan_number}`" />

    <AdminLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div>
                <Link href="/admin/loans" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4">
                    <ArrowLeft class="h-4 w-4 mr-1" />
                    Back to Loans
                </Link>
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Loan Details</h1>
                        <p class="mt-1 text-sm text-gray-500">{{ loan.loan_number }}</p>
                    </div>
                    <span :class="getStatusColor(loan.status)" class="px-3 py-1 text-sm font-semibold rounded-full uppercase">
                        {{ loan.status.replace('_', ' ') }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Loan Info -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Loan Overview -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Loan Overview</h2>
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <div class="text-xs text-gray-500 uppercase">Loan Amount</div>
                                <div class="text-2xl font-bold text-gray-900 mt-1">{{ formatCurrency(loan.loan_amount, loan.currency) }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500 uppercase">Outstanding Balance</div>
                                <div class="text-2xl font-bold text-orange-600 mt-1">{{ formatCurrency(loan.outstanding_balance, loan.currency) }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500 uppercase">Interest Rate</div>
                                <div class="text-xl font-semibold text-gray-900 mt-1">{{ formatPercentage(loan.interest_rate) }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500 uppercase">Monthly Payment</div>
                                <div class="text-xl font-semibold text-gray-900 mt-1">{{ formatCurrency(loan.monthly_payment, loan.currency) }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500 uppercase">Total Paid</div>
                                <div class="text-xl font-semibold text-green-600 mt-1">{{ formatCurrency(loan.total_paid, loan.currency) }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500 uppercase">Loan Type</div>
                                <div class="text-xl font-semibold text-gray-900 mt-1 capitalize">{{ loan.loan_type }}</div>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mt-6">
                            <div class="flex justify-between text-sm text-gray-500 mb-2">
                                <span>Loan Progress</span>
                                <span>{{ loan.term_months - loan.remaining_months }} / {{ loan.term_months }} months</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-orange-600 h-3 rounded-full transition-all" :style="{ width: calculateProgress() + '%' }"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Loan Actions -->
                    <div v-if="loan.status === 'pending' || loan.status === 'active'" class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Loan Actions</h2>
                        <div class="grid grid-cols-2 gap-4">
                            <button
                                v-if="loan.status === 'pending'"
                                @click="showApproveModal = true"
                                class="inline-flex items-center justify-center px-4 py-2 border border-green-300 rounded-md shadow-sm text-sm font-medium text-green-700 bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                            >
                                <CheckCircle class="h-4 w-4 mr-2" />
                                Approve Loan
                            </button>

                            <button
                                v-if="loan.status === 'active'"
                                @click="showAdjustModal = true"
                                class="inline-flex items-center justify-center px-4 py-2 border border-blue-300 rounded-md shadow-sm text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                <Edit class="h-4 w-4 mr-2" />
                                Adjust Payment
                            </button>

                            <button
                                v-if="loan.status === 'active'"
                                @click="showWriteOffModal = true"
                                class="inline-flex items-center justify-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                            >
                                <AlertTriangle class="h-4 w-4 mr-2" />
                                Write Off
                            </button>

                            <button
                                v-if="loan.linkedAccount"
                                @click="toggleAutoPay"
                                :class="loan.auto_pay ? 'border-orange-300 text-orange-700 bg-orange-50 hover:bg-orange-100' : 'border-gray-300 text-gray-700 bg-gray-50 hover:bg-gray-100'"
                                class="inline-flex items-center justify-center px-4 py-2 border rounded-md shadow-sm text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
                            >
                                {{ loan.auto_pay ? 'Disable' : 'Enable' }} Auto-Pay
                            </button>
                        </div>
                    </div>

                    <!-- Payment Schedule -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Payment Schedule</h2>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Payment</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Principal</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Interest</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Balance</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr v-for="payment in payment_schedule" :key="payment.payment_number" :class="payment.status === 'paid' ? 'bg-green-50' : ''">
                                        <td class="px-4 py-2 text-sm text-gray-900">{{ payment.payment_number }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-500">{{ new Date(payment.payment_date).toLocaleDateString() }}</td>
                                        <td class="px-4 py-2 text-sm font-medium text-gray-900">{{ formatCurrency(payment.payment_amount, loan.currency) }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-900">{{ formatCurrency(payment.principal, loan.currency) }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-500">{{ formatCurrency(payment.interest, loan.currency) }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-900">{{ formatCurrency(payment.balance, loan.currency) }}</td>
                                        <td class="px-4 py-2">
                                            <span :class="payment.status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'" class="px-2 py-1 text-xs font-semibold rounded-full uppercase">
                                                {{ payment.status }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr v-if="payment_schedule.length === 0">
                                        <td colspan="7" class="px-4 py-4 text-center text-sm text-gray-500">
                                            No payment schedule available
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Borrower Info -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                            <User class="h-5 w-5 mr-2" />
                            Borrower
                        </h2>
                        <div class="space-y-3">
                            <div>
                                <div class="text-xs text-gray-500">Name</div>
                                <div class="text-sm font-medium text-gray-900">{{ loan.user.name }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Email</div>
                                <div class="text-sm font-medium text-gray-900">{{ loan.user.email }}</div>
                            </div>
                            <div class="pt-3 border-t">
                                <Link :href="`/admin/users/${loan.user.id}`" class="text-sm text-orange-600 hover:text-orange-900">
                                    View User Profile →
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Linked Account -->
                    <div v-if="loan.linkedAccount" class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                            <Wallet class="h-5 w-5 mr-2" />
                            Linked Account
                        </h2>
                        <div class="space-y-3">
                            <div>
                                <div class="text-xs text-gray-500">Account Number</div>
                                <div class="text-sm font-medium text-gray-900">{{ loan.linkedAccount.account_number }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Account Name</div>
                                <div class="text-sm font-medium text-gray-900">{{ loan.linkedAccount.account_name }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Available Balance</div>
                                <div class="text-sm font-medium text-gray-900">{{ formatCurrency(loan.linkedAccount.available_balance, loan.currency) }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Auto-Pay</div>
                                <div class="text-sm font-medium" :class="loan.auto_pay ? 'text-green-600' : 'text-gray-500'">
                                    {{ loan.auto_pay ? 'Enabled' : 'Disabled' }}
                                </div>
                            </div>
                            <div class="pt-3 border-t">
                                <Link :href="`/admin/accounts/${loan.linkedAccount.id}`" class="text-sm text-orange-600 hover:text-orange-900">
                                    View Account →
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Loan Timeline -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                            <Calendar class="h-5 w-5 mr-2" />
                            Timeline
                        </h2>
                        <div class="space-y-3">
                            <div>
                                <div class="text-xs text-gray-500">Origination Date</div>
                                <div class="text-sm font-medium text-gray-900">{{ new Date(loan.origination_date).toLocaleDateString() }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Next Payment</div>
                                <div class="text-sm font-medium text-gray-900">{{ new Date(loan.next_payment_date).toLocaleDateString() }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Maturity Date</div>
                                <div class="text-sm font-medium text-gray-900">{{ new Date(loan.maturity_date).toLocaleDateString() }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Remaining Term</div>
                                <div class="text-sm font-medium text-gray-900">{{ loan.remaining_months }} months</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approve Modal -->
        <div v-if="showApproveModal" class="fixed z-10 inset-0 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showApproveModal = false"></div>
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div>
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                            <CheckCircle class="h-6 w-6 text-green-600" />
                        </div>
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Approve Loan</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    This will approve the loan and disburse {{ formatCurrency(loan.loan_amount, loan.currency) }} to the selected account.
                                </p>
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 text-left mb-1">Disburse to Account *</label>
                                <select
                                    v-model.number="approveForm.disburse_to_account_id"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm rounded-md"
                                    required
                                >
                                    <option :value="null" disabled>Select account...</option>
                                    <option v-if="loan.linkedAccount" :value="loan.linkedAccount.id">
                                        {{ loan.linkedAccount.account_number }} - {{ loan.linkedAccount.account_name }}
                                    </option>
                                </select>
                                <p v-if="approveForm.errors.disburse_to_account_id" class="mt-1 text-sm text-red-600">{{ approveForm.errors.disburse_to_account_id }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <button
                            @click="approve"
                            :disabled="approveForm.processing || !approveForm.disburse_to_account_id"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:col-start-2 sm:text-sm disabled:opacity-50"
                        >
                            Approve & Disburse
                        </button>
                        <button
                            @click="showApproveModal = false"
                            type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:mt-0 sm:col-start-1 sm:text-sm"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Adjust Payment Modal -->
        <div v-if="showAdjustModal" class="fixed z-10 inset-0 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showAdjustModal = false"></div>
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div>
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                            <Edit class="h-6 w-6 text-blue-600" />
                        </div>
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Adjust Monthly Payment</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Adjust the monthly payment amount for this loan.
                                </p>
                            </div>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 text-left mb-1">New Monthly Payment ({{ loan.currency }}) *</label>
                                    <input
                                        v-model.number="adjustForm.new_monthly_payment"
                                        type="number"
                                        step="0.01"
                                        min="0.01"
                                        class="shadow-sm focus:ring-orange-500 focus:border-orange-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                        required
                                    />
                                    <p v-if="adjustForm.errors.new_monthly_payment" class="mt-1 text-sm text-red-600">{{ adjustForm.errors.new_monthly_payment }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 text-left mb-1">Reason *</label>
                                    <textarea
                                        v-model="adjustForm.reason"
                                        rows="3"
                                        class="shadow-sm focus:ring-orange-500 focus:border-orange-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                        placeholder="Enter reason for adjustment..."
                                        required
                                    />
                                    <p v-if="adjustForm.errors.reason" class="mt-1 text-sm text-red-600">{{ adjustForm.errors.reason }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <button
                            @click="adjustPayment"
                            :disabled="adjustForm.processing || !adjustForm.new_monthly_payment || !adjustForm.reason"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-2 sm:text-sm disabled:opacity-50"
                        >
                            Adjust Payment
                        </button>
                        <button
                            @click="showAdjustModal = false"
                            type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:mt-0 sm:col-start-1 sm:text-sm"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Write Off Modal -->
        <div v-if="showWriteOffModal" class="fixed z-10 inset-0 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showWriteOffModal = false"></div>
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div>
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                            <AlertTriangle class="h-6 w-6 text-red-600" />
                        </div>
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Write Off Loan</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    This will mark the loan as defaulted and write off the outstanding balance of {{ formatCurrency(loan.outstanding_balance, loan.currency) }}. This action should only be used when the loan is deemed uncollectible.
                                </p>
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 text-left mb-1">Reason *</label>
                                <textarea
                                    v-model="writeOffForm.reason"
                                    rows="3"
                                    class="shadow-sm focus:ring-orange-500 focus:border-orange-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                    placeholder="Enter detailed reason for write-off..."
                                    required
                                />
                                <p v-if="writeOffForm.errors.reason" class="mt-1 text-sm text-red-600">{{ writeOffForm.errors.reason }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <button
                            @click="writeOff"
                            :disabled="writeOffForm.processing || !writeOffForm.reason"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:col-start-2 sm:text-sm disabled:opacity-50"
                        >
                            Write Off Loan
                        </button>
                        <button
                            @click="showWriteOffModal = false"
                            type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:mt-0 sm:col-start-1 sm:text-sm"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
