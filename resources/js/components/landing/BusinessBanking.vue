<script setup lang="ts">
import { ref } from 'vue';

const activeTab = ref('investing');

const tabs = [
    { id: 'investing', label: 'Investing Basics' },
    { id: 'credit', label: 'Find a Credit Card' },
    { id: 'payment', label: 'Payment Technologies' },
    { id: 'benefits', label: 'Card Benefits' },
    { id: 'wallet', label: 'Digital Wallets' },
];

const tabContent = {
    investing: {
        title: 'Nothing is impossible. We can help you achieve your goals!',
        description:
            "The UK's largest banking groups are required to comply with strict lending requirements from 1 January 2014. Find out more about our approach and what it means for you.",
        image: 'https://images.unsplash.com/photo-1553877522-43269d4ea984?w=800',
        alt: 'Business Meeting',
    },
    credit: {
        title: 'Find the perfect credit card for your business needs',
        description:
            'Compare our range of business credit cards with competitive rates, rewards programs, and flexible payment options. Whether you need cashback, travel rewards, or low interest rates, we have a card that fits your business.',
        image: 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=800',
        alt: 'Credit Cards',
    },
    payment: {
        title: 'Modern payment solutions for your growing business',
        description:
            'Stay ahead with cutting-edge payment technologies including contactless payments, mobile wallets, and online payment gateways. Our secure and reliable payment systems help you accept payments anywhere, anytime.',
        image: 'https://images.unsplash.com/photo-1556742502-ec7c0e9f34b1?w=800',
        alt: 'Payment Technology',
    },
    benefits: {
        title: 'Exclusive benefits designed for business success',
        description:
            'Enjoy premium card benefits including travel insurance, purchase protection, extended warranties, and exclusive business rewards. Get access to airport lounges, concierge services, and special discounts with partner merchants worldwide.',
        image: 'https://images.unsplash.com/photo-1559526324-4b87b5e36e44?w=800',
        alt: 'Card Benefits',
    },
    wallet: {
        title: 'Go digital with secure mobile wallet solutions',
        description:
            'Experience the convenience of digital wallets with G-Trust. Make instant payments, track expenses in real-time, and manage multiple cards from one secure app. Compatible with Apple Pay, Google Pay, and Samsung Pay.',
        image: 'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=800',
        alt: 'Digital Wallet',
    },
};
</script>

<template>
    <section class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center text-gray-900 mb-8">Business Finance</h2>

            <div class="flex justify-center gap-8 mb-12 border-b border-gray-200 overflow-x-auto">
                <button
                    v-for="tab in tabs"
                    :key="tab.id"
                    :class="[
                        'pb-4 text-sm font-medium transition-colors whitespace-nowrap',
                        activeTab === tab.id
                            ? 'text-red-500 border-b-2 border-red-500'
                            : 'text-gray-600 hover:text-gray-900',
                    ]"
                    @click="activeTab = tab.id"
                >
                    {{ tab.label }}
                </button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="bg-gray-100 rounded-lg overflow-hidden">
                    <img
                        :src="tabContent[activeTab].image"
                        :alt="tabContent[activeTab].alt"
                        class="w-full h-[350px] object-cover"
                    />
                </div>
                <div>
                    <h3 class="text-3xl font-bold text-gray-900 mb-4">
                        {{ tabContent[activeTab].title }}
                    </h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        {{ tabContent[activeTab].description }}
                    </p>
                    <button
                        class="bg-red-500 hover:bg-red-600 text-white px-8 py-3 rounded-full font-medium transition-colors"
                    >
                        Learn more
                    </button>
                </div>
            </div>
        </div>
    </section>
</template>
