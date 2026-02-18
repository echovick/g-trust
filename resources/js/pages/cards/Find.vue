<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import PageHero from '@/components/shared/PageHero.vue';
import ContentSection from '@/components/shared/ContentSection.vue';
import { ref } from 'vue';

const selectedCategory = ref('all');

const categories = [
    { id: 'all', name: 'All Cards' },
    { id: 'cashback', name: 'Cashback' },
    { id: 'travel', name: 'Travel Rewards' },
    { id: 'business', name: 'Business' },
    { id: 'student', name: 'Student' },
];
</script>

<template>
    <MainLayout title="Find a Credit Card - G-Trust Bank">
        <PageHero
            title="Find Your Perfect Credit Card"
            subtitle="Compare cards and find the one that matches your spending style"
            :image="'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=1600'"
            :dark="true"
        />

        <ContentSection title="Filter by Category" :centered="true">
            <div class="flex justify-center gap-4 mb-12 flex-wrap">
                <button
                    v-for="category in categories"
                    :key="category.id"
                    @click="selectedCategory = category.id"
                    :class="[
                        'px-6 py-2 rounded-full font-medium transition-colors',
                        selectedCategory === category.id
                            ? 'bg-red-500 text-white'
                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200',
                    ]"
                >
                    {{ category.name }}
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Cashback Card -->
                <div class="bg-white border-2 border-gray-200 rounded-xl p-8 hover:border-red-500 transition-all hover:shadow-lg">
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg p-6 text-white mb-6 h-40 flex flex-col justify-between">
                        <div>
                            <p class="text-sm opacity-80">G-Trust Bank</p>
                            <p class="text-xl font-bold">Cashback Rewards</p>
                        </div>
                        <div class="text-2xl font-bold">2% Back</div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Everyday Cashback Card</h3>
                    <p class="text-gray-600 text-sm mb-4">Earn 2% cash back on all purchases, no categories to track</p>
                    <ul class="space-y-2 text-sm text-gray-600 mb-6">
                        <li>✓ No annual fee</li>
                        <li>✓ 0% intro APR for 15 months</li>
                        <li>✓ No foreign transaction fees</li>
                    </ul>
                    <a href="#" class="block w-full bg-red-500 hover:bg-red-600 text-white py-3 rounded-full font-medium transition-colors text-center">
                        Apply Now
                    </a>
                </div>

                <!-- Travel Card -->
                <div class="bg-white border-2 border-gray-200 rounded-xl p-8 hover:border-red-500 transition-all hover:shadow-lg">
                    <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-lg p-6 text-white mb-6 h-40 flex flex-col justify-between">
                        <div>
                            <p class="text-sm opacity-80">G-Trust Bank</p>
                            <p class="text-xl font-bold">Travel Elite</p>
                        </div>
                        <div class="text-2xl font-bold">3X Points</div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Travel Rewards Card</h3>
                    <p class="text-gray-600 text-sm mb-4">Earn 3X points on travel and dining, 1X on everything else</p>
                    <ul class="space-y-2 text-sm text-gray-600 mb-6">
                        <li>✓ $95 annual fee</li>
                        <li>✓ Airport lounge access</li>
                        <li>✓ Travel insurance included</li>
                    </ul>
                    <a href="#" class="block w-full bg-red-500 hover:bg-red-600 text-white py-3 rounded-full font-medium transition-colors text-center">
                        Apply Now
                    </a>
                </div>

                <!-- Premium Card -->
                <div class="bg-white border-2 border-red-500 rounded-xl p-8 hover:shadow-lg transition-all relative">
                    <div class="absolute top-0 right-0 bg-red-500 text-white px-4 py-1 rounded-bl-lg rounded-tr-lg text-sm font-medium">
                        Best Value
                    </div>
                    <div class="bg-gradient-to-r from-gray-800 to-gray-900 rounded-lg p-6 text-white mb-6 h-40 flex flex-col justify-between">
                        <div>
                            <p class="text-sm opacity-80">G-Trust Bank</p>
                            <p class="text-xl font-bold">Platinum Elite</p>
                        </div>
                        <div class="text-2xl font-bold">5X Points</div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Premium Rewards Card</h3>
                    <p class="text-gray-600 text-sm mb-4">5X points on select categories, premium benefits and concierge</p>
                    <ul class="space-y-2 text-sm text-gray-600 mb-6">
                        <li>✓ $195 annual fee</li>
                        <li>✓ Concierge service 24/7</li>
                        <li>✓ Exclusive partner benefits</li>
                    </ul>
                    <a href="#" class="block w-full bg-red-500 hover:bg-red-600 text-white py-3 rounded-full font-medium transition-colors text-center">
                        Apply Now
                    </a>
                </div>
            </div>
        </ContentSection>
    </MainLayout>
</template>
