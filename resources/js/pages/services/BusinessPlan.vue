<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import PageHero from '@/components/shared/PageHero.vue';
import ContentSection from '@/components/shared/ContentSection.vue';
import CallToAction from '@/components/shared/CallToAction.vue';
</script>

<template>
    <MainLayout title="Business Plan Services - G-Trust">
        <PageHero
            title="Business Planning Services"
            subtitle="Expert guidance to help your business succeed"
            :image="'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=1600'"
            :dark="true"
        />

        <ContentSection>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">Comprehensive Business Planning</h2>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Our business planning services provide you with the tools, resources, and expertise needed to develop a solid business strategy and achieve your goals.
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-2">
                            <span class="text-red-500 font-bold">✓</span>
                            <span class="text-gray-600">Strategic planning workshops</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500 font-bold">✓</span>
                            <span class="text-gray-600">Financial projections and modeling</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500 font-bold">✓</span>
                            <span class="text-gray-600">Market analysis and research</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500 font-bold">✓</span>
                            <span class="text-gray-600">Business plan development</span>
                        </li>
                    </ul>
                </div>
                <div class="bg-gray-100 rounded-lg h-96 flex items-center justify-center">
                    <p class="text-gray-500">Business Planning Illustration</p>
                </div>
            </div>
        </ContentSection>

        <CallToAction
            title="Start planning for success"
            description="Schedule a consultation with our business planning experts"
            buttonText="Schedule Consultation"
            buttonLink="/register"
            background="blue"
        />
    </MainLayout>
</template>
