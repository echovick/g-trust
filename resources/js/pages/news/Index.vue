<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import PageHero from '@/components/shared/PageHero.vue';
import ContentSection from '@/components/shared/ContentSection.vue';
</script>

<template>
    <MainLayout title="News - G-Trust">
        <PageHero
            title="News & Updates"
            subtitle="Stay informed with the latest news from G-Trust"
            :image="'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=1600'"
            :dark="true"
        />

        <ContentSection>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <article class="bg-white rounded-lg overflow-hidden border border-gray-200 hover:shadow-lg transition-shadow">
                    <img src="https://images.unsplash.com/photo-1559526324-4b87b5e36e44?w=400" alt="News" class="w-full h-48 object-cover" />
                    <div class="p-6">
                        <span class="text-sm text-gray-500">March 15, 2024</span>
                        <h3 class="text-xl font-bold text-gray-900 mt-2 mb-3">G-Trust Launches New Mobile App Features</h3>
                        <p class="text-gray-600 mb-4">Discover the latest enhancements to our award-winning mobile finance app...</p>
                        <a href="/news/blog" class="text-red-500 hover:text-red-600 font-medium">Read More →</a>
                    </div>
                </article>

                <article class="bg-white rounded-lg overflow-hidden border border-gray-200 hover:shadow-lg transition-shadow">
                    <img src="https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=400" alt="News" class="w-full h-48 object-cover" />
                    <div class="p-6">
                        <span class="text-sm text-gray-500">March 10, 2024</span>
                        <h3 class="text-xl font-bold text-gray-900 mt-2 mb-3">New Interest Rates Announced</h3>
                        <p class="text-gray-600 mb-4">We're increasing our savings account rates to help your money grow faster...</p>
                        <a href="/news/blog" class="text-red-500 hover:text-red-600 font-medium">Read More →</a>
                    </div>
                </article>

                <article class="bg-white rounded-lg overflow-hidden border border-gray-200 hover:shadow-lg transition-shadow">
                    <img src="https://images.unsplash.com/photo-1579621970563-ebec7560ff3e?w=400" alt="News" class="w-full h-48 object-cover" />
                    <div class="p-6">
                        <span class="text-sm text-gray-500">March 5, 2024</span>
                        <h3 class="text-xl font-bold text-gray-900 mt-2 mb-3">Community Investment Initiative</h3>
                        <p class="text-gray-600 mb-4">G-Trust commits $10M to support local businesses and communities...</p>
                        <a href="/news/blog" class="text-red-500 hover:text-red-600 font-medium">Read More →</a>
                    </div>
                </article>
            </div>
        </ContentSection>
    </MainLayout>
</template>
