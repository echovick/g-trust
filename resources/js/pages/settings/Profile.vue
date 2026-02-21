<script setup lang="ts">
import { Form, Link, usePage } from '@inertiajs/vue3';
import { User, Mail, Shield, Trash2, Eye, Settings as SettingsIcon } from 'lucide-vue-next';
import DeleteUser from '@/components/DeleteUser.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import { send } from '@/routes/verification';

type Props = {
    mustVerifyEmail: boolean;
    status?: string;
};

defineProps<Props>();

const page = usePage();
const user = page.props.auth.user;

const settingsNav = [
    { name: 'Profile', href: '/settings/profile', icon: User, current: true },
    { name: 'Password', href: '/settings/password', icon: Shield },
    { name: 'Two-Factor Auth', href: '/settings/two-factor', icon: Eye },
    { name: 'Appearance', href: '/settings/appearance', icon: SettingsIcon },
];
</script>

<template>
    <DashboardLayout title="Profile Settings">
        <div class="mb-8">
            <p class="text-gray-600">Manage your profile and account settings</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Settings Navigation -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                    <nav class="space-y-1">
                        <Link
                            v-for="item in settingsNav"
                            :key="item.name"
                            :href="item.href"
                            :class="[
                                'flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-colors',
                                item.current
                                    ? 'bg-red-50 text-red-600'
                                    : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
                            ]"
                        >
                            <component :is="item.icon" :size="20" />
                            {{ item.name }}
                        </Link>
                    </nav>
                </div>
            </div>

            <!-- Settings Content -->
            <div class="lg:col-span-3 space-y-6">
                <!-- Profile Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 bg-red-50 rounded-full flex items-center justify-center">
                            <User :size="24" class="text-red-500" />
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Profile Information</h3>
                            <p class="text-sm text-gray-600">Update your name and email address</p>
                        </div>
                    </div>

                    <Form
                        v-bind="ProfileController.update.form()"
                        class="space-y-6"
                        v-slot="{ errors, processing, recentlySuccessful }"
                    >
                        <div class="grid gap-2">
                            <Label for="name" class="text-sm font-medium text-gray-700">Name</Label>
                            <Input
                                id="name"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-gray-900"
                                name="name"
                                :default-value="user.name"
                                required
                                autocomplete="name"
                                placeholder="Full name"
                            />
                            <InputError class="mt-2" :message="errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="email" class="text-sm font-medium text-gray-700">Email address</Label>
                            <Input
                                id="email"
                                type="email"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-gray-900"
                                name="email"
                                :default-value="user.email"
                                required
                                autocomplete="username"
                                placeholder="Email address"
                            />
                            <InputError class="mt-2" :message="errors.email" />
                        </div>

                        <div v-if="mustVerifyEmail && !user.email_verified_at" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <Mail :size="20" class="text-yellow-600 mt-0.5 flex-shrink-0" />
                                <div>
                                    <p class="text-sm text-yellow-800">
                                        Your email address is unverified.
                                        <Link
                                            :href="send()"
                                            as="button"
                                            class="font-medium underline hover:no-underline"
                                        >
                                            Click here to resend the verification email.
                                        </Link>
                                    </p>

                                    <div
                                        v-if="status === 'verification-link-sent'"
                                        class="mt-2 text-sm font-medium text-green-600"
                                    >
                                        A new verification link has been sent to your email address.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 pt-4 border-t">
                            <Button
                                :disabled="processing"
                                class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-lg font-medium transition-colors disabled:opacity-50"
                                data-test="update-profile-button"
                            >
                                {{ processing ? 'Saving...' : 'Save Changes' }}
                            </Button>

                            <Transition
                                enter-active-class="transition ease-in-out"
                                enter-from-class="opacity-0"
                                leave-active-class="transition ease-in-out"
                                leave-to-class="opacity-0"
                            >
                                <p
                                    v-show="recentlySuccessful"
                                    class="text-sm text-green-600 font-medium"
                                >
                                    ✓ Saved successfully
                                </p>
                            </Transition>
                        </div>
                    </Form>
                </div>

                <!-- Delete Account -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 bg-red-50 rounded-full flex items-center justify-center">
                            <Trash2 :size="24" class="text-red-500" />
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Delete Account</h3>
                            <p class="text-sm text-gray-600">Permanently delete your account and all data</p>
                        </div>
                    </div>

                    <DeleteUser />
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>
