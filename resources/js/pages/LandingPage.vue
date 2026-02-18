<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import TopBar from '@/components/landing/TopBar.vue';
import Header from '@/components/landing/Header.vue';
import Hero from '@/components/landing/Hero.vue';
import Partners from '@/components/landing/Partners.vue';
import Services from '@/components/landing/Services.vue';
import BusinessBanking from '@/components/landing/BusinessBanking.vue';
import MobileApp from '@/components/landing/MobileApp.vue';
import LearningCenter from '@/components/landing/LearningCenter.vue';
import News from '@/components/landing/News.vue';
import Footer from '@/components/landing/Footer.vue';
import FooterBottom from '@/components/landing/FooterBottom.vue';
import CookieBanner from '@/components/landing/CookieBanner.vue';

const showCookieBanner = ref(true);
</script>

<template>
    <Head title="G-Trust Bank - Connecting all your banking needs" />

    <div class="min-h-screen bg-white">
        <TopBar />
        <Header />
        <Hero />
        <Partners />
        <Services />
        <BusinessBanking />
        <MobileApp />
        <LearningCenter />
        <News />
        <Footer />
        <FooterBottom />
        <CookieBanner v-if="showCookieBanner" @accept="showCookieBanner = false" />
    </div>
</template>
