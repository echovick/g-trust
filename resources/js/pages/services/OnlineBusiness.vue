<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import PageHero from '@/components/shared/PageHero.vue';
import ContentSection from '@/components/shared/ContentSection.vue';
import CallToAction from '@/components/shared/CallToAction.vue';
</script>

<template>
    <MainLayout title="Online Business Banking - G-Trust Bank">
        <PageHero
            title="Online Business Banking"
            subtitle="Powerful online tools to manage your business finances"
            :image="'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=1600'"
            :dark="true"
        />

        <ContentSection>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">Streamline Your Business Banking</h2>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Our online business banking platform gives you complete control over your business finances with
                        powerful tools and features designed for modern businesses.
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-2">
                            <span class="text-red-500 font-bold">✓</span>
                            <span class="text-gray-600">Real-time account monitoring</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500 font-bold">✓</span>
                            <span class="text-gray-600">Batch payments and transfers</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500 font-bold">✓</span>
                            <span class="text-gray-600">Multi-user access controls</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500 font-bold">✓</span>
                            <span class="text-gray-600">Integrated accounting tools</span>
                        </li>
                    </ul>
                </div>
                <div class="bg-gray-100 rounded-lg h-96 flex items-center justify-center">
                    <p class="text-gray-500">Dashboard Preview</p>
                </div>
            </div>
        </ContentSection>

        <CallToAction
            title="Start banking online today"
            description="Get instant access to powerful business banking tools"
            buttonText="Get Started"
            background="blue"
        />
    </MainLayout>
</template>
