<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { UserPlus, Globe, MapPin, Edit, Trash2, CheckCircle, XCircle } from 'lucide-vue-next';
import DashboardLayout from '@/layouts/DashboardLayout.vue';

interface Beneficiary {
    id: number;
    name: string;
    nickname?: string;
    account_number: string;
    routing_number?: string;
    bank_name: string;
    bank_address?: string;
    swift_code?: string;
    iban?: string;
    country: string;
    beneficiary_type: 'domestic' | 'international';
    email?: string;
    phone?: string;
    is_verified: boolean;
    created_at: string;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedBeneficiaries {
    data: Beneficiary[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: PaginationLink[];
}

interface Props {
    beneficiaries: PaginatedBeneficiaries;
}

const props = defineProps<Props>();

const deleteBeneficiary = (id: number) => {
    if (confirm('Are you sure you want to delete this beneficiary?')) {
        router.delete(`/dashboard/beneficiaries/${id}`);
    }
};
</script>

<template>
    <DashboardLayout title="Beneficiaries">
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600">Manage your saved beneficiaries for transfers</p>
                </div>
                <Link
                    href="/dashboard/beneficiaries/create"
                    class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center gap-2"
                >
                    <UserPlus :size="20" />
                    Add Beneficiary
                </Link>
            </div>
        </div>

        <div v-if="beneficiaries.data.length > 0" class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Beneficiary
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Bank Details
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Type
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="beneficiary in beneficiaries.data" :key="beneficiary.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0">
                                        <Globe v-if="beneficiary.beneficiary_type === 'international'" :size="20" class="text-blue-500" />
                                        <MapPin v-else :size="20" class="text-green-500" />
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ beneficiary.name }}</div>
                                        <div v-if="beneficiary.nickname" class="text-sm text-gray-500">{{ beneficiary.nickname }}</div>
                                        <div class="text-sm text-gray-500 mt-1">{{ beneficiary.country }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <div class="font-medium text-gray-900">{{ beneficiary.bank_name }}</div>
                                    <div class="text-gray-500 mt-1">Acc: {{ beneficiary.account_number }}</div>
                                    <div v-if="beneficiary.routing_number" class="text-gray-500">
                                        Routing: {{ beneficiary.routing_number }}
                                    </div>
                                    <div v-if="beneficiary.swift_code" class="text-gray-500">
                                        SWIFT: {{ beneficiary.swift_code }}
                                    </div>
                                    <div v-if="beneficiary.iban" class="text-gray-500">
                                        IBAN: {{ beneficiary.iban }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium"
                                    :class="beneficiary.beneficiary_type === 'international'
                                        ? 'bg-blue-50 text-blue-700'
                                        : 'bg-green-50 text-green-700'"
                                >
                                    <Globe v-if="beneficiary.beneficiary_type === 'international'" :size="14" />
                                    <MapPin v-else :size="14" />
                                    {{ beneficiary.beneficiary_type === 'international' ? 'International' : 'Domestic' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium"
                                    :class="beneficiary.is_verified
                                        ? 'bg-green-50 text-green-700'
                                        : 'bg-yellow-50 text-yellow-700'"
                                >
                                    <CheckCircle v-if="beneficiary.is_verified" :size="14" />
                                    <XCircle v-else :size="14" />
                                    {{ beneficiary.is_verified ? 'Verified' : 'Pending Verification' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <Link
                                        :href="`/dashboard/beneficiaries/${beneficiary.id}/edit`"
                                        class="p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                        title="Edit"
                                    >
                                        <Edit :size="18" />
                                    </Link>
                                    <button
                                        @click="deleteBeneficiary(beneficiary.id)"
                                        class="p-2 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                        title="Delete"
                                    >
                                        <Trash2 :size="18" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="beneficiaries.last_page > 1" class="px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        Showing {{ ((beneficiaries.current_page - 1) * beneficiaries.per_page) + 1 }}
                        to {{ Math.min(beneficiaries.current_page * beneficiaries.per_page, beneficiaries.total) }}
                        of {{ beneficiaries.total }} beneficiaries
                    </div>
                    <div class="flex gap-2">
                        <Link
                            v-for="link in beneficiaries.links"
                            :key="link.label"
                            :href="link.url || '#'"
                            :class="[
                                'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                                link.active
                                    ? 'bg-red-500 text-white'
                                    : link.url
                                        ? 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                        : 'bg-gray-50 text-gray-400 cursor-not-allowed'
                            ]"
                            v-html="link.label"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <UserPlus :size="32" class="text-gray-400" />
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No beneficiaries yet</h3>
            <p class="text-gray-600 mb-6">Add beneficiaries to make transfers faster and easier</p>
            <Link
                href="/dashboard/beneficiaries/create"
                class="inline-flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg font-medium transition-colors"
            >
                <UserPlus :size="20" />
                Add Your First Beneficiary
            </Link>
        </div>
    </DashboardLayout>
</template>
