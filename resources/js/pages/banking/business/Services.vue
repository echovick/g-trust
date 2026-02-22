<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import PageHero from '@/components/shared/PageHero.vue';
import ContentSection from '@/components/shared/ContentSection.vue';
import CardGrid from '@/components/shared/CardGrid.vue';
import FeatureCard from '@/components/shared/FeatureCard.vue';
import { CreditCard, Globe, Users, Shield, Wallet, FileText } from 'lucide-vue-next';
</script>

<template>
    <MainLayout title="Additional Services - G-Trust">
        <PageHero
            title="Additional Finance Services"
            subtitle="Comprehensive solutions to support your business operations"
            :image="'https://images.unsplash.com/photo-1556742111-a301076d9d18?w=1600'"
            :dark="true"
        />

        <ContentSection title="Business Services" :centered="true">
            <CardGrid :columns="3">
                <FeatureCard
                    :icon="CreditCard"
                    title="Merchant Services"
                    description="Accept credit and debit card payments with competitive processing rates and modern POS solutions."
                    link="#"
                />
                <FeatureCard
                    :icon="Globe"
                    title="Wire Transfers"
                    description="Send and receive domestic and international wire transfers quickly and securely."
                    link="#"
                />
                <FeatureCard
                    :icon="Users"
                    title="Payroll Services"
                    description="Streamline payroll processing with integrated solutions and direct deposit."
                    link="#"
                />
                <FeatureCard
                    :icon="Shield"
                    title="Fraud Protection"
                    description="Advanced security features including positive pay and account reconciliation."
                    link="#"
                />
                <FeatureCard
                    :icon="Wallet"
                    title="Cash Management"
                    description="Optimize cash flow with sweep accounts, lockbox services, and more."
                    link="#"
                />
                <FeatureCard
                    :icon="FileText"
                    title="Business Credit Cards"
                    description="Flexible credit solutions with rewards, expense tracking, and employee cards."
                    link="#"
                />
            </CardGrid>
        </ContentSection>

        <ContentSection background="gray" title="Treasury Management">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h3 class="text-3xl font-bold text-gray-900 mb-6">Advanced Cash Management Solutions</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Our treasury management services help you optimize working capital, improve efficiency, and
                        reduce costs with powerful tools and expert support.
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-2">
                            <span class="text-red-500 font-bold">✓</span>
                            <span class="text-gray-600">ACH origination and management</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500 font-bold">✓</span>
                            <span class="text-gray-600">Remote deposit capture</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500 font-bold">✓</span>
                            <span class="text-gray-600">Zero balance accounts</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500 font-bold">✓</span>
                            <span class="text-gray-600">Account analysis and reporting</span>
                        </li>
                    </ul>
                </div>
                <div class="bg-gray-100 rounded-lg h-80 flex items-center justify-center">
                    <p class="text-gray-500">Treasury Dashboard Preview</p>
                </div>
            </div>
        </ContentSection>
    </MainLayout>
</template>
