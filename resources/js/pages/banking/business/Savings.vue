<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import PageHero from '@/components/shared/PageHero.vue';
import ContentSection from '@/components/shared/ContentSection.vue';
import CallToAction from '@/components/shared/CallToAction.vue';
</script>

<template>
    <MainLayout title="Business Savings - G-Trust">
        <PageHero
            title="Business Savings"
            subtitle="Earn more on your business funds with competitive rates"
            :image="'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=1600'"
            :dark="true"
        />

        <ContentSection title="Business Savings Options">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white rounded-lg p-8 border-2 border-gray-200">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Business Savings Account</h3>
                    <p class="text-4xl font-bold text-red-500 mb-2">2.75% <span class="text-lg">APY</span></p>
                    <p class="text-gray-600 mb-6">$500 minimum balance</p>
                    <ul class="space-y-3 mb-6">
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span class="text-gray-600">Competitive interest rates</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span class="text-gray-600">FDIC insured</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span class="text-gray-600">Easy online transfers</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span class="text-gray-600">No monthly fees</span>
                        </li>
                    </ul>
                    <button class="bg-red-500 hover:bg-red-600 text-white px-8 py-3 rounded-full font-medium transition-colors w-full">
                        Open Account
                    </button>
                </div>

                <div class="bg-white rounded-lg p-8 border-2 border-gray-200">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Business Money Market</h3>
                    <p class="text-4xl font-bold text-red-500 mb-2">3.25% <span class="text-lg">APY</span></p>
                    <p class="text-gray-600 mb-6">$10,000 minimum balance</p>
                    <ul class="space-y-3 mb-6">
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span class="text-gray-600">Higher interest rates</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span class="text-gray-600">Limited check writing</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span class="text-gray-600">Tiered rate structure</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span class="text-gray-600">FDIC insured</span>
                        </li>
                    </ul>
                    <button class="bg-red-500 hover:bg-red-600 text-white px-8 py-3 rounded-full font-medium transition-colors w-full">
                        Open Account
                    </button>
                </div>
            </div>
        </ContentSection>

        <CallToAction
            title="Grow your business reserves"
            description="Put your business savings to work with competitive interest rates"
            buttonText="Open Savings Account"
            buttonLink="/register"
            background="blue"
        />
    </MainLayout>
</template>
