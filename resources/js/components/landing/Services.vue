<script setup lang="ts">
import { Briefcase, TrendingUp, Smartphone, PiggyBank } from 'lucide-vue-next';

const services = [
    {
        title: 'Online Business',
        description: 'Explore competitive pricing with full service online banking. Bank online with over 250 services',
        icon: Briefcase,
        link: 'Apply online',
        href: '/services/online-business',
        color: 'bg-red-50',
        iconColor: 'text-red-500',
    },
    {
        title: 'Business Plan',
        description:
            "Your business deserves banking that puts you first. We offer everything you need, from deposits to bill tools, there's a huge range of investments to choose",
        icon: TrendingUp,
        link: 'Take the first step',
        href: '/services/business-plan',
        color: 'bg-red-50',
        iconColor: 'text-red-500',
    },
    {
        title: 'Mobile Bank',
        description: 'Explore competitive pricing with full service internet banking. Bank online with over 250 services',
        icon: Smartphone,
        link: 'Find out more',
        href: '/services/mobile-banking',
        color: 'bg-red-50',
        iconColor: 'text-red-500',
    },
    {
        title: 'Online Deposit',
        description: 'Explore competitive pricing with full service internet banking. Bank online with over 250 services',
        icon: PiggyBank,
        link: 'Learn more',
        href: '/banking/personal/checking',
        color: 'bg-red-50',
        iconColor: 'text-red-500',
    },
];
</script>

<template>
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">
                Presenting products and services that are right for you
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div
                    v-for="service in services"
                    :key="service.title"
                    class="bg-white rounded-lg p-6 hover:shadow-lg transition-shadow"
                >
                    <div :class="[service.color, 'w-14 h-14 rounded-lg flex items-center justify-center mb-4']">
                        <component :is="service.icon" :size="28" :class="service.iconColor" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">{{ service.title }}</h3>
                    <p class="text-sm text-gray-600 mb-4 leading-relaxed">{{ service.description }}</p>
                    <a :href="service.href" class="text-sm text-red-500 hover:text-red-600 font-medium">
                        {{ service.link }} →
                    </a>
                </div>
            </div>
        </div>
    </section>
</template>
