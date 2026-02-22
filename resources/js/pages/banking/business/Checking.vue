<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import PageHero from '@/components/shared/PageHero.vue';
import ContentSection from '@/components/shared/ContentSection.vue';
import CallToAction from '@/components/shared/CallToAction.vue';
</script>

<template>
    <MainLayout title="Business Checking - G-Trust">
        <PageHero
            title="Business Checking"
            subtitle="Powerful checking accounts designed for businesses of all sizes"
            :image="'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=1600'"
            :dark="true"
        />

        <ContentSection title="Choose Your Business Checking Account" :centered="true">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12">
                <div class="bg-white border-2 border-gray-200 rounded-lg p-8 hover:border-red-500 transition-colors">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Basic Business</h3>
                    <p class="text-3xl font-bold text-red-500 mb-4">$10<span class="text-sm text-gray-600">/mo</span></p>
                    <ul class="space-y-3 mb-6">
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span class="text-sm text-gray-600">100 free transactions/month</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span class="text-sm text-gray-600">Online & mobile banking</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span class="text-sm text-gray-600">Business debit card</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span class="text-sm text-gray-600">Bill pay included</span>
                        </li>
                    </ul>
                    <button class="w-full bg-red-500 hover:bg-red-600 text-white py-3 rounded-full font-medium transition-colors">
                        Open Account
                    </button>
                </div>

                <div class="bg-white border-2 border-red-500 rounded-lg p-8 relative">
                    <div class="absolute top-0 right-0 bg-red-500 text-white px-4 py-1 rounded-bl-lg rounded-tr-lg text-sm font-medium">
                        Recommended
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Growth Business</h3>
                    <p class="text-3xl font-bold text-red-500 mb-4">$25<span class="text-sm text-gray-600">/mo</span></p>
                    <ul class="space-y-3 mb-6">
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span class="text-sm text-gray-600">500 free transactions/month</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span class="text-sm text-gray-600">Interest on balances</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span class="text-sm text-gray-600">Cash management tools</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span class="text-sm text-gray-600">Multi-user access</span>
                        </li>
                    </ul>
                    <button class="w-full bg-red-500 hover:bg-red-600 text-white py-3 rounded-full font-medium transition-colors">
                        Open Account
                    </button>
                </div>

                <div class="bg-white border-2 border-gray-200 rounded-lg p-8 hover:border-red-500 transition-colors">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Enterprise</h3>
                    <p class="text-3xl font-bold text-red-500 mb-4">Custom</p>
                    <ul class="space-y-3 mb-6">
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span class="text-sm text-gray-600">Unlimited transactions</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span class="text-sm text-gray-600">Dedicated account manager</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span class="text-sm text-gray-600">Custom credit solutions</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span class="text-sm text-gray-600">Premium support</span>
                        </li>
                    </ul>
                    <button class="w-full bg-red-500 hover:bg-red-600 text-white py-3 rounded-full font-medium transition-colors">
                        Contact Us
                    </button>
                </div>
            </div>
        </ContentSection>

        <CallToAction
            title="Open your business checking account today"
            description="Get started with the right account for your business needs"
            buttonText="Get Started"
            background="blue"
        />
    </MainLayout>
</template>
