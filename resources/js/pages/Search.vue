<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import PageHero from '@/components/shared/PageHero.vue';
import ContentSection from '@/components/shared/ContentSection.vue';
</script>

<template>
    <MainLayout title="Search - G-Trust Bank">
        <PageHero title="Search" subtitle="Find what you're looking for" :dark="false" />

        <ContentSection>
            <div class="max-w-2xl mx-auto">
                <input
                    type="text"
                    placeholder="Search for accounts, services, help articles..."
                    class="w-full px-6 py-4 border border-gray-300 rounded-full focus:ring-2 focus:ring-red-500 focus:border-transparent text-lg mb-8"
                />

                <div class="text-center text-gray-500">
                    <p>Enter a search term to find relevant pages and information</p>
                </div>
            </div>
        </ContentSection>
    </MainLayout>
</template>
