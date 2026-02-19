<script setup lang="ts">
import { ref } from 'vue';
import type { Component } from 'vue';
import { Eye, EyeOff } from 'lucide-vue-next';
import { useCurrency } from '@/composables/useCurrency';

interface Account {
    id: number;
    account_name: string;
    account_number: string;
    account_type: 'checking' | 'savings' | 'business';
    balance: number;
    currency: 'USD' | 'EUR' | 'GBP';
    icon?: Component;
}

interface Props {
    account: Account;
    showBalanceToggle?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    showBalanceToggle: true,
});

const { formatCurrency, convertAmount, currentCurrency } = useCurrency();
const showBalance = ref(true);

const displayBalance = () => {
    if (!showBalance.value) return '••••••';

    const converted = convertAmount(props.account.balance, props.account.currency);
    return formatCurrency(converted);
};
</script>

<template>
    <div
        class="bg-gradient-to-br from-red-600 to-red-700 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-shadow cursor-pointer"
    >
        <div class="flex items-start justify-between mb-4">
            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                <component v-if="account.icon" :is="account.icon" :size="24" />
                <span v-else class="text-xl font-bold">
                    {{ account.account_type.charAt(0).toUpperCase() }}
                </span>
            </div>
            <button
                v-if="showBalanceToggle"
                @click.stop="showBalance = !showBalance"
                class="text-white/80 hover:text-white transition-colors"
            >
                <component :is="showBalance ? Eye : EyeOff" :size="20" />
            </button>
        </div>

        <p class="text-white/80 text-sm mb-1">{{ account.account_name }}</p>
        <p class="text-white/60 text-xs mb-3">{{ account.account_number }}</p>

        <div class="text-3xl font-bold">
            {{ displayBalance() }}
        </div>

        <div v-if="account.currency !== currentCurrency" class="mt-2 text-xs text-white/60">
            Original: {{ formatCurrency(account.balance, account.currency) }}
        </div>
    </div>
</template>
