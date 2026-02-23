<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import BankAuthLayout from '@/layouts/BankAuthLayout.vue';
import { Mail } from 'lucide-vue-next';

defineProps<{
    status?: string;
}>();
</script>

<template>
    <BankAuthLayout
        title="Verify Your Email"
        description="We sent a verification link to your email"
    >
        <Head title="Verify Email">
            <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
        </Head>

        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <Mail :size="32" class="text-green-500" />
            </div>
            <h2 class="text-xl font-semibold text-gray-900 mb-2">Email Verified!</h2>
            <p class="text-gray-600 text-sm mb-6">
                Your email has been verified. You can now access your account.
            </p>
        </div>

        <div class="space-y-4">
            <Link
                href="/dashboard"
                class="block w-full h-12 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-full transition-colors flex items-center justify-center"
            >
                Go to Dashboard
            </Link>

            <div class="text-center pt-4 border-t">
                <Link
                    href="/logout"
                    method="post"
                    as="button"
                    class="text-sm text-red-500 hover:text-red-600 font-medium"
                >
                    Log out
                </Link>
            </div>
        </div>
    </BankAuthLayout>
</template>
