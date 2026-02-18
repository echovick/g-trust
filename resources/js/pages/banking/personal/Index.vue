<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import PageHero from '@/components/shared/PageHero.vue';
import ContentSection from '@/components/shared/ContentSection.vue';
import CardGrid from '@/components/shared/CardGrid.vue';
import FeatureCard from '@/components/shared/FeatureCard.vue';
import CallToAction from '@/components/shared/CallToAction.vue';
import { CreditCard, PiggyBank, TrendingUp, Shield } from 'lucide-vue-next';
</script>

<template>
    <MainLayout title="Personal Banking - G-Trust Bank">
        <PageHero
            title="Personal Banking"
            subtitle="Banking that grows with you and adapts to your life"
            :image="'https://images.unsplash.com/photo-1554224311-beee415c201f?w=1600'"
            :dark="true"
        />

        <ContentSection title="Personal Banking Services" :centered="true">
            <p class="text-lg text-gray-600 max-w-3xl mx-auto mb-12 text-center">
                From everyday checking to long-term savings, we offer a complete range of personal banking solutions
                designed to help you manage your money with confidence.
            </p>

            <CardGrid :columns="4">
                <FeatureCard
                    :icon="CreditCard"
                    title="Personal Checking"
                    description="Convenient checking accounts with no monthly fees and unlimited transactions."
                    link="#"
                    linkText="Learn more"
                />
                <FeatureCard
                    :icon="PiggyBank"
                    title="Savings Accounts"
                    description="Grow your money with competitive interest rates and flexible access to your funds."
                    link="#"
                    linkText="Learn more"
                />
                <FeatureCard
                    :icon="TrendingUp"
                    title="Investment Options"
                    description="Build wealth for the future with our range of investment and retirement accounts."
                    link="#"
                    linkText="Learn more"
                />
                <FeatureCard
                    :icon="Shield"
                    title="Account Protection"
                    description="Advanced security features and fraud protection to keep your money safe."
                    link="#"
                    linkText="Learn more"
                />
            </CardGrid>
        </ContentSection>

        <ContentSection background="gray">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="bg-gray-200 rounded-lg overflow-hidden">
                    <img
                        src="https://images.unsplash.com/photo-1579621970588-a35d0e7ab9b6?w=800"
                        alt="Mobile Banking"
                        class="w-full h-[400px] object-cover"
                    />
                </div>
                <div>
                    <h3 class="text-3xl font-bold text-gray-900 mb-4">Banking Made Simple</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Manage your accounts anytime, anywhere with our award-winning mobile app and online banking
                        platform. Pay bills, transfer money, deposit checks, and more - all from the palm of your hand.
                    </p>
                    <ul class="space-y-3 mb-6">
                        <li class="flex items-start gap-2">
                            <span class="text-red-500 font-bold">✓</span>
                            <span class="text-gray-600">Mobile check deposit</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500 font-bold">✓</span>
                            <span class="text-gray-600">Bill pay and transfers</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500 font-bold">✓</span>
                            <span class="text-gray-600">Real-time account alerts</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500 font-bold">✓</span>
                            <span class="text-gray-600">Budgeting and spending insights</span>
                        </li>
                    </ul>
                    <button class="bg-red-500 hover:bg-red-600 text-white px-8 py-3 rounded-full font-medium transition-colors">
                        Download App
                    </button>
                </div>
            </div>
        </ContentSection>

        <CallToAction
            title="Ready to get started?"
            description="Open your personal banking account today and experience banking that works for you"
            buttonText="Open an Account"
            background="blue"
        />
    </MainLayout>
</template>
