<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import PageHero from '@/components/shared/PageHero.vue';
import ContentSection from '@/components/shared/ContentSection.vue';
import { MapPin, Clock, Phone } from 'lucide-vue-next';
</script>

<template>
    <MainLayout title="Branch Locations - G-Trust">
        <PageHero
            title="Branch Locations"
            subtitle="Find a G-Trust branch near you"
            :image="'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=1600'"
            :dark="true"
        />

        <ContentSection>
            <div class="mb-8">
                <input
                    type="text"
                    placeholder="Search by city, state, or ZIP code"
                    class="w-full max-w-2xl mx-auto block px-6 py-4 border border-gray-300 rounded-full focus:ring-2 focus:ring-red-500 focus:border-transparent text-lg"
                />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-12">
                <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Manhattan - Financial District</h3>
                    <div class="space-y-3 text-gray-600">
                        <div class="flex items-start gap-2">
                            <MapPin :size="20" class="text-red-500 flex-shrink-0 mt-1" />
                            <p>123 Wall Street<br />New York, NY 10005</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <Phone :size="20" class="text-red-500 flex-shrink-0 mt-1" />
                            <p>(212) 555-0100</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <Clock :size="20" class="text-red-500 flex-shrink-0 mt-1" />
                            <p>Mon-Fri: 9am-5pm<br />Sat: 9am-2pm</p>
                        </div>
                    </div>
                    <a href="/contact" class="inline-block mt-4 text-red-500 hover:text-red-600 font-medium"
                        >Get Directions →</a
                    >
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Los Angeles - Downtown</h3>
                    <div class="space-y-3 text-gray-600">
                        <div class="flex items-start gap-2">
                            <MapPin :size="20" class="text-red-500 flex-shrink-0 mt-1" />
                            <p>456 Spring Street<br />Los Angeles, CA 90012</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <Phone :size="20" class="text-red-500 flex-shrink-0 mt-1" />
                            <p>(213) 555-0200</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <Clock :size="20" class="text-red-500 flex-shrink-0 mt-1" />
                            <p>Mon-Fri: 9am-6pm<br />Sat: 10am-3pm</p>
                        </div>
                    </div>
                    <a href="/contact" class="inline-block mt-4 text-red-500 hover:text-red-600 font-medium"
                        >Get Directions →</a
                    >
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Chicago - Loop</h3>
                    <div class="space-y-3 text-gray-600">
                        <div class="flex items-start gap-2">
                            <MapPin :size="20" class="text-red-500 flex-shrink-0 mt-1" />
                            <p>789 Michigan Avenue<br />Chicago, IL 60601</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <Phone :size="20" class="text-red-500 flex-shrink-0 mt-1" />
                            <p>(312) 555-0300</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <Clock :size="20" class="text-red-500 flex-shrink-0 mt-1" />
                            <p>Mon-Fri: 8:30am-5:30pm<br />Sat: 9am-1pm</p>
                        </div>
                    </div>
                    <a href="/contact" class="inline-block mt-4 text-red-500 hover:text-red-600 font-medium"
                        >Get Directions →</a
                    >
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Houston - Midtown</h3>
                    <div class="space-y-3 text-gray-600">
                        <div class="flex items-start gap-2">
                            <MapPin :size="20" class="text-red-500 flex-shrink-0 mt-1" />
                            <p>321 Main Street<br />Houston, TX 77002</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <Phone :size="20" class="text-red-500 flex-shrink-0 mt-1" />
                            <p>(713) 555-0400</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <Clock :size="20" class="text-red-500 flex-shrink-0 mt-1" />
                            <p>Mon-Fri: 9am-5pm<br />Sat: 10am-2pm</p>
                        </div>
                    </div>
                    <a href="/contact" class="inline-block mt-4 text-red-500 hover:text-red-600 font-medium"
                        >Get Directions →</a
                    >
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">San Francisco - SOMA</h3>
                    <div class="space-y-3 text-gray-600">
                        <div class="flex items-start gap-2">
                            <MapPin :size="20" class="text-red-500 flex-shrink-0 mt-1" />
                            <p>987 Market Street<br />San Francisco, CA 94103</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <Phone :size="20" class="text-red-500 flex-shrink-0 mt-1" />
                            <p>(415) 555-0500</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <Clock :size="20" class="text-red-500 flex-shrink-0 mt-1" />
                            <p>Mon-Fri: 9am-6pm<br />Sat: 10am-3pm</p>
                        </div>
                    </div>
                    <a href="/contact" class="inline-block mt-4 text-red-500 hover:text-red-600 font-medium"
                        >Get Directions →</a
                    >
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Boston - Back Bay</h3>
                    <div class="space-y-3 text-gray-600">
                        <div class="flex items-start gap-2">
                            <MapPin :size="20" class="text-red-500 flex-shrink-0 mt-1" />
                            <p>654 Boylston Street<br />Boston, MA 02116</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <Phone :size="20" class="text-red-500 flex-shrink-0 mt-1" />
                            <p>(617) 555-0600</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <Clock :size="20" class="text-red-500 flex-shrink-0 mt-1" />
                            <p>Mon-Fri: 8am-5pm<br />Sat: 9am-1pm</p>
                        </div>
                    </div>
                    <a href="/contact" class="inline-block mt-4 text-red-500 hover:text-red-600 font-medium"
                        >Get Directions →</a
                    >
                </div>
            </div>
        </ContentSection>
    </MainLayout>
</template>
