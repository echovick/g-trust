<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import PageHero from '@/components/shared/PageHero.vue';
import ContentSection from '@/components/shared/ContentSection.vue';
import CardGrid from '@/components/shared/CardGrid.vue';
import { Phone, Mail, MessageCircle, MapPin } from 'lucide-vue-next';
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    subject: '',
    message: '',
});

const submit = () => {
    form.post('/contact', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <MainLayout title="Contact Us - G-Trust">
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
                    <h3 class="font-semibold text-gray-900 mb-2">Customer Service</h3>
                    <p class="text-gray-600 text-sm mb-2">Mon-Fri 8am-8pm</p>
                    <a href="tel:+16282657540" class="text-red-500 hover:text-red-600 font-medium"
                        >+1 6282657540</a
                    >
                </div>

                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <MessageCircle :size="32" class="text-red-500" />
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Live Chat</h3>
                    <p class="text-gray-600 text-sm mb-2">Available now</p>
                    <a href="/help" class="text-red-500 hover:text-red-600 font-medium">Start Chat</a>
                </div>

                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <MapPin :size="32" class="text-red-500" />
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Visit Us</h3>
                    <p class="text-gray-600 text-sm mb-2">Find a branch</p>
                    <a href="/locations" class="text-red-500 hover:text-red-600 font-medium">Locations</a>
                </div>
            </CardGrid>
        </ContentSection>

        <ContentSection background="gray">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">Send Us a Message</h2>
                    <form class="space-y-4" @submit.prevent="submit">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                            <input
                                v-model="form.name"
                                type="text"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                placeholder="Your name"
                            />
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input
                                v-model="form.email"
                                type="email"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                placeholder="your@email.com"
                            />
                            <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                            <input
                                v-model="form.subject"
                                type="text"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                placeholder="How can we help?"
                            />
                            <p v-if="form.errors.subject" class="mt-1 text-sm text-red-600">{{ form.errors.subject }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                            <textarea
                                v-model="form.message"
                                rows="5"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                placeholder="Your message..."
                            ></textarea>
                            <p v-if="form.errors.message" class="mt-1 text-sm text-red-600">{{ form.errors.message }}</p>
                        </div>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="bg-red-500 hover:bg-red-600 text-white px-8 py-3 rounded-full font-medium transition-colors disabled:opacity-60 disabled:cursor-not-allowed"
                        >
                            {{ form.processing ? 'Sending...' : 'Send Message' }}
                        </button>
                    </form>
                </div>

                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">Our Offices</h2>
                    <div class="bg-white p-6 rounded-lg border border-gray-200">
                        <h3 class="font-semibold text-gray-900 mb-4">USA Office</h3>
                        <p class="text-gray-600 mb-2">333 Market Street</p>
                        <p class="text-gray-600 mb-2">San Francisco, CA 94105</p>
                        <p class="text-gray-600 mb-4">United States</p>
                        <p class="text-gray-900 font-medium mb-1">Customer Service</p>
                        <a href="tel:+16282657540" class="text-red-500 hover:text-red-600 font-medium">+1 6282657540</a>
                    </div>
                </div>
            </div>
        </ContentSection>
    </MainLayout>
</template>
