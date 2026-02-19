<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import BankAuthLayout from '@/layouts/BankAuthLayout.vue';
import { login } from '@/routes';
import { email } from '@/routes/password';

defineProps<{
    status?: string;
}>();
</script>

<template>
    <BankAuthLayout
        title="Forgot Password?"
        description="Enter your email to receive a password reset link"
    >
        <Head title="Forgot Password">
            <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
        </Head>

        <div
            v-if="status"
            class="mb-4 text-center text-sm font-medium text-green-600 bg-green-50 py-3 px-4 rounded-lg"
        >
            {{ status }}
        </div>

        <div class="space-y-6">
            <Form v-bind="email.form()" v-slot="{ errors, processing }">
                <div class="space-y-4">
                    <div>
                        <Label for="email" class="text-gray-700 font-medium">Email Address</Label>
                        <Input
                            id="email"
                            type="email"
                            name="email"
                            autocomplete="off"
                            autofocus
                            placeholder="your@email.com"
                            class="mt-2 h-12 border-gray-300 focus:border-red-500 focus:ring-red-500"
                        />
                        <InputError :message="errors.email" class="mt-1" />
                    </div>
                </div>

                <div class="mt-6">
                    <Button
                        class="w-full h-12 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-full transition-colors"
                        :disabled="processing"
                        data-test="email-password-reset-link-button"
                    >
                        <Spinner v-if="processing" class="mr-2" />
                        {{ processing ? 'Sending...' : 'Send Reset Link' }}
                    </Button>
                </div>
            </Form>

            <div class="text-center text-sm text-gray-600 pt-4 border-t">
                Remember your password?
                <TextLink :href="login()" class="text-red-500 hover:text-red-600 font-medium">
                    Back to login
                </TextLink>
            </div>
        </div>
    </BankAuthLayout>
</template>
