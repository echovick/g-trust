<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import PageHero from '@/components/shared/PageHero.vue';
import ContentSection from '@/components/shared/ContentSection.vue';
import CardGrid from '@/components/shared/CardGrid.vue';
import FeatureCard from '@/components/shared/FeatureCard.vue';
import { Smartphone, Globe, CreditCard, Lock } from 'lucide-vue-next';
</script>

<template>
    <MainLayout title="Services - G-Trust Bank">
        <PageHero
            title="Our Services"
            subtitle="Comprehensive banking services for all your financial needs"
            :image="'https://images.unsplash.com/photo-1556742502-ec7c0e9f34b1?w=1600'"
            :dark="true"
        />

        <ContentSection title="Digital Banking Services" :centered="true">
            <CardGrid :columns="4">
                <FeatureCard
                    :icon="Smartphone"
                    title="Mobile Banking"
                    description="Bank on the go with our full-featured mobile app for iOS and Android."
                    link="#"
                />
                <FeatureCard
                    :icon="Globe"
                    title="Online Banking"
                    description="Manage your accounts 24/7 from any device with our secure online platform."
                    link="#"
                />
                <FeatureCard
                    :icon="CreditCard"
                    title="Payment Solutions"
                    description="Modern payment technologies including contactless, mobile wallets, and more."
                    link="#"
                />
                <FeatureCard
                    :icon="Lock"
                    title="Security & Protection"
                    description="Advanced fraud protection and security features to keep your money safe."
                    link="#"
                />
            </CardGrid>
        </ContentSection>
    </MainLayout>
</template>
