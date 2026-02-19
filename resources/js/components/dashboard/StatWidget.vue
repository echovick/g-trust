<script setup lang="ts">
import type { Component } from 'vue';
import { TrendingUp, TrendingDown } from 'lucide-vue-next';

interface Props {
    title: string;
    value: string | number;
    icon?: Component;
    iconColor?: string;
    iconBackground?: string;
    trend?: {
        value: number;
        label: string;
    };
    subtitle?: string;
}

withDefaults(defineProps<Props>(), {
    iconColor: 'text-red-500',
    iconBackground: 'bg-red-50',
});
</script>

<template>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm text-gray-600 mb-1">{{ title }}</p>
                <p class="text-2xl font-bold text-gray-900">{{ value }}</p>
                <p v-if="subtitle" class="text-xs text-gray-500 mt-1">{{ subtitle }}</p>
            </div>

            <div
                v-if="icon"
                :class="['w-12 h-12 rounded-lg flex items-center justify-center', iconBackground]"
            >
                <component :is="icon" :size="24" :class="iconColor" />
            </div>
        </div>

        <div v-if="trend" :class="['flex items-center gap-1 mt-4 text-sm', trend.value >= 0 ? 'text-green-600' : 'text-red-600']">
            <component :is="trend.value >= 0 ? TrendingUp : TrendingDown" :size="16" />
            <span class="font-medium">{{ Math.abs(trend.value) }}%</span>
            <span class="text-gray-500 ml-1">{{ trend.label }}</span>
        </div>
    </div>
</template>
