<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { TrendingUp, Calendar, DollarSign, Percent, CheckCircle, Clock, ArrowRight } from 'lucide-vue-next';
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
    loans: Loan[];
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

const calculateProgress = (loan: Loan) => {
    const paid = loan.loan_amount - loan.outstanding_balance;
    return Math.round((paid / loan.loan_amount) * 100);
};

const getStatusColor = (status: string) => {
    switch (status) {
        case 'active': return 'bg-green-50 text-green-700';
        case 'paid_off': return 'bg-blue-50 text-blue-700';
        case 'defaulted': return 'bg-red-50 text-red-700';
        default: return 'bg-yellow-50 text-yellow-700';
    }
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};
</script>

<template>
    <DashboardLayout title="My Loans">
        <div class="mb-8">
            <p class="text-gray-600">Track and manage your loan payments</p>
        </div>

        <div v-if="loans.length > 0" class="space-y-6">
            <Link
                v-for="loan in loans"
                :key="loan.id"
                :href="`/dashboard/loans/${loan.id}`"
                class="block bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all overflow-hidden group"
            >
                <!-- Header Section -->
                <div class="bg-gradient-to-r p-6 text-white" :class="getLoanTypeColor(loan.loan_type)">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <TrendingUp :size="24" />
                                <h3 class="text-xl font-bold">{{ getLoanTypeLabel(loan.loan_type) }}</h3>
                            </div>
                            <p class="text-white/90 text-sm">
                                {{ loan.linkedAccount?.account_name || 'No linked account' }}
                            </p>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-white/20">
                            {{ loan.status.charAt(0).toUpperCase() + loan.status.slice(1).replace('_', ' ') }}
                        </span>
                    </div>
                </div>

                <!-- Content Section -->
                <div class="p-6">
                    <!-- Balance and Payment Info -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div>
                            <div class="text-sm text-gray-600 mb-1">Outstanding Balance</div>
                            <div class="text-2xl font-bold text-gray-900">
                                {{ loan.currency }} {{ loan.outstanding_balance.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                of {{ loan.currency }} {{ loan.loan_amount.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
                            </div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-600 mb-1">Monthly Payment</div>
                            <div class="text-2xl font-bold text-gray-900">
                                {{ loan.currency }} {{ loan.monthly_payment.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
                            </div>
                            <div class="flex items-center gap-1 mt-1">
                                <Percent :size="12" class="text-gray-400" />
                                <span class="text-xs text-gray-500">{{ loan.interest_rate }}% APR</span>
                            </div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-600 mb-1">Next Payment</div>
                            <div class="text-lg font-semibold text-gray-900">
                                {{ formatDate(loan.next_payment_date) }}
                            </div>
                            <div v-if="loan.auto_pay" class="flex items-center gap-1 mt-1">
                                <CheckCircle :size="12" class="text-green-500" />
                                <span class="text-xs text-green-600 font-medium">Auto-pay enabled</span>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Repayment Progress</span>
                            <span class="text-sm font-semibold text-gray-900">{{ calculateProgress(loan) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div
                                class="bg-gradient-to-r h-3 rounded-full transition-all"
                                :class="getLoanTypeColor(loan.loan_type)"
                                :style="{ width: `${calculateProgress(loan)}%` }"
                            ></div>
                        </div>
                    </div>

                    <!-- Loan Details -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4 border-t">
                        <div>
                            <div class="text-xs text-gray-500 mb-1">Start Date</div>
                            <div class="text-sm font-medium text-gray-900">{{ formatDate(loan.start_date) }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 mb-1">End Date</div>
                            <div class="text-sm font-medium text-gray-900">{{ formatDate(loan.end_date) }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 mb-1">Term</div>
                            <div class="text-sm font-medium text-gray-900">{{ loan.term_months }} months</div>
                        </div>
                        <div class="flex items-center justify-end">
                            <span class="inline-flex items-center gap-2 text-sm font-medium text-red-500 group-hover:gap-3 transition-all">
                                View Details
                                <ArrowRight :size="16" />
                            </span>
                        </div>
                    </div>
                </div>
            </Link>
        </div>

        <!-- Empty State -->
        <div v-else class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <TrendingUp :size="32" class="text-gray-400" />
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No loans yet</h3>
            <p class="text-gray-600">You don't have any active loans at the moment</p>
        </div>
    </DashboardLayout>
</template>
