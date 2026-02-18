<script setup lang="ts">
const props = defineProps<{
    title: string;
    description: string;
    buttonText: string;
    buttonLink?: string;
    background?: 'blue' | 'red' | 'gray';
}>();

const bgClasses = {
    blue: 'bg-gradient-to-br from-blue-500 to-blue-600',
    red: 'bg-gradient-to-br from-red-500 to-red-600',
    gray: 'bg-gray-900',
};
</script>

<template>
    <section :class="['py-20 text-white', bgClasses[background || 'blue']]">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">{{ title }}</h2>
            <p class="text-lg md:text-xl mb-8 opacity-90 max-w-2xl mx-auto">{{ description }}</p>
            <a
                :href="buttonLink || '#'"
                class="inline-block bg-white text-gray-900 hover:bg-gray-100 px-10 py-4 rounded-full font-medium transition-colors shadow-lg"
            >
                {{ buttonText }}
            </a>
        </div>
    </section>
</template>
