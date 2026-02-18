<script setup lang="ts">
import { ref } from 'vue';

const activeSlide = ref(0);
</script>

<template>
    <section class="relative bg-gray-200 min-h-[600px] flex items-center overflow-hidden">
        <div
            class="absolute inset-0 bg-cover bg-center bg-no-repeat"
            style="background-image: url('https://images.unsplash.com/photo-1542744173-8e7e53415bb0?w=1600&q=80'); background-position: right center;"
        ></div>
        <div class="absolute inset-0 bg-gradient-to-r from-blue-400/50 via-blue-300/20 to-transparent"></div>

        <div class="container mx-auto px-6 lg:px-12 relative z-10">
            <div class="max-w-2xl py-20">
                <h1 class="text-5xl md:text-6xl lg:text-7xl font-serif text-white leading-tight mb-6 drop-shadow-lg" style="font-family: Georgia, 'Times New Roman', serif;">
                    Connecting all your<br />banking needs
                </h1>
                <p class="text-xl md:text-2xl text-white mb-10 font-light drop-shadow-lg">Making It Easy for You.</p>
                <button
                    class="bg-orange-600 hover:bg-orange-700 text-white px-10 py-4 rounded-full font-medium transition-all shadow-lg hover:shadow-xl text-base"
                >
                    Find out more
                </button>
            </div>
        </div>

        <div class="absolute bottom-12 left-1/2 transform -translate-x-1/2 flex gap-3 z-20">
            <button
                v-for="i in 3"
                :key="i"
                :class="[
                    'w-3 h-3 rounded-full transition-all border-2',
                    activeSlide === i - 1 ? 'bg-white border-white' : 'bg-transparent border-white/70 hover:border-white',
                ]"
                @click="activeSlide = i - 1"
                :aria-label="`Go to slide ${i}`"
            ></button>
        </div>
    </section>
</template>
