<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import PageHero from '@/components/shared/PageHero.vue';
import ContentSection from '@/components/shared/ContentSection.vue';
import CardGrid from '@/components/shared/CardGrid.vue';
import FeatureCard from '@/components/shared/FeatureCard.vue';
import CallToAction from '@/components/shared/CallToAction.vue';
import { Laptop, Smartphone, Lock, Zap } from 'lucide-vue-next';
</script>

<template>
    <MainLayout title="E-Banking for Business - G-Trust Bank">
        <PageHero
            title="E-Banking for Business"
            subtitle="Powerful online and mobile banking for modern businesses"
            :image="'https://images.unsplash.com/photo-1551434678-e076c223a692?w=1600'"
            :dark="true"
        />

        <ContentSection title="Complete Digital Banking Platform" :centered="true">
            <CardGrid :columns="4">
                <FeatureCard
                    :icon="Laptop"
                    title="Online Banking"
                    description="Full-featured web platform accessible from any device, anywhere."
                />
                <FeatureCard
                    :icon="Smartphone"
                    title="Mobile App"
                    description="Bank on the go with our business mobile banking app for iOS and Android."
                />
                <FeatureCard
                    :icon="Lock"
                    title="Secure Access"
                    description="Multi-factor authentication and encryption protect your business data."
                />
                <FeatureCard
                    :icon="Zap"
                    title="Real-Time Updates"
                    description="Instant notifications and real-time account balances and transaction history."
                />
            </CardGrid>
        </ContentSection>

        <ContentSection background="gray">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">Online Banking Features</h2>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-2">
                            <span class="text-red-500 font-bold">✓</span>
                            <span class="text-gray-600">View account balances and transaction history</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500 font-bold">✓</span>
                            <span class="text-gray-600">Transfer funds between accounts</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500 font-bold">✓</span>
                            <span class="text-gray-600">Pay bills and schedule payments</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500 font-bold">✓</span>
                            <span class="text-gray-600">Manage users and permissions</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500 font-bold">✓</span>
                            <span class="text-gray-600">Download statements and reports</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500 font-bold">✓</span>
                            <span class="text-gray-600">Initiate wire transfers</span>
                        </li>
                    </ul>
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">Mobile Banking Features</h2>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-2">
                            <span class="text-red-500 font-bold">✓</span>
                            <span class="text-gray-600">Mobile check deposit</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500 font-bold">✓</span>
                            <span class="text-gray-600">Fingerprint and Face ID login</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500 font-bold">✓</span>
                            <span class="text-gray-600">Push notifications for account activity</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500 font-bold">✓</span>
                            <span class="text-gray-600">Find nearby branches and ATMs</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500 font-bold">✓</span>
                            <span class="text-gray-600">Secure messaging with support</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500 font-bold">✓</span>
                            <span class="text-gray-600">Quick balance check widget</span>
                        </li>
                    </ul>
                </div>
            </div>
        </ContentSection>

        <CallToAction
            title="Experience digital banking done right"
            description="Manage your business finances anytime, anywhere with our e-banking solutions"
            buttonText="Enroll Now"
            background="blue"
        />
    </MainLayout>
</template>
