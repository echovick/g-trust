<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import BankAuthLayout from '@/layouts/BankAuthLayout.vue';
import { logout } from '@/routes';
import { send } from '@/routes/verification';
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
            <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <Mail :size="32" class="text-red-500" />
            </div>
            <p class="text-gray-600 text-sm">
                Please verify your email address by clicking on the link we just emailed to you.
                If you didn't receive the email, click the button below.
            </p>
        </div>

        <div
            v-if="status === 'verification-link-sent'"
            class="mb-4 text-center text-sm font-medium text-green-600 bg-green-50 py-3 px-4 rounded-lg"
        >
            A new verification link has been sent to your email address.
        </div>

        <Form
            v-bind="send.form()"
            class="space-y-6"
            v-slot="{ processing }"
        >
            <Button
                :disabled="processing"
                class="w-full h-12 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-full transition-colors"
            >
                <Spinner v-if="processing" class="mr-2" />
                {{ processing ? 'Sending...' : 'Resend Verification Email' }}
            </Button>

            <div class="text-center pt-4 border-t">
                <TextLink
                    :href="logout()"
                    as="button"
                    class="text-sm text-red-500 hover:text-red-600 font-medium"
                >
                    Log out
                </TextLink>
            </div>
        </Form>
    </BankAuthLayout>
</template>
