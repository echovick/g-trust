<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import PageHero from '@/components/shared/PageHero.vue';
import ContentSection from '@/components/shared/ContentSection.vue';
import CallToAction from '@/components/shared/CallToAction.vue';
</script>

<template>
    <MainLayout title="Personal Checking - G-Trust">
        <PageHero
            title="Personal Checking"
            subtitle="Simple, fee-free checking that fits your lifestyle"
            :image="'https://images.unsplash.com/photo-1563013544-824ae1b704d3?w=1600'"
            :dark="true"
        />

        <ContentSection title="Choose Your Checking Account" :centered="true">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12">
                <div class="bg-white border-2 border-gray-200 rounded-lg p-8 hover:border-red-500 transition-colors">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Basic Checking</h3>
                    <p class="text-3xl font-bold text-red-500 mb-4">$0<span class="text-sm text-gray-600">/mo</span></p>
                    <ul class="space-y-3 mb-6">
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span class="text-sm text-gray-600">No monthly fees</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span class="text-sm text-gray-600">Free debit card</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span class="text-sm text-gray-600">Online & mobile banking</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span class="text-sm text-gray-600">Bill pay</span>
                        </li>
                    </ul>
                    <button class="w-full bg-red-500 hover:bg-red-600 text-white py-3 rounded-full font-medium transition-colors">
                        Open Account
                    </button>
                </div>

                <div class="bg-white border-2 border-red-500 rounded-lg p-8 relative">
                    <div class="absolute top-0 right-0 bg-red-500 text-white px-4 py-1 rounded-bl-lg rounded-tr-lg text-sm font-medium">
                        Popular
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Plus Checking</h3>
                    <p class="text-3xl font-bold text-red-500 mb-4">$5<span class="text-sm text-gray-600">/mo</span></p>
                    <ul class="space-y-3 mb-6">
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span class="text-sm text-gray-600">Everything in Basic</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span class="text-sm text-gray-600">Overdraft protection</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span class="text-sm text-gray-600">ATM fee rebates</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span class="text-sm text-gray-600">Interest on balances</span>
                        </li>
                    </ul>
                    <button class="w-full bg-red-500 hover:bg-red-600 text-white py-3 rounded-full font-medium transition-colors">
                        Open Account
                    </button>
                </div>

                <div class="bg-white border-2 border-gray-200 rounded-lg p-8 hover:border-red-500 transition-colors">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Premier Checking</h3>
                    <p class="text-3xl font-bold text-red-500 mb-4">$15<span class="text-sm text-gray-600">/mo</span></p>
                    <ul class="space-y-3 mb-6">
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span class="text-sm text-gray-600">Everything in Plus</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span class="text-sm text-gray-600">Personal banker</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span class="text-sm text-gray-600">Priority support</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span class="text-sm text-gray-600">Free checks & wire transfers</span>
                        </li>
                    </ul>
                    <button class="w-full bg-red-500 hover:bg-red-600 text-white py-3 rounded-full font-medium transition-colors">
                        Open Account
                    </button>
                </div>
            </div>
        </ContentSection>

        <CallToAction
            title="Open your checking account in minutes"
            description="Get started with no minimum deposit and enjoy banking without the fees"
            buttonText="Get Started"
            background="blue"
        />
    </MainLayout>
</template>
