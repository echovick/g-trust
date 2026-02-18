<script setup lang="ts">
const partners = [
    'UNI CF NCH',
    'hopp',
    'envato',
    'jQuery',
    'citbank',
];
</script>

<template>
    <section class="py-12 bg-white border-b border-gray-100">
        <div class="container mx-auto px-6">
            <div class="flex items-center justify-between gap-8 opacity-40">
                <div
                    v-for="partner in partners"
                    :key="partner"
                    class="text-gray-400 font-semibold text-lg"
                >
                    {{ partner }}
                </div>
            </div>
        </div>
    </section>
</template>
