<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import PageHero from '@/components/shared/PageHero.vue';
import ContentSection from '@/components/shared/ContentSection.vue';
import CardGrid from '@/components/shared/CardGrid.vue';
import { Calendar, User, ArrowRight } from 'lucide-vue-next';

const blogPosts = [
    {
        title: '5 Smart Ways to Build Your Emergency Fund',
        excerpt: 'Learn practical strategies to establish and grow your financial safety net.',
        author: 'Sarah Johnson',
        date: 'February 12, 2026',
        category: 'Personal Finance',
        image: 'https://images.unsplash.com/photo-1579621970563-ebec7560ff3e?w=800',
    },
    {
        title: 'Understanding Business Credit: A Complete Guide',
        excerpt: 'Everything small business owners need to know about building strong business credit.',
        author: 'Michael Chen',
        date: 'February 8, 2026',
        category: 'Business',
        image: 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800',
    },
    {
        title: 'Digital Finance Security: Protecting Your Accounts',
        excerpt: 'Essential tips to keep your online finance safe and secure.',
        author: 'Jennifer Martinez',
        date: 'February 5, 2026',
        category: 'Security',
        image: 'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=800',
    },
    {
        title: 'Retirement Planning in Your 30s: Getting Started',
        excerpt: 'Why starting early makes all the difference for your retirement savings.',
        author: 'David Williams',
        date: 'January 30, 2026',
        category: 'Retirement',
        image: 'https://images.unsplash.com/photo-1434626881859-194d67b2b86f?w=800',
    },
    {
        title: 'Small Business Cash Flow Management Tips',
        excerpt: 'Proven strategies to maintain healthy cash flow for your business.',
        author: 'Lisa Anderson',
        date: 'January 25, 2026',
        category: 'Business',
        image: 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=800',
    },
    {
        title: 'Making Sense of Mortgage Rates',
        excerpt: 'A straightforward guide to understanding and comparing mortgage options.',
        author: 'Robert Taylor',
        date: 'January 20, 2026',
        category: 'Mortgages',
        image: 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=800',
    },
];
</script>

<template>
    <MainLayout title="Blog - G-Trust">
        <PageHero
            title="G-Trust Blog"
            subtitle="Financial insights, tips, and news to help you succeed"
            :image="'https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=1600'"
            :dark="true"
        />

        <ContentSection>
            <div class="mb-8">
                <div class="flex flex-wrap gap-3">
                    <button class="px-4 py-2 bg-red-500 text-white rounded-full text-sm font-medium">All Posts</button>
                    <button class="px-4 py-2 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-full text-sm font-medium">Personal Finance</button>
                    <button class="px-4 py-2 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-full text-sm font-medium">Business</button>
                    <button class="px-4 py-2 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-full text-sm font-medium">Security</button>
                    <button class="px-4 py-2 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-full text-sm font-medium">Retirement</button>
                </div>
            </div>

            <CardGrid :columns="3">
                <article
                    v-for="post in blogPosts"
                    :key="post.title"
                    class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow"
                >
                    <img :src="post.image" :alt="post.title" class="w-full h-48 object-cover" />
                    <div class="p-6">
                        <div class="text-xs font-medium text-red-500 mb-2">{{ post.category }}</div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">{{ post.title }}</h3>
                        <p class="text-gray-600 mb-4">{{ post.excerpt }}</p>
                        <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                            <div class="flex items-center gap-2">
                                <User :size="16" />
                                <span>{{ post.author }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <Calendar :size="16" />
                                <span>{{ post.date }}</span>
                            </div>
                        </div>
                        <a href="/news/blog" class="text-red-500 hover:text-red-600 font-medium inline-flex items-center gap-2">
                            Read more
                            <ArrowRight :size="16" />
                        </a>
                    </div>
                </article>
            </CardGrid>
        </ContentSection>
    </MainLayout>
</template>
