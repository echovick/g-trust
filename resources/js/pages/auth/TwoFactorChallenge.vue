<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    InputOTP,
    InputOTPGroup,
    InputOTPSlot,
} from '@/components/ui/input-otp';
import BankAuthLayout from '@/layouts/BankAuthLayout.vue';
import type { TwoFactorConfigContent } from '@/types';
import { store } from '@/routes/two-factor/login';
import { ShieldCheck, Key } from 'lucide-vue-next';

const authConfigContent = computed<TwoFactorConfigContent>(() => {
    if (showRecoveryInput.value) {
        return {
            title: 'Recovery Code',
            description:
                'Please confirm access to your account by entering one of your emergency recovery codes.',
            buttonText: 'login using an authentication code',
        };
    }

    return {
        title: 'Two-Factor Authentication',
        description:
            'Enter the authentication code provided by your authenticator application.',
        buttonText: 'login using a recovery code',
    };
});

const showRecoveryInput = ref<boolean>(false);

const toggleRecoveryMode = (clearErrors: () => void): void => {
    showRecoveryInput.value = !showRecoveryInput.value;
    clearErrors();
    code.value = '';
};

const code = ref<string>('');
</script>

<template>
    <BankAuthLayout
        :title="authConfigContent.title"
        :description="authConfigContent.description"
    >
        <Head title="Two-Factor Authentication" />

        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <component :is="showRecoveryInput ? Key : ShieldCheck" :size="32" class="text-red-500" />
            </div>
        </div>

        <div class="space-y-6">
            <template v-if="!showRecoveryInput">
                <Form
                    v-bind="store.form()"
                    class="space-y-6"
                    reset-on-error
                    @error="code = ''"
                    #default="{ errors, processing, clearErrors }"
                >
                    <input type="hidden" name="code" :value="code" />
                    <div
                        class="flex flex-col items-center justify-center space-y-4 text-center"
                    >
                        <div class="flex w-full items-center justify-center">
                            <InputOTP
                                id="otp"
                                v-model="code"
                                :maxlength="6"
                                :disabled="processing"
                                autofocus
                            >
                                <InputOTPGroup>
                                    <InputOTPSlot
                                        v-for="index in 6"
                                        :key="index"
                                        :index="index - 1"
                                        class="h-14 w-14 text-lg border-gray-300"
                                    />
                                </InputOTPGroup>
                            </InputOTP>
                        </div>
                        <InputError :message="errors.code" />
                    </div>
                    <Button
                        type="submit"
                        class="w-full h-12 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-full transition-colors"
                        :disabled="processing"
                    >
                        Continue
                    </Button>
                    <div class="text-center text-sm text-gray-600 pt-4 border-t">
                        <span>Having trouble? </span>
                        <button
                            type="button"
                            class="text-red-500 hover:text-red-600 font-medium"
                            @click="() => toggleRecoveryMode(clearErrors)"
                        >
                            {{ authConfigContent.buttonText }}
                        </button>
                    </div>
                </Form>
            </template>

            <template v-else>
                <Form
                    v-bind="store.form()"
                    class="space-y-6"
                    reset-on-error
                    #default="{ errors, processing, clearErrors }"
                >
                    <div>
                        <Input
                            name="recovery_code"
                            type="text"
                            placeholder="Enter recovery code"
                            :autofocus="showRecoveryInput"
                            required
                            class="h-12 border-gray-300 focus:border-red-500 focus:ring-red-500 text-center font-mono"
                        />
                        <InputError :message="errors.recovery_code" class="mt-1" />
                    </div>
                    <Button
                        type="submit"
                        class="w-full h-12 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-full transition-colors"
                        :disabled="processing"
                    >
                        Continue
                    </Button>

                    <div class="text-center text-sm text-gray-600 pt-4 border-t">
                        <span>Having trouble? </span>
                        <button
                            type="button"
                            class="text-red-500 hover:text-red-600 font-medium"
                            @click="() => toggleRecoveryMode(clearErrors)"
                        >
                            {{ authConfigContent.buttonText }}
                        </button>
                    </div>
                </Form>
            </template>
        </div>
    </BankAuthLayout>
</template>
