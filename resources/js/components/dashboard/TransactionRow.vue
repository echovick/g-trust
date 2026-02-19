<script setup lang="ts">
import type { Component } from 'vue';
import CurrencyAmount from './CurrencyAmount.vue';
import type { Currency } from '@/composables/useCurrency';

interface Transaction {
    id: number;
    description: string;
    amount: number;
    currency: Currency;
    category?: string;
    transaction_date: string;
    status: 'pending' | 'completed' | 'failed' | 'cancelled';
    icon?: Component;
}

interface Props {
    transaction: Transaction;
    showDate?: boolean;
    clickable?: boolean;
}

withDefaults(defineProps<Props>(), {
    showDate: true,
    clickable: true,
});

const emit = defineEmits<{
    click: [transaction: Transaction];
}>();

const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    const today = new Date();
    const yesterday = new Date(today);
    yesterday.setDate(yesterday.getDate() - 1);

    if (date.toDateString() === today.toDateString()) {
        return `Today, ${date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' })}`;
    } else if (date.toDateString() === yesterday.toDateString()) {
        return `Yesterday, ${date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' })}`;
    } else {
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }
};

const statusColor = (status: Transaction['status']) => {
    switch (status) {
        case 'completed':
            return 'bg-green-100 text-green-800';
        case 'pending':
            return 'bg-yellow-100 text-yellow-800';
        case 'failed':
            return 'bg-red-100 text-red-800';
        case 'cancelled':
            return 'bg-gray-100 text-gray-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};
</script>

<template>
    <div
        :class="[
            'flex items-center justify-between py-3 border-b last:border-0 transition-colors',
            clickable ? 'hover:bg-gray-50 -mx-2 px-2 rounded-lg cursor-pointer' : ''
        ]"
        @click="clickable && emit('click', transaction)"
    >
        <div class="flex-1">
            <div class="flex items-center gap-2">
                <p class="font-medium text-gray-900">{{ transaction.description }}</p>
                <span
                    v-if="transaction.status !== 'completed'"
                    :class="['text-xs px-2 py-0.5 rounded-full', statusColor(transaction.status)]"
                >
                    {{ transaction.status }}
                </span>
            </div>
            <div v-if="showDate" class="flex items-center gap-2 mt-1">
                <p class="text-sm text-gray-500">
                    {{ formatDate(transaction.transaction_date) }}
                </p>
                <span v-if="transaction.category" class="text-sm text-gray-400">•</span>
                <p v-if="transaction.category" class="text-sm text-gray-500">
                    {{ transaction.category }}
                </p>
            </div>
        </div>

        <div class="text-right">
            <p :class="[
                'font-semibold text-lg',
                transaction.amount > 0 ? 'text-green-600' : 'text-gray-900'
            ]">
                <span v-if="transaction.amount > 0">+</span>
                <CurrencyAmount :amount="Math.abs(transaction.amount)" :currency="transaction.currency" />
            </p>
        </div>
    </div>
</template>
