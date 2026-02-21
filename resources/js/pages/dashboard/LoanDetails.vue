<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ArrowLeft, TrendingUp, Calendar, DollarSign, Percent, CheckCircle, Info } from 'lucide-vue-next';
import DashboardLayout from '@/layouts/DashboardLayout.vue';

interface Account {
    id: number;
    account_name: string;
    account_number: string;
}

interface Loan {
    id: number;
    loan_type: 'personal' | 'mortgage' | 'auto' | 'business' | 'student';
    loan_amount: number;
    outstanding_balance: number;
    interest_rate: number;
    term_months: number;
    monthly_payment: number;
    next_payment_date: string;
    start_date: string;
    end_date: string;
    status: 'active' | 'paid_off' | 'defaulted' | 'pending';
    auto_pay: boolean;
    currency: string;
    linkedAccount?: Account;
}

interface Props {
    loan: Loan;
}

const props = defineProps<Props>();

const getLoanTypeLabel = (type: string) => {
    const labels: Record<string, string> = {
        'personal': 'Personal Loan',
        'mortgage': 'Mortgage',
        'auto': 'Auto Loan',
        'business': 'Business Loan',
        'student': 'Student Loan'
    };
    return labels[type] || type;
};

const getLoanTypeColor = (type: string) => {
    const colors: Record<string, string> = {
        'personal': 'from-blue-500 to-blue-600',
        'mortgage': 'from-purple-500 to-purple-600',
        'auto': 'from-green-500 to-green-600',
        'business': 'from-orange-500 to-orange-600',
        'student': 'from-pink-500 to-pink-600'
    };
    return colors[type] || 'from-gray-500 to-gray-600';
};

const calculateProgress = () => {
    const paid = props.loan.loan_amount - props.loan.outstanding_balance;
    return Math.round((paid / props.loan.loan_amount) * 100);
};

const calculateTotalInterest = () => {
    const totalPayments = props.loan.monthly_payment * props.loan.term_months;
    const totalInterest = totalPayments - props.loan.loan_amount;
    return totalInterest;
};

const calculateRemainingPayments = () => {
    const totalPaid = props.loan.loan_amount - props.loan.outstanding_balance;
    const paymentsMade = Math.floor(totalPaid / props.loan.monthly_payment);
    return props.loan.term_months - paymentsMade;
};

