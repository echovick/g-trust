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
import { store } from '@/routes/register';
</script>

<template>
    <BankAuthLayout
        title="Create Account"
        description="Join G-Trust Bank today"
    >
        <Head title="Register">
            <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
        </Head>

        <Form
            v-bind="store.form()"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
            class="space-y-6"
        >
            <div class="space-y-4">
                <div>
                    <Label for="name" class="text-gray-700 font-medium">Full Name</Label>
                    <Input
                        id="name"
                        type="text"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="name"
                        name="name"
                        placeholder="John Doe"
                        class="mt-2 h-12 border-gray-300 focus:border-red-500 focus:ring-red-500"
                    />
                    <InputError :message="errors.name" class="mt-1" />
                </div>

                <div>
                    <Label for="email" class="text-gray-700 font-medium">Email Address</Label>
                    <Input
                        id="email"
                        type="email"
                        required
                        :tabindex="2"
                        autocomplete="email"
                        name="email"
                        placeholder="your@email.com"
                        class="mt-2 h-12 border-gray-300 focus:border-red-500 focus:ring-red-500"
                    />
                    <InputError :message="errors.email" class="mt-1" />
                </div>

                <div>
                    <Label for="password" class="text-gray-700 font-medium">Password</Label>
                    <Input
                        id="password"
                        type="password"
                        required
                        :tabindex="3"
                        autocomplete="new-password"
                        name="password"
                        placeholder="Create a strong password"
                        class="mt-2 h-12 border-gray-300 focus:border-red-500 focus:ring-red-500"
                    />
                    <InputError :message="errors.password" class="mt-1" />
                </div>

                <div>
                    <Label for="password_confirmation" class="text-gray-700 font-medium">Confirm Password</Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        required
                        :tabindex="4"
                        autocomplete="new-password"
                        name="password_confirmation"
                        placeholder="Confirm your password"
                        class="mt-2 h-12 border-gray-300 focus:border-red-500 focus:ring-red-500"
                    />
                    <InputError :message="errors.password_confirmation" class="mt-1" />
                </div>
            </div>

            <Button
                type="submit"
                class="w-full h-12 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-full transition-colors"
                tabindex="5"
                :disabled="processing"
                data-test="register-user-button"
            >
                <Spinner v-if="processing" class="mr-2" />
                {{ processing ? 'Creating account...' : 'Create Account' }}
            </Button>

            <div class="text-center text-sm text-gray-600 pt-4 border-t">
                Already have an account?
                <TextLink
                    :href="login()"
                    class="text-red-500 hover:text-red-600 font-medium"
                    :tabindex="6"
                >
                    Sign in
                </TextLink>
            </div>
        </Form>
    </BankAuthLayout>
</template>
