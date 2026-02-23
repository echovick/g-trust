<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import BankAuthLayout from '@/layouts/BankAuthLayout.vue';
import { CheckCircle, Mail } from 'lucide-vue-next';

defineProps<{
    userName?: string;
}>();
</script>

<template>
    <BankAuthLayout
        title="Registration Successful"
        description="Welcome to G-Trust Bank"
    >
        <Head title="Registration Successful">
            <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
        </Head>

        <div class="space-y-6 text-center">
            <!-- Success Icon -->
            <div class="flex justify-center">
                <div class="relative">
                    <div class="absolute inset-0 bg-green-100 rounded-full animate-ping opacity-75"></div>
                    <div class="relative bg-green-500 rounded-full p-4">
                        <CheckCircle class="w-16 h-16 text-white" />
                    </div>
                </div>
            </div>

            <!-- Welcome Message -->
            <div class="space-y-3">
                <h2 class="text-2xl font-bold text-gray-900">
                    Welcome to G-Trust Bank{{ userName ? ', ' + userName : '' }}!
                </h2>
                <p class="text-gray-600 text-lg">
                    Your account has been created successfully.
                </p>
            </div>

            <!-- Email Notification -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <Mail class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" />
                    <div class="text-left">
                        <p class="text-sm font-medium text-blue-900">
                            Account Details Sent
                        </p>
                        <p class="text-sm text-blue-700 mt-1">
                            We've sent your account details and welcome information to your email address.
                            Please check your inbox (and spam folder) for next steps.
                        </p>
                    </div>
                </div>
            </div>

            <!-- CTA Button -->
            <div class="pt-4">
                <Button
                    href="/dashboard"
                    as="a"
                    class="w-full h-12 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-full transition-colors"
                >
                    Continue to Dashboard
                </Button>
            </div>

            <!-- Additional Info -->
            <p class="text-sm text-gray-500 pt-2">
                You can start exploring your account and our banking services.
            </p>
        </div>
    </BankAuthLayout>
</template>
