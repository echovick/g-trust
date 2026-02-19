<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import BankAuthLayout from '@/layouts/BankAuthLayout.vue';
import { store } from '@/routes/password/confirm';
import { Shield } from 'lucide-vue-next';
</script>

<template>
    <BankAuthLayout
        title="Confirm Password"
        description="This is a secure area - please confirm your password"
    >
        <Head title="Confirm Password">
            <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
        </Head>

        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <Shield :size="32" class="text-red-500" />
            </div>
            <p class="text-gray-600 text-sm">
                For your security, please confirm your password before continuing.
            </p>
        </div>

        <Form
            v-bind="store.form()"
            reset-on-success
            v-slot="{ errors, processing }"
        >
            <div class="space-y-6">
                <div>
                    <Label htmlFor="password" class="text-gray-700 font-medium">Password</Label>
                    <Input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        autofocus
                        placeholder="Enter your password"
                        class="mt-2 h-12 border-gray-300 focus:border-red-500 focus:ring-red-500"
                    />
                    <InputError :message="errors.password" class="mt-1" />
                </div>

                <Button
                    class="w-full h-12 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-full transition-colors"
                    :disabled="processing"
                    data-test="confirm-password-button"
                >
                    <Spinner v-if="processing" class="mr-2" />
                    {{ processing ? 'Confirming...' : 'Confirm Password' }}
                </Button>
            </div>
        </Form>
    </BankAuthLayout>
</template>
