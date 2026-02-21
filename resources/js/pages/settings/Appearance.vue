<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { User, Shield, Eye, Settings as SettingsIcon, Palette } from 'lucide-vue-next';
import AppearanceTabs from '@/components/AppearanceTabs.vue';
import DashboardLayout from '@/layouts/DashboardLayout.vue';

const settingsNav = [
    { name: 'Profile', href: '/settings/profile', icon: User },
    { name: 'Password', href: '/settings/password', icon: Shield },
    { name: 'Two-Factor Auth', href: '/settings/two-factor', icon: Eye },
    { name: 'Appearance', href: '/settings/appearance', icon: SettingsIcon, current: true },
];
</script>

<template>
    <DashboardLayout title="Appearance Settings">
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
                            <Palette :size="24" class="text-red-500" />
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Appearance Settings</h3>
                            <p class="text-sm text-gray-600">Customize the look and feel of your dashboard</p>
                        </div>
                    </div>

                    <AppearanceTabs />
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>
