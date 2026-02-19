<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import BankAuthLayout from '@/layouts/BankAuthLayout.vue';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();
</script>

<template>
    <BankAuthLayout
        title="Welcome Back"
        description="Sign in to access your account"
    >
        <Head title="Log in">
            <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
        </Head>

        <div
            v-if="status"
            class="mb-4 text-center text-sm font-medium text-green-600 bg-green-50 py-2 px-4 rounded-lg"
        >
            {{ status }}
        </div>

        <Form
            v-bind="store.form()"
            :reset-on-success="['password']"
            v-slot="{ errors, processing }"
            class="space-y-6"
        >
            <div class="space-y-4">
                <div>
                    <Label for="email" class="text-gray-700 font-medium">Email Address</Label>
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="email"
                        placeholder="your@email.com"
                        class="mt-2 h-12 border-gray-300 focus:border-red-500 focus:ring-red-500"
                    />
                    <InputError :message="errors.email" class="mt-1" />
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <Label for="password" class="text-gray-700 font-medium">Password</Label>
                        <TextLink
                            v-if="canResetPassword"
                            :href="request()"
                            class="text-sm text-red-500 hover:text-red-600"
                            :tabindex="5"
                        >
                            Forgot password?
                        </TextLink>
                    </div>
                    <Input
                        id="password"
                        type="password"
                        name="password"
                        required
                        :tabindex="2"
                        autocomplete="current-password"
                        placeholder="Enter your password"
                        class="h-12 border-gray-300 focus:border-red-500 focus:ring-red-500"
                    />
                    <InputError :message="errors.password" class="mt-1" />
                </div>

                <div class="flex items-center">
                    <Label for="remember" class="flex items-center space-x-2 cursor-pointer">
                        <Checkbox id="remember" name="remember" :tabindex="3" class="border-gray-300 text-red-500" />
                        <span class="text-sm text-gray-700">Keep me signed in</span>
                    </Label>
                </div>
            </div>

            <Button
                type="submit"
                class="w-full h-12 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-full transition-colors"
                :tabindex="4"
                :disabled="processing"
                data-test="login-button"
            >
                <Spinner v-if="processing" class="mr-2" />
                {{ processing ? 'Signing in...' : 'Sign In' }}
            </Button>

            <div
                class="text-center text-sm text-gray-600 pt-4 border-t"
                v-if="canRegister"
            >
                Don't have an account?
                <TextLink :href="register()" class="text-red-500 hover:text-red-600 font-medium" :tabindex="5">
                    Create account
                </TextLink>
            </div>
        </Form>
    </BankAuthLayout>
</template>
