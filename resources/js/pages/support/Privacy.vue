<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import PageHero from '@/components/shared/PageHero.vue';
import ContentSection from '@/components/shared/ContentSection.vue';
</script>

<template>
    <MainLayout title="Privacy Policy - G-Trust">
        <PageHero title="Privacy Policy" subtitle="Your privacy and security are our top priorities" :dark="false" />

        <ContentSection>
            <div class="max-w-4xl mx-auto prose prose-lg">
                <p class="text-gray-600 leading-relaxed mb-6">
                    Last updated: March 1, 2024
                </p>

                <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">Information We Collect</h2>
                <p class="text-gray-600 leading-relaxed mb-6">
                    We collect information necessary to provide you with excellent financial services, including personal
                    identification information, financial information, and transaction data.
                </p>

                <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">How We Use Your Information</h2>
                <p class="text-gray-600 leading-relaxed mb-6">
                    Your information is used to process transactions, maintain your accounts, comply with legal requirements,
                    and provide you with the best possible service experience.
                </p>

                <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">Information Security</h2>
                <p class="text-gray-600 leading-relaxed mb-6">
                    We employ industry-leading security measures to protect your personal and financial information,
                    including encryption, secure servers, and regular security audits.
                </p>

                <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">Your Rights</h2>
                <p class="text-gray-600 leading-relaxed mb-6">
                    You have the right to access, correct, or delete your personal information. Contact our privacy team
                    to exercise these rights.
                </p>
            </div>
        </ContentSection>
    </MainLayout>
</template>
