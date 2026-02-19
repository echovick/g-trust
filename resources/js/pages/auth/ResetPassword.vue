<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import BankAuthLayout from '@/layouts/BankAuthLayout.vue';
import { update } from '@/routes/password';

const props = defineProps<{
    token: string;
    email: string;
}>();

const inputEmail = ref(props.email);
</script>

<template>
    <BankAuthLayout
        title="Reset Password"
        description="Enter your new password below"
    >
        <Head title="Reset Password">
            <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
        </Head>

        <Form
            v-bind="update.form()"
            :transform="(data) => ({ ...data, token, email })"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
        >
            <div class="space-y-4">
                <div>
                    <Label for="email" class="text-gray-700 font-medium">Email</Label>
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        autocomplete="email"
                        v-model="inputEmail"
                        readonly
                        class="mt-2 h-12 border-gray-200 bg-gray-50 text-gray-600"
                    />
                    <InputError :message="errors.email" class="mt-1" />
                </div>

                <div>
                    <Label for="password" class="text-gray-700 font-medium">New Password</Label>
                    <Input
                        id="password"
                        type="password"
                        name="password"
                        autocomplete="new-password"
                        autofocus
                        placeholder="Enter new password"
                        class="mt-2 h-12 border-gray-300 focus:border-red-500 focus:ring-red-500"
                    />
                    <InputError :message="errors.password" class="mt-1" />
                </div>

                <div>
                    <Label for="password_confirmation" class="text-gray-700 font-medium">
                        Confirm Password
                    </Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        autocomplete="new-password"
                        placeholder="Confirm new password"
                        class="mt-2 h-12 border-gray-300 focus:border-red-500 focus:ring-red-500"
                    />
                    <InputError :message="errors.password_confirmation" class="mt-1" />
                </div>
            </div>

            <Button
                type="submit"
                class="mt-6 w-full h-12 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-full transition-colors"
                :disabled="processing"
                data-test="reset-password-button"
            >
                <Spinner v-if="processing" class="mr-2" />
                {{ processing ? 'Resetting...' : 'Reset Password' }}
            </Button>
        </Form>
    </BankAuthLayout>
</template>
