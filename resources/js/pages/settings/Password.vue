<script setup lang="ts">
import { Form, Link } from '@inertiajs/vue3';
import { User, Shield, Eye, Settings as SettingsIcon, Lock } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import PasswordController from '@/actions/App/Http/Controllers/Settings/PasswordController';

const settingsNav = [
    { name: 'Profile', href: '/settings/profile', icon: User },
    { name: 'Password', href: '/settings/password', icon: Shield, current: true },
    { name: 'Two-Factor Auth', href: '/settings/two-factor', icon: Eye },
    { name: 'Appearance', href: '/settings/appearance', icon: SettingsIcon },
];
</script>

<template>
    <DashboardLayout title="Password Settings">
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
            <div class="lg:col-span-3">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 bg-red-50 rounded-full flex items-center justify-center">
                            <Lock :size="24" class="text-red-500" />
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Update Password</h3>
                            <p class="text-sm text-gray-600">Ensure your account is using a long, random password to stay secure</p>
                        </div>
                    </div>

                    <Form
                        v-bind="PasswordController.update.form()"
                        :options="{
                            preserveScroll: true,
                        }"
                        reset-on-success
                        :reset-on-error="[
                            'password',
                            'password_confirmation',
                            'current_password',
                        ]"
                        class="space-y-6"
                        v-slot="{ errors, processing, recentlySuccessful }"
                    >
                        <div class="grid gap-2">
                            <Label for="current_password" class="text-sm font-medium text-gray-700">Current Password</Label>
                            <Input
                                id="current_password"
                                name="current_password"
                                type="password"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-gray-900"
                                autocomplete="current-password"
                                placeholder="Enter your current password"
                            />
                            <InputError :message="errors.current_password" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="password" class="text-sm font-medium text-gray-700">New Password</Label>
                            <Input
                                id="password"
                                name="password"
                                type="password"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-gray-900"
                                autocomplete="new-password"
                                placeholder="Enter new password"
                            />
                            <InputError :message="errors.password" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="password_confirmation" class="text-sm font-medium text-gray-700">Confirm Password</Label>
                            <Input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-gray-900"
                                autocomplete="new-password"
                                placeholder="Confirm new password"
                            />
                            <InputError :message="errors.password_confirmation" />
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-sm text-blue-800">
                                <strong>Password requirements:</strong> Use at least 8 characters with a mix of letters, numbers, and symbols for better security.
                            </p>
                        </div>

                        <div class="flex items-center gap-4 pt-4 border-t">
                            <Button
                                :disabled="processing"
                                class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-lg font-medium transition-colors disabled:opacity-50"
                                data-test="update-password-button"
                            >
                                {{ processing ? 'Updating...' : 'Update Password' }}
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
                                    ✓ Password updated successfully
                                </p>
                            </Transition>
                        </div>
                    </Form>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>
