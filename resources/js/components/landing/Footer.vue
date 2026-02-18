<script setup lang="ts">
const footerSections = [
    {
        title: 'Personal Banking',
        links: [
            'Personal Checking',
            'Saving Accounts',
            'Personal Managers',
            'Credit Cards',
            'Personal Banking',
        ],
    },
    {
        title: 'Business Banking',
        links: [
            'Business Checking',
            'Business Savings',
            'Small Business Administration',
            'Banking Additional Services',
            'E-Banking',
        ],
    },
    {
        title: 'Quick Links',
        links: ['About G-Trust Bank', 'G-Trust Bank News', 'Credit Cards', 'G-Trust Bank Careers'],
    },
    {
        title: 'News & Blog',
        links: [
            'G-Trust Bank Recent News',
            'Media Centre',
            'G-Trust Bank Blog',
            'G-Trust Bank Events',
            'G-Trust Bank FAQs',
        ],
    },
];
</script>

<template>
    <footer class="bg-white border-t border-gray-200 py-12">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div v-for="section in footerSections" :key="section.title">
                    <h4 class="font-semibold text-gray-900 mb-4">{{ section.title }}</h4>
                    <ul class="space-y-2">
                        <li v-for="link in section.links" :key="link">
                            <a href="#" class="text-sm text-gray-600 hover:text-red-500 transition-colors">
                                {{ link }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
</template>