const toggleAutoPay = () => {
    router.post(`/dashboard/loans/${props.loan.id}/toggle-autopay`);
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const formatCurrency = (amount: number) => {
    return `${props.loan.currency} ${amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
};
</script>

<template>
    <DashboardLayout :title="`${getLoanTypeLabel(loan.loan_type)} Details`">
        <div class="mb-8">
            <button
                @click="router.visit('/dashboard/loans')"
                class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-4 transition-colors"
            >
                <ArrowLeft :size="20" />
                Back to Loans
            </button>
            <p class="text-gray-600">Loan details and payment information</p>
        </div>

        <!-- Loan Header Card -->
        <div class="bg-gradient-to-r rounded-xl shadow-lg p-8 mb-6 text-white" :class="getLoanTypeColor(loan.loan_type)">
            <div class="flex items-start justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <TrendingUp :size="24" />
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold">{{ formatCurrency(loan.outstanding_balance) }}</h2>
                        <p class="text-white/90">Outstanding Balance</p>
                    </div>
                </div>
                <span class="px-4 py-2 bg-white/20 rounded-full text-sm font-medium">
                    {{ loan.status.charAt(0).toUpperCase() + loan.status.slice(1).replace('_', ' ') }}
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <div class="text-white/80 text-sm mb-1">Monthly Payment</div>
                    <div class="text-xl font-bold">{{ formatCurrency(loan.monthly_payment) }}</div>
                </div>
                <div>
                    <div class="text-white/80 text-sm mb-1">Interest Rate</div>
                    <div class="text-xl font-bold">{{ loan.interest_rate }}% APR</div>
                </div>
                <div>
                    <div class="text-white/80 text-sm mb-1">Next Payment</div>
                    <div class="text-xl font-bold">{{ formatDate(loan.next_payment_date).split(',')[0] }}</div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Repayment Progress -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Repayment Progress</h3>

                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-gray-600">Amount Paid</span>
                            <span class="text-sm font-semibold text-gray-900">
                                {{ formatCurrency(loan.loan_amount - loan.outstanding_balance) }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-4">
                            <div
                                class="bg-gradient-to-r h-4 rounded-full transition-all flex items-center justify-end pr-2"
                                :class="getLoanTypeColor(loan.loan_type)"
                                :style="{ width: `${calculateProgress()}%` }"
                            >
                                <span class="text-xs font-bold text-white">{{ calculateProgress() }}%</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-xs text-gray-500">{{ formatCurrency(0) }}</span>
                            <span class="text-xs text-gray-500">{{ formatCurrency(loan.loan_amount) }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-sm text-gray-600 mb-1">Remaining Payments</div>
                            <div class="text-2xl font-bold text-gray-900">{{ calculateRemainingPayments() }}</div>
                            <div class="text-xs text-gray-500">of {{ loan.term_months }} months</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-sm text-gray-600 mb-1">Estimated Total Interest</div>
                            <div class="text-2xl font-bold text-gray-900">
                                {{ formatCurrency(calculateTotalInterest()) }}
                            </div>
                            <div class="text-xs text-gray-500">Over loan term</div>
                        </div>
                    </div>
                </div>

                <!-- Loan Details -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Loan Details</h3>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between py-3 border-b">
                            <span class="text-sm text-gray-600">Original Loan Amount</span>
                            <span class="font-semibold text-gray-900">{{ formatCurrency(loan.loan_amount) }}</span>
                        </div>
                        <div class="flex items-center justify-between py-3 border-b">
                            <span class="text-sm text-gray-600">Loan Term</span>
                            <span class="font-semibold text-gray-900">{{ loan.term_months }} months</span>
                        </div>
                        <div class="flex items-center justify-between py-3 border-b">
                            <span class="text-sm text-gray-600">Start Date</span>
                            <span class="font-semibold text-gray-900">{{ formatDate(loan.start_date) }}</span>
                        </div>
                        <div class="flex items-center justify-between py-3 border-b">
                            <span class="text-sm text-gray-600">End Date</span>
                            <span class="font-semibold text-gray-900">{{ formatDate(loan.end_date) }}</span>
                        </div>
                        <div class="flex items-center justify-between py-3">
                            <span class="text-sm text-gray-600">Linked Account</span>
                            <span class="font-semibold text-gray-900">
                                {{ loan.linkedAccount?.account_name || 'Not linked' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Auto-Pay Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <CheckCircle :size="20" :class="loan.auto_pay ? 'text-green-500' : 'text-gray-400'" />
                        <h3 class="text-lg font-bold text-gray-900">Auto-Pay</h3>
                    </div>

                    <p class="text-sm text-gray-600 mb-4">
                        {{ loan.auto_pay
                            ? 'Automatic payments are enabled. Your monthly payment will be deducted automatically.'
                            : 'Enable auto-pay to never miss a payment and avoid late fees.'
                        }}
                    </p>

                    <button
                        @click="toggleAutoPay"
                        class="w-full px-4 py-3 rounded-lg font-medium transition-colors"
                        :class="loan.auto_pay
                            ? 'bg-red-50 text-red-700 hover:bg-red-100 border border-red-200'
                            : 'bg-green-500 text-white hover:bg-green-600'"
                    >
                        {{ loan.auto_pay ? 'Disable Auto-Pay' : 'Enable Auto-Pay' }}
                    </button>
                </div>

                <!-- Next Payment Card -->
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <Calendar :size="20" class="text-blue-600" />
                        <h3 class="text-lg font-bold text-blue-900">Next Payment Due</h3>
                    </div>

                    <div class="text-3xl font-bold text-blue-900 mb-2">
                        {{ formatCurrency(loan.monthly_payment) }}
                    </div>

                    <div class="text-sm text-blue-700 mb-4">
                        Due on {{ formatDate(loan.next_payment_date) }}
                    </div>

                    <div class="bg-white rounded-lg p-3 text-sm text-blue-800">
                        <div class="flex items-start gap-2">
                            <Info :size="16" class="mt-0.5 flex-shrink-0" />
                            <span>
                                Make sure you have sufficient funds in your linked account before the due date.
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Help Card -->
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="font-bold text-gray-900 mb-3">Need Help?</h3>
                    <p class="text-sm text-gray-600 mb-4">
                        If you're experiencing financial difficulties, we're here to help.
                    </p>
                    <a href="/support/contact" class="text-sm font-medium text-red-500 hover:text-red-600">
                        Contact Support →
                    </a>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>
