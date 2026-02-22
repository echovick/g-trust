<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import PageHero from '@/components/shared/PageHero.vue';
import ContentSection from '@/components/shared/ContentSection.vue';
import { ref } from 'vue';

const faqs = [
    { question: 'How do I open an account?', answer: 'You can open an account online in minutes by clicking the "Open Account" button and following the simple application process.' },
    { question: 'What are your interest rates?', answer: 'Our interest rates vary by account type. Visit our Savings or Checking pages for current rates.' },
    { question: 'Is my money safe?', answer: 'Yes, all deposits are FDIC insured up to $250,000 per depositor, per account ownership category.' },
    { question: 'How do I reset my password?', answer: 'Click "Forgot Password" on the login page and follow the instructions sent to your registered email.' },
];

const openIndex = ref<number | null>(null);
</script>

<template>
    <MainLayout title="Help & FAQ - G-Trust">
        <PageHero
            title="How Can We Help?"
            subtitle="Find answers to your questions or get in touch with our support team"
            :image="'https://images.unsplash.com/photo-1423666639041-f56000c27a9a?w=1600'"
            :dark="true"
        />

        <ContentSection title="Frequently Asked Questions" :centered="true">
            <div class="max-w-3xl mx-auto space-y-4">
                <div
                    v-for="(faq, index) in faqs"
                    :key="index"
                    class="bg-white border border-gray-200 rounded-lg overflow-hidden"
                >
                    <button
                        @click="openIndex = openIndex === index ? null : index"
                        class="w-full px-6 py-4 text-left flex justify-between items-center hover:bg-gray-50"
                    >
                        <span class="font-semibold text-gray-900">{{ faq.question }}</span>
                        <span class="text-red-500">{{ openIndex === index ? '−' : '+' }}</span>
                    </button>
                    <div v-if="openIndex === index" class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <p class="text-gray-600">{{ faq.answer }}</p>
                    </div>
                </div>
            </div>
        </ContentSection>
    </MainLayout>
</template>
