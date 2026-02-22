<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import PageHero from '@/components/shared/PageHero.vue';
import ContentSection from '@/components/shared/ContentSection.vue';
import CardGrid from '@/components/shared/CardGrid.vue';
import FeatureCard from '@/components/shared/FeatureCard.vue';
import CallToAction from '@/components/shared/CallToAction.vue';
import { User, Calendar, Phone, TrendingUp } from 'lucide-vue-next';
</script>

<template>
    <MainLayout title="Personal Managers - G-Trust">
        <PageHero
            title="Personal Managers"
            subtitle="Get dedicated support from your own personal finance manager"
            :image="'https://images.unsplash.com/photo-1556761175-4b46a572b786?w=1600'"
            :dark="true"
        />

        <ContentSection title="Personalized Finance Experience" :centered="true">
            <p class="text-lg text-gray-600 max-w-3xl mx-auto mb-12 text-center">
                Your personal finance manager is your dedicated partner in achieving your financial goals. Get expert
                guidance, priority service, and tailored solutions designed specifically for you.
            </p>

            <CardGrid :columns="4">
                <FeatureCard
                    :icon="User"
                    title="Dedicated Manager"
                    description="A single point of contact who knows you and your financial needs."
                />
                <FeatureCard
                    :icon="Calendar"
                    title="Flexible Meetings"
                    description="Schedule in-person, phone, or video meetings at your convenience."
                />
                <FeatureCard
                    :icon="Phone"
                    title="Priority Support"
                    description="Direct line to your manager with guaranteed response times."
                />
                <FeatureCard
                    :icon="TrendingUp"
                    title="Financial Planning"
                    description="Personalized strategies to grow and protect your wealth."
                />
            </CardGrid>
        </ContentSection>

        <ContentSection background="gray">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">What Your Personal Manager Can Do</h2>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <span class="text-red-500 font-bold text-xl">•</span>
                            <div>
                                <strong class="text-gray-900">Financial Goal Planning</strong>
                                <p class="text-gray-600 text-sm">
                                    Help you create and achieve short and long-term financial goals
                                </p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="text-red-500 font-bold text-xl">•</span>
                            <div>
                                <strong class="text-gray-900">Product Recommendations</strong>
                                <p class="text-gray-600 text-sm">
                                    Suggest the right accounts, cards, and services for your needs
                                </p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="text-red-500 font-bold text-xl">•</span>
                            <div>
                                <strong class="text-gray-900">Issue Resolution</strong>
                                <p class="text-gray-600 text-sm">Quickly resolve any account issues or concerns</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="text-red-500 font-bold text-xl">•</span>
                            <div>
                                <strong class="text-gray-900">Regular Check-ins</strong>
                                <p class="text-gray-600 text-sm">
                                    Proactive reviews to ensure your finances stay on track
                                </p>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="bg-gray-100 rounded-lg overflow-hidden">
                    <img
                        src="https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?w=800"
                        alt="Personal Finance Manager"
                        class="w-full h-[400px] object-cover"
                    />
                </div>
            </div>
        </ContentSection>

        <CallToAction
            title="Ready for personalized finance?"
            description="Connect with a personal finance manager and experience the difference"
            buttonText="Request a Manager"
            buttonLink="/register"
            background="blue"
        />
    </MainLayout>
</template>
