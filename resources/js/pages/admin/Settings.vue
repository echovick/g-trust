<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { User, Lock, Bell, Shield, Palette, LogOut } from 'lucide-vue-next';

const settingsSections = [
    {
        title: 'Profile Settings',
        description: 'Update your personal information and email',
        icon: User,
        href: '/settings/profile',
        color: 'text-blue-600 bg-blue-50',
    },
    {
        title: 'Password & Security',
        description: 'Change your password and manage security settings',
        icon: Lock,
        href: '/settings/password',
        color: 'text-green-600 bg-green-50',
    },
    {
        title: 'Two-Factor Authentication',
        description: 'Add an extra layer of security to your account',
        icon: Shield,
        href: '/settings/two-factor',
        color: 'text-purple-600 bg-purple-50',
    },
    {
        title: 'Appearance',
        description: 'Customize your dashboard theme and preferences',
        icon: Palette,
        href: '/settings/appearance',
        color: 'text-orange-600 bg-orange-50',
    },
];
</script>

<template>
    <AdminLayout title="Admin Settings">
        <div class="space-y-6">
            <!-- Header -->
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Settings</h1>
                <p class="text-gray-600 mt-1">Manage your account settings and preferences</p>
            </div>

            <!-- Settings Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <Link
                    v-for="section in settingsSections"
                    :key="section.title"
                    :href="section.href"
                    class="block bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md hover:border-red-300 transition-all group"
                >
                    <div class="flex items-start gap-4">
                        <div :class="['w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0', section.color]">
                            <component :is="section.icon" :size="24" />
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900 mb-1 group-hover:text-red-600 transition-colors">
                                {{ section.title }}
                            </h3>
                            <p class="text-sm text-gray-600">{{ section.description }}</p>
                        </div>
                    </div>
                </Link>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
                <div class="space-y-3">
                    <Link
                        href="/dashboard"
                        class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors group"
                    >
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                                <User :size="20" class="text-blue-600" />
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 group-hover:text-red-600 transition-colors">
                                    Switch to User Dashboard
                                </p>
                                <p class="text-sm text-gray-600">View your personal banking dashboard</p>
                            </div>
                        </div>
                    </Link>

                    <Link
                        href="/logout"
                        method="post"
                        as="button"
                        class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-red-50 transition-colors group"
                    >
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-red-50 rounded-lg flex items-center justify-center">
                                <LogOut :size="20" class="text-red-600" />
                            </div>
                            <div class="text-left">
                                <p class="font-medium text-gray-900 group-hover:text-red-600 transition-colors">
                                    Sign Out
                                </p>
                                <p class="text-sm text-gray-600">Log out of your account</p>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
