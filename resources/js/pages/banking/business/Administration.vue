<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import PageHero from '@/components/shared/PageHero.vue';
import ContentSection from '@/components/shared/ContentSection.vue';
import CardGrid from '@/components/shared/CardGrid.vue';
import FeatureCard from '@/components/shared/FeatureCard.vue';
import CallToAction from '@/components/shared/CallToAction.vue';
import { FileText, DollarSign, Users, TrendingUp } from 'lucide-vue-next';
</script>

<template>
    <MainLayout title="Small Business Administration - G-Trust Bank">
        <PageHero
            title="Small Business Administration (SBA) Loans"
            subtitle="Access government-backed financing for your small business"
            :image="'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=1600'"
            :dark="true"
        />

        <ContentSection title="SBA Loan Programs" :centered="true">
            <p class="text-lg text-gray-600 max-w-3xl mx-auto mb-12 text-center">
                G-Trust Bank is a preferred SBA lender, helping small businesses access affordable financing with
                favorable terms and lower down payments.
            </p>

            <CardGrid :columns="4">
                <FeatureCard
                    :icon="DollarSign"
                    title="SBA 7(a) Loans"
                    description="Up to $5 million for working capital, equipment, and real estate."
                />
                <FeatureCard :icon="FileText" title="SBA 504 Loans" description="Long-term, fixed-rate financing for major assets and expansions." />
                <FeatureCard
                    :icon="Users"
                    title="SBA Microloans"
                    description="Smaller loans up to $50,000 for startups and growing businesses."
                />
                <FeatureCard
                    :icon="TrendingUp"
                    title="Express Loans"
                    description="Expedited processing for loans up to $500,000."
                />
            </CardGrid>
        </ContentSection>

        <ContentSection background="gray" title="Why Choose SBA Loans?">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Benefits of SBA Financing</h3>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <span class="text-red-500 font-bold">✓</span>
                            <span class="text-gray-600">Lower down payments (as low as 10%)</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="text-red-500 font-bold">✓</span>
                            <span class="text-gray-600">Longer repayment terms up to 25 years</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="text-red-500 font-bold">✓</span>
                            <span class="text-gray-600">Competitive interest rates</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="text-red-500 font-bold">✓</span>
                            <span class="text-gray-600">Government guarantee reduces lender risk</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="text-red-500 font-bold">✓</span>
                            <span class="text-gray-600">Flexible use of funds</span>
                        </li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Qualifying for an SBA Loan</h3>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <span class="text-red-500 font-bold">•</span>
                            <span class="text-gray-600">Operate for profit in the United States</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="text-red-500 font-bold">•</span>
                            <span class="text-gray-600">Meet SBA size standards for your industry</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="text-red-500 font-bold">•</span>
                            <span class="text-gray-600">Demonstrate ability to repay the loan</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="text-red-500 font-bold">•</span>
                            <span class="text-gray-600">Have a sound business purpose</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="text-red-500 font-bold">•</span>
                            <span class="text-gray-600">Owner must have invested equity</span>
                        </li>
                    </ul>
                </div>
            </div>
        </ContentSection>

        <CallToAction
            title="Ready to grow with SBA financing?"
            description="Speak with our SBA lending specialists to find the right loan for your business"
            buttonText="Apply Now"
            background="blue"
        />
    </MainLayout>
</template>
