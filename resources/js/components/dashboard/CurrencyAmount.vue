<script setup lang="ts">
import { computed } from 'vue';
import { useCurrency } from '@/composables/useCurrency';
import type { Currency } from '@/composables/useCurrency';

interface Props {
    amount: number;
    currency?: Currency;
    showCode?: boolean;
    colored?: boolean; // Color positive green, negative red
    convertToPreferred?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    showCode: false,
    colored: false,
    convertToPreferred: true,
});

const { formatCurrency, convertAmount, currentCurrency } = useCurrency();

const displayAmount = computed(() => {
    const sourceCurrency = props.currency || currentCurrency.value;
    let amount = props.amount;

    if (props.convertToPreferred && sourceCurrency !== currentCurrency.value) {
        amount = convertAmount(props.amount, sourceCurrency);
    }

    return formatCurrency(amount, props.convertToPreferred ? currentCurrency.value : sourceCurrency, props.showCode);
});

const colorClass = computed(() => {
    if (!props.colored) return '';
    return props.amount > 0 ? 'text-green-600' : props.amount < 0 ? 'text-red-600' : 'text-gray-900';
});
</script>

<template>
    <span :class="colorClass">
        {{ displayAmount }}
    </span>
</template>
