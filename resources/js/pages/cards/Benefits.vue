<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import PageHero from '@/components/shared/PageHero.vue';
import ContentSection from '@/components/shared/ContentSection.vue';
import CardGrid from '@/components/shared/CardGrid.vue';
import FeatureCard from '@/components/shared/FeatureCard.vue';
import { Shield, Plane, Gift, Headphones } from 'lucide-vue-next';
</script>

<template>
    <MainLayout title="Credit Card Benefits - G-Trust Bank">
        <PageHero
            title="Credit Card Benefits"
            subtitle="More than just a card - enjoy exclusive perks and protection"
            :image="'https://images.unsplash.com/photo-1559526324-4b87b5e36e44?w=1600'"
            :dark="true"
        />

        <ContentSection title="Premium Card Benefits" :centered="true">
            <CardGrid :columns="4">
                <FeatureCard
                    :icon="Shield"
                    title="Purchase Protection"
                    description="Coverage for eligible purchases against damage or theft for up to 120 days."
                />
                <FeatureCard
                    :icon="Plane"
                    title="Travel Insurance"
                    description="Complimentary travel accident insurance and trip cancellation protection."
                />
                <FeatureCard
                    :icon="Gift"
                    title="Extended Warranty"
                    description="Automatically extends manufacturer's warranty by an additional year."
                />
                <FeatureCard
                    :icon="Headphones"
                    title="24/7 Concierge"
                    description="Premium cardholders get access to concierge service for travel, dining, and events."
                />
            </CardGrid>
        </ContentSection>

        <ContentSection background="gray" title="Card-Specific Benefits">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white rounded-lg p-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Travel Cards</h3>
                    <ul class="space-y-3 text-gray-600">
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span>Airport lounge access worldwide</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span>No foreign transaction fees</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span>Travel accident insurance</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span>Baggage delay insurance</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span>Car rental coverage</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-white rounded-lg p-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Premium Cards</h3>
                    <ul class="space-y-3 text-gray-600">
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span>Dedicated account manager</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span>Exclusive event access</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span>Luxury hotel program</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span>Dining and entertainment credits</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span>Priority customer service</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-white rounded-lg p-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">All Cardholders</h3>
                    <ul class="space-y-3 text-gray-600">
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span>Zero fraud liability</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span>Card lock/unlock feature</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span>Real-time transaction alerts</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span>Virtual card numbers</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">✓</span>
                            <span>Contactless payments</span>
                        </li>
                    </ul>
                </div>
            </div>
        </ContentSection>
    </MainLayout>
</template>
