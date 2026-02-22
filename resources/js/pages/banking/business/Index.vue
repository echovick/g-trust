<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import PageHero from '@/components/shared/PageHero.vue';
import ContentSection from '@/components/shared/ContentSection.vue';
import CardGrid from '@/components/shared/CardGrid.vue';
import FeatureCard from '@/components/shared/FeatureCard.vue';
import CallToAction from '@/components/shared/CallToAction.vue';
import { Briefcase, TrendingUp, Shield, Users } from 'lucide-vue-next';
</script>

<template>
    <MainLayout title="Business Finance - G-Trust">
        <PageHero
            title="Business Finance"
            subtitle="Financial solutions designed to help your business thrive"
            :image="'https://images.unsplash.com/photo-1553877522-43269d4ea984?w=1600'"
            :dark="true"
        />

        <ContentSection title="Business Finance Solutions" :centered="true">
            <CardGrid :columns="4">
                <FeatureCard
                    :icon="Briefcase"
                    title="Business Checking"
                    description="Flexible checking accounts with unlimited transactions and low fees for your business needs."
                    link="#"
                />
                <FeatureCard
                    :icon="TrendingUp"
                    title="Business Savings"
                    description="Earn competitive interest while maintaining access to your business funds."
                    link="#"
                />
                <FeatureCard
                    :icon="Shield"
                    title="Merchant Services"
                    description="Accept payments seamlessly with our secure merchant processing solutions."
                    link="#"
                />
                <FeatureCard
                    :icon="Users"
                    title="Business Loans"
                    description="Flexible financing options to fuel your business growth and expansion."
                    link="#"
                />
            </CardGrid>
        </ContentSection>

        <CallToAction
            title="Ready to grow your business?"
            description="Partner with G-Trust for business finance solutions that scale with you"
            buttonText="Get Started"
            buttonLink="/register"
            background="blue"
        />
    </MainLayout>
</template>
