<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import PageHero from '@/components/shared/PageHero.vue';
import ContentSection from '@/components/shared/ContentSection.vue';
import { Eye, Ear, MousePointer, Keyboard, Phone, Mail } from 'lucide-vue-next';

const features = [
    {
        icon: Eye,
        title: 'Visual Accessibility',
        description: 'High contrast modes, adjustable text sizes, and screen reader compatibility for visually impaired users.',
    },
    {
        icon: Ear,
        title: 'Audio Accessibility',
        description: 'Video captions, transcripts, and visual alerts for users with hearing impairments.',
    },
    {
        icon: MousePointer,
        title: 'Motor Accessibility',
        description: 'Full keyboard navigation, voice control support, and clickable target sizing for ease of use.',
    },
    {
        icon: Keyboard,
        title: 'Keyboard Navigation',
        description: 'Complete site functionality accessible through keyboard shortcuts and tab navigation.',
    },
];
</script>

<template>
    <MainLayout title="Accessibility - G-Trust Bank">
        <PageHero
            title="Accessibility Commitment"
            subtitle="Banking services accessible to everyone"
            :image="'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=1600'"
            :dark="true"
        />

        <ContentSection>
            <div class="max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Our Commitment to Accessibility</h2>
                <p class="text-lg text-gray-600 mb-4">
                    At G-Trust Bank, we are committed to ensuring that our banking services, both digital and physical,
                    are accessible to all customers, including those with disabilities. We continuously work to enhance
                    the accessibility of our platforms and services.
                </p>
                <p class="text-lg text-gray-600">
                    Our website and mobile applications are designed to meet WCAG 2.1 Level AA standards, ensuring
                    that everyone can access our services with ease.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-16">
                <div
                    v-for="feature in features"
                    :key="feature.title"
                    class="bg-white border border-gray-200 rounded-lg p-8"
                >
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mb-4">
                        <component :is="feature.icon" :size="24" class="text-red-500" />
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">{{ feature.title }}</h3>
                    <p class="text-gray-600">{{ feature.description }}</p>
                </div>
            </div>
        </ContentSection>

        <ContentSection background="gray">
            <div class="max-w-3xl mx-auto">
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Branch Accessibility</h2>
                <div class="bg-white border border-gray-200 rounded-lg p-8 mb-8">
                    <p class="text-gray-600 mb-6">
                        All G-Trust Bank branches are designed with accessibility in mind:
                    </p>
                    <ul class="space-y-3 text-gray-600">
                        <li class="flex items-start gap-2">
                            <span class="text-red-500 font-bold">✓</span>
                            <span>Wheelchair accessible entrances and facilities</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500 font-bold">✓</span>
                            <span>Accessible ATMs with audio guidance and Braille instructions</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500 font-bold">✓</span>
                            <span>Hearing loop systems at teller counters</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500 font-bold">✓</span>
                            <span>Large print materials and forms available upon request</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500 font-bold">✓</span>
                            <span>Staff trained in accessibility awareness and assistance</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500 font-bold">✓</span>
                            <span>Accessible parking spaces near branch entrances</span>
                        </li>
                    </ul>
                </div>

                <h2 class="text-3xl font-bold text-gray-900 mb-6">Alternative Banking Options</h2>
                <div class="bg-white border border-gray-200 rounded-lg p-8">
                    <p class="text-gray-600 mb-6">
                        We offer multiple ways to bank that accommodate different accessibility needs:
                    </p>
                    <ul class="space-y-3 text-gray-600">
                        <li class="flex items-start gap-2">
                            <span class="text-red-500 font-bold">✓</span>
                            <span>24/7 telephone banking with TTY/TDD service available</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500 font-bold">✓</span>
                            <span>Video banking with ASL interpretation services</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500 font-bold">✓</span>
                            <span>Mobile banking app with VoiceOver and TalkBack support</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500 font-bold">✓</span>
                            <span>Online banking with full keyboard navigation</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500 font-bold">✓</span>
                            <span>Mail-in banking services for those unable to visit branches</span>
                        </li>
                    </ul>
                </div>
            </div>
        </ContentSection>

        <ContentSection>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-8 md:p-12">
                <div class="max-w-3xl mx-auto">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Accessibility Feedback</h2>
                    <p class="text-gray-600 mb-6">
                        We are continuously working to improve accessibility. If you encounter any accessibility barriers
                        or have suggestions for improvement, please contact our Accessibility Support Team.
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex items-start gap-3">
                            <Phone :size="20" class="text-red-500 mt-1" />
                            <div>
                                <div class="font-medium text-gray-900 mb-1">Phone</div>
                                <div class="text-gray-600">(555) 123-4567</div>
                                <div class="text-sm text-gray-500">TTY: (555) 123-4568</div>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <Mail :size="20" class="text-red-500 mt-1" />
                            <div>
                                <div class="font-medium text-gray-900 mb-1">Email</div>
                                <div class="text-gray-600">accessibility@g-trustbank.com</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </ContentSection>
    </MainLayout>
</template>
