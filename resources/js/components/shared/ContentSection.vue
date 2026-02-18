<script setup lang="ts">
const props = defineProps<{
    title?: string;
    background?: 'white' | 'gray';
    centered?: boolean;
}>();
</script>

<template>
    <section :class="['py-16', background === 'gray' ? 'bg-gray-50' : 'bg-white']">
        <div class="container mx-auto px-6">
            <h2 v-if="title" :class="['text-3xl font-bold text-gray-900 mb-12', centered ? 'text-center' : '']">
                {{ title }}
            </h2>
            <slot />
        </div>
    </section>
</template>
