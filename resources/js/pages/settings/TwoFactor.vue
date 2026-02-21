<script setup lang="ts">
import { Form, Link } from '@inertiajs/vue3';
import { User, Shield, Eye, Settings as SettingsIcon, ShieldBan, ShieldCheck } from 'lucide-vue-next';
import { onUnmounted, ref } from 'vue';
import TwoFactorRecoveryCodes from '@/components/TwoFactorRecoveryCodes.vue';
import TwoFactorSetupModal from '@/components/TwoFactorSetupModal.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { useTwoFactorAuth } from '@/composables/useTwoFactorAuth';
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { disable, enable } from '@/routes/two-factor';

type Props = {
    requiresConfirmation?: boolean;
    twoFactorEnabled?: boolean;
};

withDefaults(defineProps<Props>(), {
    requiresConfirmation: false,
    twoFactorEnabled: false,
});

const { hasSetupData, clearTwoFactorAuthData } = useTwoFactorAuth();
const showSetupModal = ref<boolean>(false);

const settingsNav = [
    { name: 'Profile', href: '/settings/profile', icon: User },
    { name: 'Password', href: '/settings/password', icon: Shield },
    { name: 'Two-Factor Auth', href: '/settings/two-factor', icon: Eye, current: true },
    { name: 'Appearance', href: '/settings/appearance', icon: SettingsIcon },
];

onUnmounted(() => {
    clearTwoFactorAuthData();
});
</script>

<template>
    <DashboardLayout title="Two-Factor Authentication">
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
                            <Eye :size="24" class="text-red-500" />
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Two-Factor Authentication</h3>
                            <p class="text-sm text-gray-600">Add an extra layer of security to your account</p>
                        </div>
                    </div>

                    <div v-if="!twoFactorEnabled" class="space-y-6">
                        <Badge variant="destructive" class="inline-flex">Disabled</Badge>

                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-sm text-blue-800">
                                When you enable two-factor authentication, you will be
                                prompted for a secure pin during login. This pin can be
                                retrieved from a TOTP-supported application on your
                                phone such as Google Authenticator or Authy.
                            </p>
                        </div>

                        <div class="pt-4 border-t">
                            <Button
                                v-if="hasSetupData"
                                @click="showSetupModal = true"
                                class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-lg font-medium transition-colors"
                            >
                                <ShieldCheck :size="20" />
                                Continue Setup
                            </Button>
                            <Form
                                v-else
                                v-bind="enable.form()"
                                @success="showSetupModal = true"
                                #default="{ processing }"
                            >
                                <Button
                                    type="submit"
                                    :disabled="processing"
                                    class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-lg font-medium transition-colors disabled:opacity-50"
                                >
                                    <ShieldCheck :size="20" />
                                    {{ processing ? 'Enabling...' : 'Enable 2FA' }}
                                </Button>
                            </Form>
                        </div>
                    </div>

                    <div v-else class="space-y-6">
                        <Badge variant="default" class="inline-flex bg-green-100 text-green-700">Enabled</Badge>

                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <p class="text-sm text-green-800">
                                Two-factor authentication is active on your account. You will be
                                prompted for a secure, random pin during login, which
                                you can retrieve from the TOTP-supported application on
                                your phone.
                            </p>
                        </div>

                        <TwoFactorRecoveryCodes />

                        <div class="pt-4 border-t">
                            <Form v-bind="disable.form()" #default="{ processing }">
                                <Button
                                    variant="destructive"
                                    type="submit"
                                    :disabled="processing"
                                    class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-lg font-medium transition-colors disabled:opacity-50"
                                >
                                    <ShieldBan :size="20" />
                                    {{ processing ? 'Disabling...' : 'Disable 2FA' }}
                                </Button>
                            </Form>
                        </div>
                    </div>

                    <TwoFactorSetupModal
                        v-model:isOpen="showSetupModal"
                        :requiresConfirmation="requiresConfirmation"
                        :twoFactorEnabled="twoFactorEnabled"
                    />
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>
