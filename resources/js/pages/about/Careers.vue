<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import PageHero from '@/components/shared/PageHero.vue';
import ContentSection from '@/components/shared/ContentSection.vue';
import CardGrid from '@/components/shared/CardGrid.vue';
import FeatureCard from '@/components/shared/FeatureCard.vue';
import { Heart, TrendingUp, Users, Coffee } from 'lucide-vue-next';
</script>

<template>
    <MainLayout title="Careers at G-Trust Bank">
        <PageHero
            title="Build Your Career With Us"
            subtitle="Join our team and be part of shaping the future of banking"
            :image="'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=1600'"
            :dark="true"
        />

        <ContentSection title="Why Work at G-Trust Bank?" :centered="true">
            <p class="text-lg text-gray-600 max-w-3xl mx-auto mb-12 text-center">
                At G-Trust Bank, we believe our people are our greatest asset. We foster a culture of innovation,
                collaboration, and continuous learning where every team member can thrive and make a meaningful impact.
            </p>

            <CardGrid :columns="4">
                <FeatureCard
                    :icon="Heart"
                    title="Great Benefits"
                    description="Competitive salary, health insurance, retirement plans, and more."
                />
                <FeatureCard
                    :icon="TrendingUp"
                    title="Career Growth"
                    description="Continuous learning opportunities and clear career progression paths."
                />
                <FeatureCard
                    :icon="Users"
                    title="Inclusive Culture"
                    description="Diverse, inclusive workplace where everyone's voice matters."
                />
                <FeatureCard
                    :icon="Coffee"
                    title="Work-Life Balance"
                    description="Flexible work arrangements and generous time-off policies."
                />
            </CardGrid>
        </ContentSection>

        <ContentSection background="gray" title="Current Openings">
            <div class="space-y-4">
                <div class="bg-white p-6 rounded-lg border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-xl font-semibold text-gray-900">Senior Software Engineer</h3>
                        <span class="text-sm text-gray-500">Technology</span>
                    </div>
                    <p class="text-gray-600 mb-4">
                        Join our engineering team to build next-generation banking platforms.
                    </p>
                    <div class="flex gap-4 items-center">
                        <span class="text-sm text-gray-500">📍 Remote</span>
                        <span class="text-sm text-gray-500">⏰ Full-time</span>
                        <a href="#" class="ml-auto text-red-500 hover:text-red-600 font-medium">Apply Now →</a>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-xl font-semibold text-gray-900">Customer Service Representative</h3>
                        <span class="text-sm text-gray-500">Customer Support</span>
                    </div>
                    <p class="text-gray-600 mb-4">
                        Provide exceptional service to our valued customers and help them achieve their goals.
                    </p>
                    <div class="flex gap-4 items-center">
                        <span class="text-sm text-gray-500">📍 Multiple Locations</span>
                        <span class="text-sm text-gray-500">⏰ Full-time</span>
                        <a href="#" class="ml-auto text-red-500 hover:text-red-600 font-medium">Apply Now →</a>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-xl font-semibold text-gray-900">Product Manager</h3>
                        <span class="text-sm text-gray-500">Product</span>
                    </div>
                    <p class="text-gray-600 mb-4">
                        Lead product strategy and development for our digital banking solutions.
                    </p>
                    <div class="flex gap-4 items-center">
                        <span class="text-sm text-gray-500">📍 Hybrid</span>
                        <span class="text-sm text-gray-500">⏰ Full-time</span>
                        <a href="#" class="ml-auto text-red-500 hover:text-red-600 font-medium">Apply Now →</a>
                    </div>
                </div>
            </div>

            <div class="text-center mt-12">
                <a
                    href="#"
                    class="inline-block bg-red-500 hover:bg-red-600 text-white px-10 py-4 rounded-full font-medium transition-colors"
                >
                    View All Open Positions
                </a>
            </div>
        </ContentSection>
    </MainLayout>
</template>
