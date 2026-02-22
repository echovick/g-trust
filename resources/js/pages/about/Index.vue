<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import PageHero from '@/components/shared/PageHero.vue';
import ContentSection from '@/components/shared/ContentSection.vue';
import CardGrid from '@/components/shared/CardGrid.vue';
import FeatureCard from '@/components/shared/FeatureCard.vue';
import CallToAction from '@/components/shared/CallToAction.vue';
import { Target, Users, Award, TrendingUp } from 'lucide-vue-next';
</script>

<template>
    <MainLayout title="About G-Trust">
        <PageHero
            title="About G-Trust"
            subtitle="Building trust through innovation and excellence in finance since our foundation"
            :image="'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=1600'"
            :dark="true"
        />

        <ContentSection title="Our Story" :centered="true">
            <div class="max-w-4xl mx-auto">
                <p class="text-lg text-gray-600 leading-relaxed mb-6">
                    G-Trust has been at the forefront of financial innovation for decades, serving millions of
                    customers worldwide with dedication, integrity, and excellence. Our commitment to providing
                    exceptional financial services has made us one of the most trusted names in finance.
                </p>
                <p class="text-lg text-gray-600 leading-relaxed">
                    From humble beginnings to becoming a global financial institution, we've never lost sight of our
                    core mission: to empower individuals and businesses to achieve their financial goals through
                    innovative solutions and personalized service.
                </p>
            </div>
        </ContentSection>

        <ContentSection title="Our Values" background="gray" :centered="true">
            <CardGrid :columns="4">
                <FeatureCard
                    :icon="Target"
                    title="Mission Driven"
                    description="We're committed to making finance accessible, simple, and beneficial for everyone."
                />
                <FeatureCard
                    :icon="Users"
                    title="Customer First"
                    description="Every decision we make is guided by what's best for our customers and communities."
                />
                <FeatureCard
                    :icon="Award"
                    title="Excellence"
                    description="We strive for excellence in everything we do, from service delivery to innovation."
                />
                <FeatureCard
                    :icon="TrendingUp"
                    title="Innovation"
                    description="We continuously evolve to meet changing needs with cutting-edge financial solutions."
                />
            </CardGrid>
        </ContentSection>

        <ContentSection>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h3 class="text-3xl font-bold text-gray-900 mb-4">Why Choose G-Trust?</h3>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <span class="text-red-500 font-bold">✓</span>
                            <span class="text-gray-600">Over 50 years of financial excellence and expertise</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="text-red-500 font-bold">✓</span>
                            <span class="text-gray-600">Award-winning customer service and support</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="text-red-500 font-bold">✓</span>
                            <span class="text-gray-600">Cutting-edge digital finance platforms</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="text-red-500 font-bold">✓</span>
                            <span class="text-gray-600">Comprehensive range of financial products</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="text-red-500 font-bold">✓</span>
                            <span class="text-gray-600">Commitment to community development and sustainability</span>
                        </li>
                    </ul>
                </div>
                <div class="bg-gray-100 rounded-lg overflow-hidden">
                    <img
                        src="https://images.unsplash.com/photo-1560179707-f14e90ef3623?w=800"
                        alt="G-Trust Team"
                        class="w-full h-[400px] object-cover"
                    />
                </div>
            </div>
        </ContentSection>

        <CallToAction
            title="Ready to experience financial services done right?"
            description="Join millions of satisfied customers who trust us with their financial future"
            buttonText="Open an Account"
            buttonLink="/register"
            background="blue"
        />
    </MainLayout>
</template>
