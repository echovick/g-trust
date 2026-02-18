<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import PageHero from '@/components/shared/PageHero.vue';
import ContentSection from '@/components/shared/ContentSection.vue';
import CallToAction from '@/components/shared/CallToAction.vue';
import { Download, TrendingUp, Calendar, Bell } from 'lucide-vue-next';

const financialHighlights = [
    { label: 'Total Assets', value: '$12.4B', change: '+15%' },
    { label: 'Net Income', value: '$487M', change: '+22%' },
    { label: 'ROE', value: '14.2%', change: '+1.8%' },
    { label: 'Dividend Yield', value: '3.45%', change: '+0.25%' },
];

const recentFilings = [
    {
        title: '10-K Annual Report',
        date: 'February 15, 2026',
        type: 'Annual Filing',
    },
    {
        title: 'Q4 2025 Earnings Release',
        date: 'January 28, 2026',
        type: 'Earnings',
    },
    {
        title: '8-K Current Report',
        date: 'January 15, 2026',
        type: 'Current Report',
    },
    {
        title: 'Proxy Statement',
        date: 'December 20, 2025',
        type: 'Proxy',
    },
];

const upcomingEvents = [
    {
        title: 'Q1 2026 Earnings Call',
        date: 'April 28, 2026',
        time: '8:00 AM ET',
    },
    {
        title: 'Annual Shareholder Meeting',
        date: 'May 15, 2026',
        time: '10:00 AM ET',
    },
];
</script>

<template>
    <MainLayout title="Investor Relations - G-Trust Bank">
        <PageHero
            title="Investor Relations"
            subtitle="Financial information and resources for our shareholders"
            :image="'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=1600'"
            :dark="true"
        />

        <ContentSection>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                <div
                    v-for="highlight in financialHighlights"
                    :key="highlight.label"
                    class="bg-white border border-gray-200 rounded-lg p-6"
                >
                    <div class="text-sm text-gray-500 mb-2">{{ highlight.label }}</div>
                    <div class="text-2xl font-bold text-gray-900 mb-1">{{ highlight.value }}</div>
                    <div class="text-sm text-green-600 font-medium">{{ highlight.change }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Recent SEC Filings</h2>
                    <div class="space-y-4">
                        <div
                            v-for="filing in recentFilings"
                            :key="filing.title"
                            class="flex items-start justify-between border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors"
                        >
                            <div class="flex-1">
                                <div class="font-semibold text-gray-900 mb-1">{{ filing.title }}</div>
                                <div class="text-sm text-gray-500">
                                    {{ filing.type }} • {{ filing.date }}
                                </div>
                            </div>
                            <a href="/contact" class="text-red-500 hover:text-red-600 ml-4">
                                <Download :size="20" />
                            </a>
                        </div>
                    </div>
                    <a href="/contact" class="inline-block mt-6 text-red-500 hover:text-red-600 font-medium">
                        View all SEC filings →
                    </a>
                </div>

                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Upcoming Events</h2>
                    <div class="space-y-4 mb-8">
                        <div
                            v-for="event in upcomingEvents"
                            :key="event.title"
                            class="border border-gray-200 rounded-lg p-6"
                        >
                            <div class="flex items-start gap-3 mb-3">
                                <Calendar :size="20" class="text-red-500 mt-1" />
                                <div>
                                    <h3 class="font-semibold text-gray-900 mb-1">{{ event.title }}</h3>
                                    <p class="text-sm text-gray-600">{{ event.date }} at {{ event.time }}</p>
                                </div>
                            </div>
                            <button class="text-red-500 hover:text-red-600 text-sm font-medium">
                                Add to calendar
                            </button>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                        <div class="flex items-start gap-3">
                            <Bell :size="20" class="text-blue-600 mt-1" />
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-2">Email Alerts</h3>
                                <p class="text-sm text-gray-600 mb-4">
                                    Subscribe to receive investor news and updates
                                </p>
                                <button class="bg-red-500 hover:bg-red-600 text-white py-2 px-6 rounded-full text-sm font-medium transition-colors">
                                    Subscribe
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </ContentSection>

        <ContentSection background="gray">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <TrendingUp :size="32" class="text-red-500 mb-4" />
                    <h3 class="font-bold text-gray-900 mb-2">Stock Information</h3>
                    <p class="text-sm text-gray-600 mb-4">Real-time quotes, historical data, and analyst coverage</p>
                    <a href="/about/investors" class="text-red-500 hover:text-red-600 font-medium text-sm">
                        View stock info →
                    </a>
                </div>
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <Download :size="32" class="text-red-500 mb-4" />
                    <h3 class="font-bold text-gray-900 mb-2">Financial Reports</h3>
                    <p class="text-sm text-gray-600 mb-4">Annual reports, quarterly earnings, and presentations</p>
                    <a href="/about/insight" class="text-red-500 hover:text-red-600 font-medium text-sm">
                        Download reports →
                    </a>
                </div>
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <Calendar :size="32" class="text-red-500 mb-4" />
                    <h3 class="font-bold text-gray-900 mb-2">Corporate Governance</h3>
                    <p class="text-sm text-gray-600 mb-4">Board information, policies, and governance documents</p>
                    <a href="/about" class="text-red-500 hover:text-red-600 font-medium text-sm">
                        Learn more →
                    </a>
                </div>
            </div>
        </ContentSection>

        <CallToAction
            title="Questions about our investor relations?"
            description="Our investor relations team is here to help"
            buttonText="Contact IR Team"
            background="blue"
        />
    </MainLayout>
</template>
