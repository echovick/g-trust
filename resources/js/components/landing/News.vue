<script setup lang="ts">
const news = [
    {
        title: 'When Next Listing Credit Cards Can Hurt You',
        image: 'https://images.unsplash.com/photo-1559526324-4b87b5e36e44?w=400',
        href: '/news/blog',
    },
    {
        title: 'Keep Your Children Safe from Financial Fraud',
        image: 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=400',
        href: '/news/blog',
    },
    {
        title: "You're Making More Money Here's How to Save",
        image: 'https://images.unsplash.com/photo-1579621970563-ebec7560ff3e?w=400',
        href: '/news/blog',
    },
    {
        title: 'Help and guidance: Personal Financial Advice Service',
        image: 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=400',
        href: '/help',
    },
];
</script>

<template>
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">Your news and information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <a
                    v-for="item in news"
                    :key="item.title"
                    :href="item.href"
                    class="bg-white rounded-lg overflow-hidden hover:shadow-lg transition-shadow cursor-pointer group"
                >
                    <div class="overflow-hidden">
                        <img
                            :src="item.image"
                            :alt="item.title"
                            class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300"
                        />
                    </div>
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 group-hover:text-red-500 transition-colors">
                            {{ item.title }}
                        </h3>
                    </div>
                </a>
            </div>
        </div>
    </section>
</template>
