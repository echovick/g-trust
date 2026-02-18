<script setup lang="ts">
import { Phone, MapPin, User, Search, Landmark } from 'lucide-vue-next';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const currentRoute = computed(() => page.url);

const navItems = [
    { name: 'Home', href: '/', active: false },
    { name: 'Credit Cards', href: '/cards', active: false },
    { name: 'Features', href: '/features', active: false },
    { name: 'Services', href: '/services', active: false },
    { name: 'About', href: '/about', active: false },
    { name: 'News', href: '/news', active: false },
];

const isActive = (href: string) => {
    return currentRoute.value === href || currentRoute.value.startsWith(href + '/');
};
</script>

<template>
    <header class="bg-white sticky top-0 z-50 shadow-sm">
        <div class="container mx-auto px-6">
            <div class="flex items-center justify-between relative">
                <div class="flex items-center gap-2 py-4">
                    <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center">
                        <Landmark :size="24" class="text-white" />
                    </div>
                    <span class="text-xl font-semibold text-gray-900">G-Trust Bank</span>
                </div>

                <nav class="hidden lg:flex items-center gap-8">
                    <a
                        v-for="item in navItems"
                        :key="item.name"
                        :href="item.href"
                        :class="[
                            'text-sm font-medium transition-colors relative py-6',
                            isActive(item.href)
                                ? 'text-red-500'
                                : 'text-gray-700 hover:text-red-500',
                        ]"
                    >
                        {{ item.name }}
                        <span
                            v-if="isActive(item.href)"
                            class="absolute left-0 right-0 h-0.5 bg-red-500"
                            style="bottom: -0.25rem;"
                        ></span>
                    </a>
                </nav>

                <div class="flex items-center gap-6">
                    <a
                        href="/contact"
                        class="text-sm text-gray-600 hover:text-gray-900 transition-colors hidden md:inline-flex items-center gap-1"
                    >
                        <Phone :size="16" />
                        <span>Contact Us</span>
                    </a>
                    <a
                        href="/locations"
                        class="text-sm text-gray-600 hover:text-gray-900 transition-colors hidden md:inline-flex items-center gap-1"
                    >
                        <MapPin :size="16" />
                        <span>Locations</span>
                    </a>
                    <a
                        href="/login"
                        class="text-sm text-gray-600 hover:text-gray-900 transition-colors hidden md:inline-flex items-center gap-1"
                    >
                        <User :size="16" />
                        <span>Sign In</span>
                    </a>
                    <a
                        href="/search"
                        class="text-sm text-gray-600 hover:text-gray-900 transition-colors hidden md:inline-flex items-center gap-1"
                    >
                        <Search :size="16" />
                        <span>Search</span>
                    </a>
                </div>
            </div>
        </div>
    </header>
</template>
