<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import PageHero from '@/components/shared/PageHero.vue';
import ContentSection from '@/components/shared/ContentSection.vue';
import { Calendar, MapPin, Clock, Users } from 'lucide-vue-next';

const upcomingEvents = [
    {
        title: 'First-Time Homebuyer Workshop',
        date: 'March 5, 2026',
        time: '6:00 PM - 8:00 PM',
        location: 'Downtown Branch',
        description: 'Learn everything you need to know about buying your first home, from mortgage options to closing costs.',
        spots: '15 spots remaining',
        type: 'Workshop',
    },
    {
        title: 'Small Business Financial Planning Seminar',
        date: 'March 12, 2026',
        time: '2:00 PM - 4:00 PM',
        location: 'Business Finance Center',
        description: 'Expert guidance on cash flow management, business credit, and growth strategies.',
        spots: '8 spots remaining',
        type: 'Seminar',
    },
    {
        title: 'Retirement Planning 101',
        date: 'March 18, 2026',
        time: '10:00 AM - 12:00 PM',
        location: 'Virtual Event',
        description: 'Understanding retirement accounts, investment strategies, and maximizing your savings.',
        spots: 'Unlimited',
        type: 'Virtual',
    },
    {
        title: 'Community Finance Day',
        date: 'March 25, 2026',
        time: '9:00 AM - 3:00 PM',
        location: 'All Branches',
        description: 'Visit any branch for special promotions, free financial consultations, and refreshments.',
        spots: 'Open to all',
        type: 'Open House',
    },
];

const pastEvents = [
    {
        title: 'Digital Finance Security Workshop',
        date: 'February 10, 2026',
        location: 'Downtown Branch',
    },
    {
        title: 'Investment Strategies Seminar',
        date: 'January 28, 2026',
        location: 'Virtual Event',
    },
    {
        title: 'New Year Financial Planning Workshop',
        date: 'January 15, 2026',
        location: 'Multiple Branches',
    },
];
</script>

<template>
    <MainLayout title="Events - G-Trust">
        <PageHero
            title="Events & Workshops"
            subtitle="Join us for educational events and community gatherings"
            :image="'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=1600'"
            :dark="true"
        />

        <ContentSection>
            <h2 class="text-3xl font-bold text-gray-900 mb-8">Upcoming Events</h2>
            <div class="space-y-6 mb-16">
                <div
                    v-for="event in upcomingEvents"
                    :key="event.title"
                    class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow"
                >
                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                        <div class="flex-1">
                            <div class="inline-block px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium mb-3">
                                {{ event.type }}
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ event.title }}</h3>
                            <p class="text-gray-600 mb-4">{{ event.description }}</p>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                                <div class="flex items-center gap-2">
                                    <Calendar :size="16" class="text-red-500" />
                                    <span>{{ event.date }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <Clock :size="16" class="text-red-500" />
                                    <span>{{ event.time }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <MapPin :size="16" class="text-red-500" />
                                    <span>{{ event.location }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-500 mt-3">
                                <Users :size="16" />
                                <span>{{ event.spots }}</span>
                            </div>
                        </div>
                        <div class="lg:w-48 flex-shrink-0">
                            <button class="w-full bg-red-500 hover:bg-red-600 text-white py-3 px-6 rounded-full font-medium transition-colors">
                                Register Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Past Events</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div
                        v-for="event in pastEvents"
                        :key="event.title"
                        class="bg-gray-50 border border-gray-200 rounded-lg p-6"
                    >
                        <h3 class="font-bold text-gray-900 mb-2">{{ event.title }}</h3>
                        <div class="text-sm text-gray-600 space-y-1">
                            <div class="flex items-center gap-2">
                                <Calendar :size="14" />
                                <span>{{ event.date }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <MapPin :size="14" />
                                <span>{{ event.location }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </ContentSection>
    </MainLayout>
</template>
