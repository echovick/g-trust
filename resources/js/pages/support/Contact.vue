<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import PageHero from '@/components/shared/PageHero.vue';
import ContentSection from '@/components/shared/ContentSection.vue';
import CardGrid from '@/components/shared/CardGrid.vue';
import { Phone, Mail, MessageCircle, MapPin } from 'lucide-vue-next';
</script>

<template>
    <MainLayout title="Contact Us - G-Trust Bank">
        <PageHero
            title="Contact Us"
            subtitle="We're here to help. Get in touch with us anytime"
            :image="'https://images.unsplash.com/photo-1423666639041-f56000c27a9a?w=1600'"
            :dark="true"
        />

        <ContentSection title="Get In Touch" :centered="true">
            <CardGrid :columns="4">
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <Phone :size="32" class="text-red-500" />
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Phone</h3>
                    <p class="text-gray-600 text-sm mb-2">Mon-Fri 8am-8pm</p>
                    <a href="tel:18004567890" class="text-red-500 hover:text-red-600 font-medium"
                        >1-800-456-7890</a
                    >
                </div>

                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <Mail :size="32" class="text-red-500" />
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Email</h3>
                    <p class="text-gray-600 text-sm mb-2">24/7 support</p>
                    <a href="mailto:support@gtrustbank.com" class="text-red-500 hover:text-red-600 font-medium"
                        >support@gtrustbank.com</a
                    >
                </div>

                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <MessageCircle :size="32" class="text-red-500" />
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Live Chat</h3>
                    <p class="text-gray-600 text-sm mb-2">Available now</p>
                    <a href="#" class="text-red-500 hover:text-red-600 font-medium">Start Chat</a>
                </div>

                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <MapPin :size="32" class="text-red-500" />
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Visit Us</h3>
                    <p class="text-gray-600 text-sm mb-2">Find a branch</p>
                    <a href="#" class="text-red-500 hover:text-red-600 font-medium">Locations</a>
                </div>
            </CardGrid>
        </ContentSection>

        <ContentSection background="gray">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">Send Us a Message</h2>
                    <form class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                            <input
                                type="text"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                placeholder="Your name"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input
                                type="email"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                placeholder="your@email.com"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                            <input
                                type="text"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                placeholder="How can we help?"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                            <textarea
                                rows="5"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                placeholder="Your message..."
                            ></textarea>
                        </div>
                        <button
                            type="submit"
                            class="bg-red-500 hover:bg-red-600 text-white px-8 py-3 rounded-full font-medium transition-colors"
                        >
                            Send Message
                        </button>
                    </form>
                </div>

                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">Headquarters</h2>
                    <div class="bg-white p-6 rounded-lg mb-6">
                        <h3 class="font-semibold text-gray-900 mb-4">G-Trust Bank Headquarters</h3>
                        <p class="text-gray-600 mb-2">123 Financial District</p>
                        <p class="text-gray-600 mb-2">New York, NY 10004</p>
                        <p class="text-gray-600 mb-4">United States</p>
                        <a href="#" class="text-red-500 hover:text-red-600 font-medium">Get Directions →</a>
                    </div>

                    <div class="bg-gray-300 rounded-lg h-64 flex items-center justify-center">
                        <p class="text-gray-600">Map Placeholder</p>
                    </div>
                </div>
            </div>
        </ContentSection>
    </MainLayout>
</template>
