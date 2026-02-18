<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import TopBar from '@/components/landing/TopBar.vue';
import Header from '@/components/landing/Header.vue';
import Footer from '@/components/landing/Footer.vue';
import FooterBottom from '@/components/landing/FooterBottom.vue';
import CookieBanner from '@/components/landing/CookieBanner.vue';
import { ref } from 'vue';

const props = defineProps<{
    title?: string;
    showCookieBanner?: boolean;
}>();

const cookieBannerVisible = ref(props.showCookieBanner ?? false);
</script>

<template>
    <Head :title="title || 'G-Trust Bank'" />

    <div class="min-h-screen bg-white flex flex-col">
        <TopBar />
        <Header />

        <main class="flex-grow">
            <slot />
        </main>

        <Footer />
        <FooterBottom />
        <CookieBanner v-if="cookieBannerVisible" @accept="cookieBannerVisible = false" />
    </div>
</template>
