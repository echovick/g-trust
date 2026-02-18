<script setup lang="ts">
const props = defineProps<{
    title: string;
    subtitle?: string;
    image?: string;
    dark?: boolean;
}>();
</script>

<template>
    <section class="relative bg-gradient-to-br from-blue-300 via-blue-200 to-blue-100 min-h-[400px] flex items-center overflow-hidden">
        <div
            v-if="image"
            class="absolute inset-0 bg-cover bg-center bg-no-repeat"
            :style="`background-image: url('${image}'); background-position: center center;`"
        ></div>
        <div class="absolute inset-0 bg-gradient-to-r from-blue-400/50 via-blue-300/20 to-transparent"></div>

        <div class="container mx-auto px-6 lg:px-12 relative z-10">
            <div class="max-w-3xl py-16">
                <h1 :class="['text-4xl md:text-5xl lg:text-6xl font-serif leading-tight mb-4', dark ? 'text-white drop-shadow-lg' : 'text-gray-900']" style="font-family: Georgia, 'Times New Roman', serif;">
                    {{ title }}
                </h1>
                <p v-if="subtitle" :class="['text-lg md:text-xl font-light', dark ? 'text-white drop-shadow-lg' : 'text-gray-700']">
                    {{ subtitle }}
                </p>
            </div>
        </div>
    </section>
</template>
