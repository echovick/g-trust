<script setup lang="ts">
const topLinks = [
    { name: 'About Us', href: '/about' },
    { name: 'Careers', href: '/about/careers' },
    { name: 'Credit Cards', href: '/cards' },
    { name: 'Services', href: '/services' },
];
</script>

<template>
    <div class="bg-gray-50 border-b border-gray-200">
        <div class="container mx-auto px-6">
            <div class="flex items-center justify-between py-2">
                <div class="flex items-center gap-6">
                    <a
                        v-for="link in topLinks"
                        :key="link.name"
                        :href="link.href"
                        class="text-sm text-gray-600 hover:text-gray-900 transition-colors"
                    >
                        {{ link.name }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>
