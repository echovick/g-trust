<script setup lang="ts">
const footerSections = [
    {
        title: 'Personal Banking',
        links: [
            { name: 'Personal Checking', href: '/banking/personal/checking' },
            { name: 'Saving Accounts', href: '/banking/personal/savings' },
            { name: 'Personal Managers', href: '/banking/personal/managers' },
            { name: 'Credit Cards', href: '/cards' },
            { name: 'Personal Banking', href: '/banking/personal' },
        ],
    },
    {
        title: 'Business Banking',
        links: [
            { name: 'Business Checking', href: '/banking/business/checking' },
            { name: 'Business Savings', href: '/banking/business/savings' },
            { name: 'Small Business Administration', href: '/banking/business/sba' },
            { name: 'Banking Additional Services', href: '/banking/business/services' },
            { name: 'E-Banking', href: '/banking/business/e-banking' },
        ],
    },
    {
        title: 'Quick Links',
        links: [
            { name: 'About G-Trust Bank', href: '/about' },
            { name: 'G-Trust Bank News', href: '/news' },
            { name: 'Credit Cards', href: '/cards' },
            { name: 'G-Trust Bank Careers', href: '/about/careers' },
        ],
    },
    {
        title: 'News & Blog',
        links: [
            { name: 'G-Trust Bank Recent News', href: '/news' },
            { name: 'Media Centre', href: '/news/media' },
            { name: 'G-Trust Bank Blog', href: '/news/blog' },
            { name: 'G-Trust Bank Events', href: '/news/events' },
            { name: 'G-Trust Bank FAQs', href: '/help' },
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
                        <li v-for="link in section.links" :key="link.name">
                            <a :href="link.href" class="text-sm text-gray-600 hover:text-red-500 transition-colors">
                                {{ link.name }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
</template>
