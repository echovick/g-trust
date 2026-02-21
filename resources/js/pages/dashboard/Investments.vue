<script setup lang="ts">
import { PieChart, TrendingUp, Shield, DollarSign, Target, Sparkles } from 'lucide-vue-next';
import DashboardLayout from '@/layouts/DashboardLayout.vue';
</script>

<template>
    <DashboardLayout title="Investments">
        <div class="mb-8">
            <p class="text-gray-600">Grow your wealth with smart investment options</p>
        </div>

        <!-- Coming Soon State -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center mx-auto mb-6 flex-shrink-0">
                <PieChart :size="40" class="text-white flex-shrink-0" />
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-3">Investment Services Coming Soon</h3>
            <p class="text-gray-600 mb-8 max-w-md mx-auto">
                We're building a comprehensive investment platform to help you grow your wealth.
                Stay tuned for exciting investment opportunities!
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-4xl mx-auto">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-6 text-left">
                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mb-4">
                        <TrendingUp :size="24" class="text-blue-600" />
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">Stocks & ETFs</h4>
                    <p class="text-sm text-gray-600">
                        Invest in individual stocks and exchange-traded funds with competitive fees
                    </p>
                </div>

                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-6 text-left">
                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mb-4">
                        <Shield :size="24" class="text-purple-600" />
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">Bonds & Fixed Income</h4>
                    <p class="text-sm text-gray-600">
                        Secure your portfolio with stable, low-risk bond investments
                    </p>
                </div>

                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-6 text-left">
                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mb-4">
                        <DollarSign :size="24" class="text-green-600" />
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">Mutual Funds</h4>
                    <p class="text-sm text-gray-600">
                        Diversify your investments with professionally managed mutual funds
                    </p>
                </div>

                <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg p-6 text-left">
                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mb-4">
                        <Target :size="24" class="text-orange-600" />
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">Retirement Accounts</h4>
                    <p class="text-sm text-gray-600">
                        Plan for your future with IRA and 401(k) investment options
                    </p>
                </div>

                <div class="bg-gradient-to-br from-pink-50 to-pink-100 rounded-lg p-6 text-left">
                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mb-4">
                        <Sparkles :size="24" class="text-pink-600" />
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">Robo-Advisor</h4>
                    <p class="text-sm text-gray-600">
                        Let AI manage your portfolio based on your risk tolerance and goals
                    </p>
                </div>

                <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg p-6 text-left">
                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mb-4">
                        <PieChart :size="24" class="text-indigo-600" />
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">Diversified Portfolios</h4>
                    <p class="text-sm text-gray-600">
                        Choose from pre-built portfolios tailored to different investment strategies
                    </p>
                </div>
            </div>

            <div class="mt-10 bg-green-50 border border-green-200 rounded-xl p-6 max-w-2xl mx-auto">
                <h4 class="font-semibold text-green-900 mb-3 text-lg">Why Invest with Us?</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div class="text-left">
                        <div class="font-medium text-green-900 mb-1">Low Fees</div>
                        <div class="text-green-700">Competitive pricing on all investment products</div>
                    </div>
                    <div class="text-left">
                        <div class="font-medium text-green-900 mb-1">Expert Guidance</div>
                        <div class="text-green-700">Professional advisors to help you succeed</div>
                    </div>
                    <div class="text-left">
                        <div class="font-medium text-green-900 mb-1">Easy Management</div>
                        <div class="text-green-700">Track and manage all investments in one place</div>
                    </div>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>
