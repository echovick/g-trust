<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import PageHero from '@/components/shared/PageHero.vue';
import ContentSection from '@/components/shared/ContentSection.vue';
import CardGrid from '@/components/shared/CardGrid.vue';
import { TrendingUp, Users, Award, Globe } from 'lucide-vue-next';

const insights = [
    {
        icon: TrendingUp,
        title: 'Financial Performance',
        stat: '$12.4B',
        description: 'Total assets under management, representing 15% year-over-year growth.',
    },
    {
        icon: Users,
        title: 'Customer Base',
        stat: '2.3M+',
        description: 'Active customers across personal and business banking segments.',
    },
    {
        icon: Award,
        title: 'Industry Recognition',
        stat: '50+',
        description: 'Awards and accolades received for excellence in banking services.',
    },
    {
        icon: Globe,
        title: 'Community Impact',
        stat: '$150M',
        description: 'Invested in community development and financial literacy programs.',
    },
];

const reports = [
    {
        title: '2025 Annual Report',
        description: 'Comprehensive overview of our financial performance and strategic initiatives.',
        year: '2025',
    },
    {
        title: 'Sustainability Report 2025',
        description: 'Our commitment to environmental, social, and governance excellence.',
        year: '2025',
    },
    {
        title: 'Q4 2025 Earnings Report',
        description: 'Detailed quarterly financial results and market analysis.',
        year: '2025',
    },
];
</script>

<template>
    <MainLayout title="G-Trust Bank Insight - G-Trust Bank">
        <PageHero
            title="G-Trust Bank Insight"
            subtitle="Data, insights, and transparency into our operations"
            :image="'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=1600'"
            :dark="true"
        />

        <ContentSection>
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Transparency Through Data</h2>
                <p class="text-lg text-gray-600">
                    At G-Trust Bank, we believe in open communication with our stakeholders. Explore our key metrics,
                    performance indicators, and strategic insights that drive our business forward.
                </p>
            </div>

            <CardGrid :columns="4">
                <div
                    v-for="insight in insights"
                    :key="insight.title"
                    class="bg-white border border-gray-200 rounded-lg p-6 text-center hover:shadow-lg transition-shadow"
                >
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <component :is="insight.icon" :size="24" class="text-red-500" />
                    </div>
                    <div class="text-3xl font-bold text-gray-900 mb-2">{{ insight.stat }}</div>
                    <h3 class="font-semibold text-gray-900 mb-2">{{ insight.title }}</h3>
                    <p class="text-sm text-gray-600">{{ insight.description }}</p>
                </div>
            </CardGrid>
        </ContentSection>

        <ContentSection background="gray">
            <h2 class="text-3xl font-bold text-gray-900 mb-8">Reports & Publications</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div
                    v-for="report in reports"
                    :key="report.title"
                    class="bg-white border border-gray-200 rounded-lg p-8"
                >
                    <div class="text-xs font-medium text-red-500 mb-3">{{ report.year }}</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">{{ report.title }}</h3>
                    <p class="text-gray-600 mb-6">{{ report.description }}</p>
                    <a href="/contact" class="text-red-500 hover:text-red-600 font-medium">
                        Download PDF →
                    </a>
                </div>
            </div>
        </ContentSection>

        <ContentSection>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-8 md:p-12">
                <div class="max-w-3xl">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Investor Relations</h2>
                    <p class="text-gray-600 mb-6">
                        Access comprehensive financial data, earnings reports, and investor presentations.
                        Subscribe to our investor alerts to stay informed about important announcements.
                    </p>
                    <button class="bg-red-500 hover:bg-red-600 text-white py-3 px-8 rounded-full font-medium transition-colors">
                        Visit Investor Relations
                    </button>
                </div>
            </div>
        </ContentSection>
    </MainLayout>
</template>
