<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import PageHero from '@/components/shared/PageHero.vue';
import ContentSection from '@/components/shared/ContentSection.vue';
</script>

<template>
    <MainLayout title="Apply for a Credit Card - G-Trust">
        <PageHero
            title="Credit Card Application"
            subtitle="Apply online in minutes and get a decision instantly"
            :image="'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=1600'"
            :dark="true"
        />

        <ContentSection>
            <div class="max-w-3xl mx-auto">
                <div class="bg-white rounded-lg border border-gray-200 p-8 mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Application Information</h2>

                    <form class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                                <input
                                    type="text"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                    placeholder="John"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                                <input
                                    type="text"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                    placeholder="Doe"
                                />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input
                                type="email"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                placeholder="john@example.com"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <input
                                type="tel"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                placeholder="(555) 123-4567"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Annual Income</label>
                            <input
                                type="number"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                placeholder="50000"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Select Card</label>
                            <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                                <option>Cashback Rewards Card</option>
                                <option>Travel Rewards Card</option>
                                <option>Premium Platinum Card</option>
                            </select>
                        </div>

                        <div class="flex items-start gap-2">
                            <input type="checkbox" class="mt-1 rounded border-gray-300 text-red-500 focus:ring-red-500" />
                            <label class="text-sm text-gray-600">
                                I agree to the terms and conditions and authorize a credit check
                            </label>
                        </div>

                        <button
                            type="submit"
                            class="w-full bg-red-500 hover:bg-red-600 text-white py-4 rounded-full font-medium transition-colors text-lg"
                        >
                            Submit Application
                        </button>
                    </form>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <h3 class="font-semibold text-gray-900 mb-2">What happens next?</h3>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li>• You'll receive an instant decision in most cases</li>
                        <li>• If approved, your card will arrive within 7-10 business days</li>
                        <li>• Activate your card and start earning rewards immediately</li>
                    </ul>
                </div>
            </div>
        </ContentSection>
    </MainLayout>
</template>
