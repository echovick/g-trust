<script setup lang="ts">
import type { Component } from 'vue';

const props = defineProps<{
    title: string;
    description: string;
    icon?: Component;
    link?: string;
    linkText?: string;
}>();
</script>

<template>
    <div class="bg-white rounded-lg p-6 hover:shadow-lg transition-shadow border border-gray-100">
        <div v-if="icon" class="w-14 h-14 bg-red-50 rounded-lg flex items-center justify-center mb-4">
            <component :is="icon" :size="28" class="text-red-500" />
        </div>
        <h3 class="text-lg font-semibold text-gray-900 mb-3">{{ title }}</h3>
        <p class="text-sm text-gray-600 mb-4 leading-relaxed">{{ description }}</p>
        <a v-if="link" :href="link" class="text-sm text-red-500 hover:text-red-600 font-medium">
            {{ linkText || 'Learn more' }} →
        </a>
    </div>
</template>
