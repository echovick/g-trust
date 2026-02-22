<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import PageHero from '@/components/shared/PageHero.vue';
import ContentSection from '@/components/shared/ContentSection.vue';
import CallToAction from '@/components/shared/CallToAction.vue';
</script>

<template>
    <MainLayout title="Credit Cards - G-Trust">
        <PageHero
            title="Credit Cards"
            subtitle="Find the perfect credit card for your lifestyle and goals"
            :image="'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=1600'"
            :dark="true"
        />

        <ContentSection title="Our Credit Cards" :centered="true">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12">
                <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl p-8 text-white">
                    <h3 class="text-2xl font-bold mb-4">Cashback Card</h3>
                    <p class="text-4xl font-bold mb-2">2%</p>
                    <p class="mb-6 opacity-90">Cashback on all purchases</p>
                    <ul class="space-y-2 mb-8 text-sm">
                        <li>✓ No annual fee</li>
                        <li>✓ 0% intro APR for 12 months</li>
                        <li>✓ No foreign transaction fees</li>
                    </ul>
                    <button class="w-full bg-white text-blue-600 py-3 rounded-full font-medium hover:bg-gray-100 transition-colors">
                        Apply Now
                    </button>
                </div>

                <div class="bg-gradient-to-br from-red-600 to-red-700 rounded-xl p-8 text-white">
                    <h3 class="text-2xl font-bold mb-4">Travel Rewards</h3>
                    <p class="text-4xl font-bold mb-2">3x</p>
                    <p class="mb-6 opacity-90">Points on travel & dining</p>
                    <ul class="space-y-2 mb-8 text-sm">
                        <li>✓ $95 annual fee</li>
                        <li>✓ Airport lounge access</li>
                        <li>✓ Travel insurance included</li>
                    </ul>
                    <button class="w-full bg-white text-red-600 py-3 rounded-full font-medium hover:bg-gray-100 transition-colors">
                        Apply Now
                    </button>
                </div>

                <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-8 text-white">
                    <h3 class="text-2xl font-bold mb-4">Premium Card</h3>
                    <p class="text-4xl font-bold mb-2">5x</p>
                    <p class="mb-6 opacity-90">Points on select categories</p>
                    <ul class="space-y-2 mb-8 text-sm">
                        <li>✓ $195 annual fee</li>
                        <li>✓ Concierge service</li>
                        <li>✓ Exclusive benefits</li>
                    </ul>
                    <button class="w-full bg-white text-gray-900 py-3 rounded-full font-medium hover:bg-gray-100 transition-colors">
                        Apply Now
                    </button>
                </div>
            </div>
        </ContentSection>

        <CallToAction
            title="Find your perfect card"
            description="Compare our credit cards and choose the one that fits your lifestyle"
            buttonText="Compare Cards"
            background="red"
        />
    </MainLayout>
</template>
