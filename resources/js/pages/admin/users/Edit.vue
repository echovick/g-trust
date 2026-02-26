<script setup lang="ts">
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { ArrowLeft, Trash2, AlertCircle } from 'lucide-vue-next';

interface UserData {
    id: number;
    name: string;
    email: string;
    is_admin: boolean;
    preferred_currency: string;
}

interface Props {
    user: UserData;
}

const props = defineProps<Props>();

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    preferred_currency: props.user.preferred_currency,
    is_admin: props.user.is_admin,
});

const showDeleteModal = ref(false);
const deleteInProgress = ref(false);

const submitForm = () => {
    form.put(`/admin/users/${props.user.id}`, {
        preserveScroll: true,
    });
};

const deleteUser = () => {
    deleteInProgress.value = true;
    router.delete(`/admin/users/${props.user.id}`, {
        onFinish: () => {
            deleteInProgress.value = false;
            showDeleteModal.value = false;
        },
    });
};
</script>

<template>
    <AdminLayout :title="`Edit User: ${user.name}`">
        <div class="space-y-6">
            <div class="flex items-center gap-4">
                <a
                    :href="`/admin/users/${user.id}`"
                    class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
                >
                    <ArrowLeft :size="20" class="text-gray-600" />
                </a>
                <div class="flex-1">
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Edit User</h1>
                    <p class="text-gray-600 mt-1">{{ user.email }}</p>
                </div>
                <button
                    @click="showDeleteModal = true"
                    class="px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition-colors flex items-center gap-2"
                >
                    <Trash2 :size="18" />
                    <span class="hidden sm:inline">Delete User</span>
                </button>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 max-w-3xl">
                <form @submit.prevent="submitForm" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Full Name *
                            </label>
                            <input
                                v-model="form.name"
                                type="text"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-gray-900"
                                placeholder="John Doe"
                            />
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address *
                            </label>
                            <input
                                v-model="form.email"
                                type="email"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-gray-900"
                                placeholder="john@example.com"
                            />
                            <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                                {{ form.errors.email }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Preferred Currency *
                            </label>
                            <select
                                v-model="form.preferred_currency"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-gray-900"
                            >
                                <option value="USD">USD - US Dollar</option>
                                <option value="EUR">EUR - Euro</option>
                                <option value="GBP">GBP - British Pound</option>
                                <option value="NGN">NGN - Nigerian Naira</option>
                            </select>
                            <p v-if="form.errors.preferred_currency" class="mt-1 text-sm text-red-600">
                                {{ form.errors.preferred_currency }}
                            </p>
                        </div>

                        <div class="flex items-center">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input
                                    v-model="form.is_admin"
                                    type="checkbox"
                                    class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500"
                                />
                                <span class="text-sm font-medium text-gray-700">
                                    Administrator
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center gap-3 pt-4 border-t">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 font-medium"
                        >
                            {{ form.processing ? 'Saving Changes...' : 'Save Changes' }}
                        </button>
                        <a
                            :href="`/admin/users/${user.id}`"
                            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium"
                        >
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div
            v-if="showDeleteModal"
            class="fixed inset-0 backdrop-blur-sm bg-black/30 z-50 flex items-center justify-center p-4"
            @click="showDeleteModal = false"
        >
            <div
                class="bg-white rounded-lg shadow-xl max-w-md w-full p-6"
                @click.stop
            >
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <AlertCircle :size="24" class="text-red-600" />
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Delete User</h2>
                        <p class="text-sm text-gray-600">This action cannot be undone</p>
                    </div>
                </div>

                <div class="mb-6">
                    <p class="text-sm text-gray-700 mb-3">
                        Are you sure you want to delete this user?
                    </p>
                    <div class="bg-gray-50 rounded-lg p-3 text-sm">
                        <p class="font-medium text-gray-900">{{ user.name }}</p>
                        <p class="text-gray-600">{{ user.email }}</p>
                    </div>
                    <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <p class="text-xs text-yellow-800">
                            <strong>Warning:</strong> This will permanently delete the user and all related records including accounts, transactions, cards, transfers, beneficiaries, loans, investments, deposits, and bill payments.
                        </p>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button
                        @click="showDeleteModal = false"
                        :disabled="deleteInProgress"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                    >
                        Cancel
                    </button>
                    <button
                        @click="deleteUser"
                        :disabled="deleteInProgress"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50"
                    >
                        {{ deleteInProgress ? 'Deleting...' : 'Delete User' }}
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
