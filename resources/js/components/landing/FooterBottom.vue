<script setup lang="ts">
import { Landmark } from 'lucide-vue-next';

const sections = [
    {
        title: 'Banking With Us',
        links: ['Personal Banking', 'Mobile Banking', 'G-Trust Bank Insight', 'G-Trust Bank History', 'Services'],
    },
    {
        title: 'Customer Service',
        links: ['Help', 'About us', 'Locations', 'Privacy policy', 'Accessibility'],
    },
    {
        title: 'Socials',
        links: ['Facebook', 'Twitter', 'Instagram', 'YouTube', 'Pinterest'],
    },
];
</script>

<template>
    <div class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center gap-2 mb-6">
                        <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center">
                            <Landmark :size="24" class="text-white" />
                        </div>
                        <span class="text-xl font-semibold">G-Trust Bank</span>
                    </div>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-white transition-colors">About G-Trust Bank</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">G-Trust Bank Insight</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">G-Trust Bank History</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Investors Information</a></li>
                    </ul>
                </div>
                <div v-for="section in sections" :key="section.title">
                    <h5 class="font-semibold mb-4 text-sm">{{ section.title }}</h5>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li v-for="link in section.links" :key="link">
                            <a href="#" class="hover:text-white transition-colors">{{ link }}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>
