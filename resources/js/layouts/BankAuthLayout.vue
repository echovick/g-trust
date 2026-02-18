<script setup lang="ts">
import { Link, Head } from '@inertiajs/vue3';
import { Landmark } from 'lucide-vue-next';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="min-h-screen bg-gray-50 flex">
        <Head>
            <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
        </Head>
        <!-- Left Side - Branding -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-red-600 to-red-700 p-12 flex-col justify-between relative overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;1&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
            </div>

            <div class="relative z-10">
                <Link :href="'/'" class="flex items-center gap-3 text-white">
                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center">
                        <Landmark :size="28" class="text-red-500" />
                    </div>
                    <span class="text-2xl font-bold">G-Trust Bank</span>
                </Link>
            </div>

            <div class="relative z-10">
                <h2 class="text-4xl font-bold text-white mb-4 leading-tight">
                    Secure Banking<br />at Your Fingertips
                </h2>
                <p class="text-red-100 text-lg">
                    Experience modern banking with industry-leading security and 24/7 access to your accounts.
                </p>
            </div>

            <div class="relative z-10 text-red-100 text-sm">
                <p>© 2026 G-Trust Bank. All rights reserved.</p>
            </div>
        </div>

        <!-- Right Side - Form -->
        <div class="flex-1 flex items-center justify-center p-8">
            <div class="w-full max-w-md">
                <!-- Mobile Logo -->
                <div class="lg:hidden mb-8 text-center">
                    <Link :href="'/'" class="inline-flex items-center gap-3">
                        <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center">
                            <Landmark :size="28" class="text-white" />
                        </div>
                        <span class="text-2xl font-bold text-gray-900">G-Trust Bank</span>
                    </Link>
                </div>

                <div class="bg-white rounded-2xl shadow-xl p-8 md:p-10">
                    <div class="mb-8">
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">{{ title }}</h1>
                        <p class="text-gray-600">{{ description }}</p>
                    </div>

                    <slot />
                </div>

                <p class="text-center text-sm text-gray-500 mt-6">
                    Protected by industry-leading security standards
                </p>
            </div>
        </div>
    </div>
</template>
