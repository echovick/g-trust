<script setup lang="ts">
import { ArrowDownToLine, Upload, FileText, Info } from 'lucide-vue-next';
import DashboardLayout from '@/layouts/DashboardLayout.vue';
</script>

<template>
    <DashboardLayout title="Deposits">
        <div class="mb-8">
            <p class="text-gray-600">Mobile check deposit and direct deposits</p>
        </div>

        <!-- Coming Soon State -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <div class="w-20 h-20 bg-purple-50 rounded-full flex items-center justify-center mx-auto mb-6 flex-shrink-0">
                <ArrowDownToLine :size="40" class="text-purple-500 flex-shrink-0" />
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-3">Mobile Check Deposit Coming Soon</h3>
            <p class="text-gray-600 mb-8 max-w-md mx-auto">
                We're working on bringing you the convenience of mobile check deposits.
                Soon you'll be able to deposit checks right from your phone!
            </p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-3xl mx-auto">
                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mx-auto mb-4">
                        <Upload :size="24" class="text-purple-500" />
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">Snap a Photo</h4>
                    <p class="text-sm text-gray-600">Take a picture of your check using your phone's camera</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mx-auto mb-4">
                        <FileText :size="24" class="text-purple-500" />
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">Enter Details</h4>
                    <p class="text-sm text-gray-600">Confirm the amount and select your account</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mx-auto mb-4">
                        <ArrowDownToLine :size="24" class="text-purple-500" />
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">Get Funds</h4>
                    <p class="text-sm text-gray-600">Funds typically available within 1-2 business days</p>
                </div>
            </div>

            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4 max-w-2xl mx-auto">
                <div class="flex items-start gap-3">
                    <Info :size="20" class="text-blue-600 mt-0.5 flex-shrink-0" />
                    <div class="text-left">
                        <p class="text-sm font-medium text-blue-900 mb-1">Set Up Direct Deposit</p>
                        <p class="text-sm text-blue-700">
                            Get your paycheck up to 2 days early with direct deposit. Contact your employer and
                            provide them with your account details to set up direct deposit.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>
