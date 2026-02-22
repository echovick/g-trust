<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import PageHero from '@/components/shared/PageHero.vue';
import ContentSection from '@/components/shared/ContentSection.vue';
import { Calendar, Download, Image } from 'lucide-vue-next';

const mediaReleases = [
    {
        date: 'February 15, 2026',
        title: 'G-Trust Announces Q4 2025 Financial Results',
        excerpt: 'Record-breaking quarter demonstrates strong growth across all business segments.',
        category: 'Press Release',
    },
    {
        date: 'January 28, 2026',
        title: 'New Mobile Finance Features Launch',
        excerpt: 'Enhanced security features and AI-powered financial insights now available to all customers.',
        category: 'Product News',
    },
    {
        date: 'January 10, 2026',
        title: 'G-Trust Expands Community Investment Program',
        excerpt: '$50 million commitment to support local businesses and affordable housing initiatives.',
        category: 'Community',
    },
    {
        date: 'December 18, 2025',
        title: 'Award Recognition for Digital Finance Excellence',
        excerpt: 'G-Trust receives industry recognition for innovative online finance platform.',
        category: 'Awards',
    },
];
</script>

<template>
    <MainLayout title="Media Centre - G-Trust">
        <PageHero
            title="Media Centre"
            subtitle="Latest news, press releases, and media resources"
            :image="'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=1600'"
            :dark="true"
        />

        <ContentSection>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Recent Press Releases</h2>
                    <div class="space-y-6">
                        <article
                            v-for="release in mediaReleases"
                            :key="release.title"
                            class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow"
                        >
                            <div class="flex items-center gap-2 text-sm text-gray-500 mb-3">
                                <Calendar :size="16" />
                                <span>{{ release.date }}</span>
                                <span class="mx-2">•</span>
                                <span class="text-red-500 font-medium">{{ release.category }}</span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ release.title }}</h3>
                            <p class="text-gray-600 mb-4">{{ release.excerpt }}</p>
                            <a href="/news/media" class="text-red-500 hover:text-red-600 font-medium inline-flex items-center gap-2">
                                Read full release
                                <Download :size="16" />
                            </a>
                        </article>
                    </div>
                </div>

                <div>
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-6">
                        <h3 class="font-bold text-gray-900 mb-4">Media Resources</h3>
                        <ul class="space-y-3">
                            <li>
                                <a href="/contact" class="text-red-500 hover:text-red-600 font-medium inline-flex items-center gap-2">
                                    <Download :size="16" />
                                    Brand Guidelines
                                </a>
                            </li>
                            <li>
                                <a href="/contact" class="text-red-500 hover:text-red-600 font-medium inline-flex items-center gap-2">
                                    <Image :size="16" />
                                    Logo Assets
                                </a>
                            </li>
                            <li>
                                <a href="/contact" class="text-red-500 hover:text-red-600 font-medium inline-flex items-center gap-2">
                                    <Download :size="16" />
                                    Executive Photos
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                        <h3 class="font-bold text-gray-900 mb-3">Media Inquiries</h3>
                        <p class="text-sm text-gray-600 mb-4">For press and media inquiries, please contact our communications team.</p>
                        <div class="text-sm space-y-2">
                            <p class="font-medium">Email:</p>
                            <p class="text-gray-600">media@g-trustbank.com</p>
                            <p class="font-medium mt-3">Phone:</p>
                            <p class="text-gray-600">(555) 123-4567</p>
                        </div>
                    </div>
                </div>
            </div>
        </ContentSection>
    </MainLayout>
</template>
