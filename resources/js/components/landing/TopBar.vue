<script setup lang="ts">
import { Facebook, Twitter, Linkedin, Youtube, Instagram } from 'lucide-vue-next';

const topLinks = [
    { name: 'About Bank', href: '#' },
    { name: 'Careers', href: '#' },
    { name: 'Credit Cards', href: '#' },
    { name: 'Services', href: '#' },
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
                <div class="flex items-center gap-3">
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
                        class="w-6 h-6 rounded-full bg-gray-300 hover:bg-gray-400 transition-colors flex items-center justify-center"
                        aria-label="YouTube"
                    >
                        <Youtube :size="14" class="text-white" />
                    </a>
                    <a
                        href="#"
                        class="w-6 h-6 rounded-full bg-gray-300 hover:bg-gray-400 transition-colors flex items-center justify-center"
                        aria-label="Instagram"
                    >
                        <Instagram :size="14" class="text-white" />
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>
