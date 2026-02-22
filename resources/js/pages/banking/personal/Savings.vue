<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import PageHero from '@/components/shared/PageHero.vue';
import ContentSection from '@/components/shared/ContentSection.vue';
import CardGrid from '@/components/shared/CardGrid.vue';
import FeatureCard from '@/components/shared/FeatureCard.vue';
import CallToAction from '@/components/shared/CallToAction.vue';
import { TrendingUp, Shield, Zap, Target } from 'lucide-vue-next';
</script>

<template>
    <MainLayout title="Savings Accounts - G-Trust">
        <PageHero
            title="Savings Accounts"
            subtitle="Grow your money with competitive rates and flexible access"
            :image="'https://images.unsplash.com/photo-1579621970588-a35d0e7ab9b6?w=1600'"
            :dark="true"
        />

        <ContentSection title="Why Save With Us?" :centered="true">
            <CardGrid :columns="4">
                <FeatureCard
                    :icon="TrendingUp"
                    title="High Interest Rates"
                    description="Earn competitive interest on your savings with rates that help your money grow faster."
                />
                <FeatureCard
                    :icon="Shield"
                    title="FDIC Insured"
                    description="Your deposits are insured up to $250,000 per account holder."
                />
                <FeatureCard
                    :icon="Zap"
                    title="Easy Access"
                    description="Access your money anytime through our mobile app, online banking, or ATMs."
                />
                <FeatureCard
                    :icon="Target"
                    title="Savings Goals"
                    description="Set and track savings goals with our intuitive money management tools."
                />
            </CardGrid>
        </ContentSection>

        <ContentSection background="gray" title="Savings Account Options">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white rounded-lg p-8 border border-gray-200">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">High-Yield Savings</h3>
                    <p class="text-4xl font-bold text-red-500 mb-2">2.50% <span class="text-lg">APY</span></p>
                    <p class="text-gray-600 mb-6">No minimum balance required</p>
                    <ul class="space-y-3 mb-6">
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span class="text-gray-600">Competitive interest rate</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span class="text-gray-600">No monthly fees</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span class="text-gray-600">Easy online transfers</span>
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

                <div class="bg-white rounded-lg p-8 border border-gray-200">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Money Market Account</h3>
                    <p class="text-4xl font-bold text-red-500 mb-2">3.00% <span class="text-lg">APY</span></p>
                    <p class="text-gray-600 mb-6">$2,500 minimum balance</p>
                    <ul class="space-y-3 mb-6">
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span class="text-gray-600">Higher interest rates</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span class="text-gray-600">Check writing privileges</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span class="text-gray-600">Debit card access</span>
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
            title="Start saving smarter today"
            description="Open a savings account and watch your money grow with competitive interest rates"
            buttonText="Open Savings Account"
            background="blue"
        />
    </MainLayout>
</template>
