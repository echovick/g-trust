<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import PageHero from '@/components/shared/PageHero.vue';
import ContentSection from '@/components/shared/ContentSection.vue';
</script>

<template>
    <MainLayout title="Our History - G-Trust">
        <PageHero
            title="Our History"
            subtitle="A legacy of trust, innovation, and excellence spanning decades"
            :image="'https://images.unsplash.com/photo-1554224311-beee415c201f?w=1600'"
            :dark="true"
        />

        <ContentSection>
            <div class="max-w-4xl mx-auto">
                <div class="space-y-12">
                    <!-- Timeline Item -->
                    <div class="flex gap-8">
                        <div class="flex-shrink-0 w-24 text-right">
                            <span class="text-3xl font-bold text-red-500">1970</span>
                        </div>
                        <div class="flex-grow border-l-2 border-gray-200 pl-8 pb-12">
                            <h3 class="text-2xl font-bold text-gray-900 mb-3">The Beginning</h3>
                            <p class="text-gray-600 leading-relaxed">
                                G-Trust was founded with a simple mission: to provide honest, reliable financial
                                services to local communities. Starting with just one branch and 15 employees, we set
                                out to build something special.
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-8">
                        <div class="flex-shrink-0 w-24 text-right">
                            <span class="text-3xl font-bold text-red-500">1985</span>
                        </div>
                        <div class="flex-grow border-l-2 border-gray-200 pl-8 pb-12">
                            <h3 class="text-2xl font-bold text-gray-900 mb-3">National Expansion</h3>
                            <p class="text-gray-600 leading-relaxed">
                                Our commitment to excellence led to rapid growth. We expanded to 50 branches across the
                                country, serving over 100,000 customers and introducing innovative savings products.
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-8">
                        <div class="flex-shrink-0 w-24 text-right">
                            <span class="text-3xl font-bold text-red-500">1998</span>
                        </div>
                        <div class="flex-grow border-l-2 border-gray-200 pl-8 pb-12">
                            <h3 class="text-2xl font-bold text-gray-900 mb-3">Digital Revolution</h3>
                            <p class="text-gray-600 leading-relaxed">
                                We launched our first online finance platform, pioneering digital financial services and
                                making finance accessible 24/7. This marked our transformation into a modern,
                                technology-driven institution.
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-8">
                        <div class="flex-shrink-0 w-24 text-right">
                            <span class="text-3xl font-bold text-red-500">2010</span>
                        </div>
                        <div class="flex-grow border-l-2 border-gray-200 pl-8 pb-12">
                            <h3 class="text-2xl font-bold text-gray-900 mb-3">Mobile Innovation</h3>
                            <p class="text-gray-600 leading-relaxed">
                                Introduction of our award-winning mobile finance app, bringing full-featured finance to
                                customers' fingertips and setting new industry standards for mobile finance experiences.
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-8">
                        <div class="flex-shrink-0 w-24 text-right">
                            <span class="text-3xl font-bold text-red-500">2020</span>
                        </div>
                        <div class="flex-grow border-l-2 border-gray-200 pl-8">
                            <h3 class="text-2xl font-bold text-gray-900 mb-3">Global Reach</h3>
                            <p class="text-gray-600 leading-relaxed">
                                Reaching 5 million customers worldwide with presence in 15 countries. Our commitment to
                                innovation and customer service has made us a trusted partner for individuals and
                                businesses globally.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </ContentSection>

        <ContentSection background="gray">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Looking to the Future</h2>
                <p class="text-lg text-gray-600 leading-relaxed">
                    As we continue to grow and evolve, our commitment remains unchanged: to provide exceptional financial
                    services built on trust, innovation, and a deep understanding of our customers' needs. The next
                    chapter of our story is being written today, and we're excited to have you be part of it.
                </p>
            </div>
        </ContentSection>
    </MainLayout>
</template>
