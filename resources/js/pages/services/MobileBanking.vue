<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import PageHero from '@/components/shared/PageHero.vue';
import ContentSection from '@/components/shared/ContentSection.vue';
import CardGrid from '@/components/shared/CardGrid.vue';
import FeatureCard from '@/components/shared/FeatureCard.vue';
import CallToAction from '@/components/shared/CallToAction.vue';
import { Smartphone, QrCode, Bell, Shield } from 'lucide-vue-next';
</script>

<template>
    <MainLayout title="Mobile Finance - G-Trust">
        <PageHero
            title="Mobile Finance"
            subtitle="Manage your finances anywhere, anytime with our award-winning mobile app"
            :image="'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=1600'"
            :dark="true"
        />

        <ContentSection title="Mobile Finance Features" :centered="true">
            <CardGrid :columns="4">
                <FeatureCard
                    :icon="Smartphone"
                    title="Mobile Check Deposit"
                    description="Deposit checks instantly by taking a photo with your phone."
                />
                <FeatureCard
                    :icon="QrCode"
                    title="Contactless Payments"
                    description="Pay securely with Apple Pay, Google Pay, and Samsung Pay."
                />
                <FeatureCard
                    :icon="Bell"
                    title="Real-Time Alerts"
                    description="Get instant notifications for all account activity and transactions."
                />
                <FeatureCard
                    :icon="Shield"
                    title="Biometric Security"
                    description="Secure login with fingerprint or face recognition."
                />
            </CardGrid>
        </ContentSection>

        <CallToAction
            title="Download the G-Trust app today"
            description="Experience the future of finance in the palm of your hand"
            buttonText="Get the App"
            background="blue"
        />
    </MainLayout>
</template>
