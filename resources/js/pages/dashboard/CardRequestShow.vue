<script setup lang="ts">
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { ArrowLeft, CheckCircle, XCircle, Clock, CreditCard, Wallet, Calendar } from 'lucide-vue-next';

interface User {
    id: number;
    name: string;
}

interface Account {
    id: number;
    account_number: string;
    account_name: string;
}

interface Card {
    id: number;
    card_number: string;
}

interface CardRequest {
    id: number;
    account: Account;
    card_type: string;
    card_brand: string;
    purpose?: string;
    status: 'pending' | 'approved' | 'rejected';
    created_at: string;
    approved_at?: string;
    rejected_at?: string;
    rejection_reason?: string;
    createdCard?: Card;
    approvedBy?: User;
}

interface Props {
    request: CardRequest;
}

const props = defineProps<Props>();

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
        case 'approved': return 'text-green-700 bg-green-50 border-green-200';
        case 'pending': return 'text-yellow-700 bg-yellow-50 border-yellow-200';
        case 'rejected': return 'text-red-700 bg-red-50 border-red-200';
        default: return 'text-gray-700 bg-gray-50 border-gray-200';
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
    <DashboardLayout title="Card Request Details">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <a
                    href="/dashboard/card-requests"
                    class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
                >
                    <ArrowLeft :size="20" class="text-gray-600" />
                </a>
                <div class="flex-1">
                    <p class="text-gray-600">View the details of your card request</p>
                </div>
                <div>
                    <span :class="[
                        'inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium border',
                        getStatusColor(request.status)
                    ]">
                        <component :is="getStatusIcon(request.status)" :size="18" />
                        {{ request.status.charAt(0).toUpperCase() + request.status.slice(1) }}
                    </span>
                </div>
            </div>

            <!-- Request Details -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Request Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-red-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <CreditCard :size="20" class="text-red-500" />
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Card Type</p>
                                <p class="font-semibold text-gray-900 capitalize">{{ request.card_type }} Card</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600 mb-1">Card Brand</p>
                        <p class="font-semibold text-gray-900 uppercase">{{ request.card_brand }}</p>
                    </div>

                    <div>
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <Wallet :size="20" class="text-blue-500" />
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Linked Account</p>
                                <p class="font-semibold text-gray-900 font-mono">{{ request.account.account_number }}</p>
                                <p class="text-xs text-gray-600">{{ request.account.account_name }}</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <Calendar :size="20" class="text-purple-500" />
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Requested On</p>
                                <p class="font-semibold text-gray-900">{{ formatDate(request.created_at) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="request.purpose" class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-600 mb-2">Purpose / Reason</p>
                    <p class="text-gray-900 leading-relaxed">{{ request.purpose }}</p>
                </div>
            </div>

            <!-- Status-specific Information -->

            <!-- Approved -->
            <div v-if="request.status === 'approved'" class="bg-green-50 border border-green-200 rounded-xl p-6">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0">
                        <CheckCircle :size="24" class="text-white" />
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-green-900 mb-2">✓ Request Approved!</h3>
                        <p class="text-sm text-green-800 mb-4">
                            Your card request has been approved and your new card has been created.
                        </p>

                        <div v-if="request.createdCard" class="bg-white border border-green-200 rounded-lg p-4 space-y-3">
                            <div>
                                <p class="text-xs text-green-700 mb-1">Card Number</p>
                                <p class="text-lg font-mono font-bold text-green-900">{{ request.createdCard.card_number }}</p>
                            </div>
                            <p class="text-sm text-green-800">
                                Your card will be shipped to your registered address within 7-10 business days.
                            </p>
                            <a
                                href="/dashboard/cards"
                                class="inline-flex items-center gap-2 mt-3 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium"
                            >
                                View All Cards →
                            </a>
                        </div>

                        <div class="mt-4 text-xs text-green-700">
                            Approved on {{ formatDate(request.approved_at!) }}
                            <span v-if="request.approvedBy"> by {{ request.approvedBy.name }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending -->
            <div v-if="request.status === 'pending'" class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center flex-shrink-0">
                        <Clock :size="24" class="text-white" />
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-yellow-900 mb-2">⏱ Pending Review</h3>
                        <p class="text-sm text-yellow-800 mb-4">
                            Your card request is currently being reviewed by our team.
                        </p>
                        <div class="bg-white border border-yellow-200 rounded-lg p-4">
                            <p class="text-sm text-yellow-900 font-medium mb-2">What happens next?</p>
                            <ul class="text-sm text-yellow-800 space-y-2">
                                <li class="flex items-start gap-2">
                                    <span class="text-yellow-500 mt-0.5">•</span>
                                    <span>Our team will review your request within 1-2 business days</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-yellow-500 mt-0.5">•</span>
                                    <span>You'll receive an email notification once it's approved</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-yellow-500 mt-0.5">•</span>
                                    <span>Your card will be created and shipped to your address</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rejected -->
            <div v-if="request.status === 'rejected'" class="bg-red-50 border border-red-200 rounded-xl p-6">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center flex-shrink-0">
                        <XCircle :size="24" class="text-white" />
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-red-900 mb-2">✗ Request Rejected</h3>
                        <p class="text-sm text-red-800 mb-4">
                            Unfortunately, your card request was not approved.
                        </p>

                        <div v-if="request.rejection_reason" class="bg-white border border-red-200 rounded-lg p-4 mb-4">
                            <p class="text-sm font-medium text-red-900 mb-2">Reason for rejection:</p>
                            <p class="text-sm text-red-800">{{ request.rejection_reason }}</p>
                        </div>

                        <div class="text-xs text-red-700 mb-4">
                            Rejected on {{ formatDate(request.rejected_at!) }}
                        </div>

                        <a
                            href="/dashboard/card-requests/create"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm font-medium"
                        >
                            Submit New Request
                        </a>
                    </div>
                </div>
            </div>

            <!-- Back Button -->
            <div class="flex justify-start">
                <a
                    href="/dashboard/card-requests"
                    class="inline-flex items-center gap-2 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium"
                >
                    <ArrowLeft :size="20" />
                    Back to All Requests
                </a>
            </div>
        </div>
    </DashboardLayout>
</template>
