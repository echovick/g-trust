<script setup lang="ts">
import { Facebook, Twitter, Linkedin, Youtube, Instagram } from 'lucide-vue-next';

const topLinks = [
    { name: 'About Bank', href: '/about' },
    { name: 'Careers', href: '/about/careers' },
    { name: 'Credit Cards', href: '/cards' },
    { name: 'Services', href: '/services' },
];
</script>

<template>
    <div class="bg-gray-50 border-b border-gray-200">
        <div class="container mx-auto px-6">
            <div class="flex items-center justify-between py-2">
                <div class="hidden lg:flex items-center gap-6">
                    <a
                        v-for="link in topLinks"
                        :key="link.name"
                        :href="link.href"
                        class="text-sm text-gray-600 hover:text-gray-900 transition-colors"
                    >
                        {{ link.name }}
                    </a>
                </div>
                <div class="flex lg:hidden items-center gap-2 text-xs text-gray-600">
                    <span>Follow us</span>
                </div>
                <div class="flex items-center gap-2 lg:gap-3">
                    <a
                        href="#"
                        class="w-6 h-6 rounded-full bg-gray-300 hover:bg-gray-400 transition-colors flex items-center justify-center"
                        aria-label="Facebook"
                    >
                        <Facebook :size="14" class="text-white" />
                    </a>
                    <a
                        href="#"
                        class="w-6 h-6 rounded-full bg-gray-300 hover:bg-gray-400 transition-colors flex items-center justify-center"
                        aria-label="Twitter"
                    >
                        <Twitter :size="14" class="text-white" />
                    </a>
                    <a
                        href="#"
                        class="w-6 h-6 rounded-full bg-gray-300 hover:bg-gray-400 transition-colors flex items-center justify-center"
                        aria-label="LinkedIn"
                    >
                        <Linkedin :size="14" class="text-white" />
                    </a>
                    <a
                        href="#"
                        class="w-6 h-6 rounded-full bg-gray-300 hover:bg-gray-400 transition-colors flex items-center justify-center hidden sm:flex"
                        aria-label="YouTube"
                    >
                        <Youtube :size="14" class="text-white" />
                    </a>
                    <a
                        href="#"
                        class="w-6 h-6 rounded-full bg-gray-300 hover:bg-gray-400 transition-colors flex items-center justify-center hidden sm:flex"
                        aria-label="Instagram"
                    >
                        <Instagram :size="14" class="text-white" />
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>
