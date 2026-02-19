<script setup lang="ts">
import { ref } from 'vue';
import { useCurrency, type Currency } from '@/composables/useCurrency';
import { ChevronDown, Check } from 'lucide-vue-next';

const { currentCurrency, currencies, setCurrency } = useCurrency();
const showDropdown = ref(false);

const selectCurrency = (currency: Currency) => {
    setCurrency(currency);
    showDropdown.value = false;
};
</script>

<template>
    <div class="relative">
        <button
            @click="showDropdown = !showDropdown"
            class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors"
        >
            <span class="text-sm font-medium text-gray-900">
                {{ currencies[currentCurrency].symbol }} {{ currentCurrency }}
            </span>
            <ChevronDown :size="16" class="text-gray-500" />
        </button>

        <div
            v-if="showDropdown"
            class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50"
        >
            <button
                v-for="(config, code) in currencies"
                :key="code"
                @click="selectCurrency(code as Currency)"
                :class="[
                    'w-full flex items-center justify-between px-4 py-2 text-sm hover:bg-gray-50 transition-colors',
                    currentCurrency === code ? 'bg-red-50 text-red-600' : 'text-gray-700'
                ]"
            >
                <span class="flex items-center gap-2">
                    <span class="text-lg">{{ config.symbol }}</span>
                    <span>{{ config.name }}</span>
                </span>
                <Check v-if="currentCurrency === code" :size="16" class="text-red-500" />
            </button>
        </div>
    </div>

    <!-- Close dropdown when clicking outside -->
    <div
        v-if="showDropdown"
        @click="showDropdown = false"
        class="fixed inset-0 z-40"
    ></div>
</template>
