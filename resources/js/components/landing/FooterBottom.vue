<script setup lang="ts">
import { Landmark } from 'lucide-vue-next';

const brandLinks = [
    { name: 'About G-Trust', href: '/about' },
    { name: 'G-Trust Insight', href: '/about/insight' },
    { name: 'G-Trust History', href: '/about/history' },
    { name: 'Investors Information', href: '/about/investors' },
];

const sections = [
    {
        title: 'Finance With Us',
        links: [
            { name: 'Personal Finance', href: '/banking/personal' },
            { name: 'Mobile Finance', href: '/services/mobile-banking' },
            { name: 'G-Trust Insight', href: '/about/insight' },
            { name: 'G-Trust History', href: '/about/history' },
            { name: 'Services', href: '/services' },
        ],
    },
    {
        title: 'Customer Service',
        links: [
            { name: 'Help', href: '/help' },
            { name: 'About us', href: '/about' },
            { name: 'Locations', href: '/locations' },
            { name: 'Privacy policy', href: '/privacy' },
            { name: 'Accessibility', href: '/about/accessibility' },
        ],
    },
    {
        title: 'Socials',
        links: [
            { name: 'Facebook', href: '#' },
            { name: 'Twitter', href: '#' },
            { name: 'Instagram', href: '#' },
            { name: 'YouTube', href: '#' },
            { name: 'Pinterest', href: '#' },
        ],
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
                        <span class="text-xl font-semibold">G-Trust</span>
                    </div>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li v-for="link in brandLinks" :key="link.name">
                            <a :href="link.href" class="hover:text-white transition-colors">{{ link.name }}</a>
                        </li>
                    </ul>
                </div>
                <div v-for="section in sections" :key="section.title">
                    <h5 class="font-semibold mb-4 text-sm">{{ section.title }}</h5>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li v-for="link in section.links" :key="link.name">
                            <a :href="link.href" class="hover:text-white transition-colors">{{ link.name }}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>
