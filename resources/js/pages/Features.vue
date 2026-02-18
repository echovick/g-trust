<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import PageHero from '@/components/shared/PageHero.vue';
import ContentSection from '@/components/shared/ContentSection.vue';
import CardGrid from '@/components/shared/CardGrid.vue';
import FeatureCard from '@/components/shared/FeatureCard.vue';
import { Smartphone, Lock, Zap, Users, Globe, TrendingUp } from 'lucide-vue-next';
</script>

<template>
    <MainLayout title="Features - G-Trust Bank">
        <PageHero
            title="Banking Features"
            subtitle="Discover the powerful features that make banking easier"
            :image="'https://images.unsplash.com/photo-1551434678-e076c223a692?w=1600'"
            :dark="true"
        />

        <ContentSection title="Core Features" :centered="true">
            <CardGrid :columns="3">
                <FeatureCard
                    :icon="Smartphone"
                    title="Mobile Banking"
                    description="Full-featured mobile app with biometric login, mobile check deposit, and instant notifications."
                />
                <FeatureCard
                    :icon="Lock"
                    title="Advanced Security"
                    description="Multi-factor authentication, fraud monitoring, and zero liability protection."
                />
                <FeatureCard
                    :icon="Zap"
                    title="Instant Transfers"
                    description="Send and receive money instantly with Zelle and same-day ACH transfers."
                />
                <FeatureCard
                    :icon="Users"
                    title="24/7 Support"
                    description="Round-the-clock customer support via phone, chat, or email whenever you need us."
                />
                <FeatureCard
                    :icon="Globe"
                    title="Global Access"
                    description="Access your accounts from anywhere in the world with international ATM access."
                />
                <FeatureCard
                    :icon="TrendingUp"
                    title="Financial Tools"
                    description="Budgeting tools, spending insights, and savings goals to help you achieve financial success."
                />
            </CardGrid>
        </ContentSection>
    </MainLayout>
</template>
