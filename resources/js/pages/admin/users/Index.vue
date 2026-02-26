<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Search, Plus, User, Wallet, Pencil, Trash2 } from 'lucide-vue-next';

interface Account {
    id: number;
    account_number: string;
    balance: number;
    currency: string;
}

interface User {
    id: number;
    name: string;
    email: string;
    is_admin: boolean;
    created_at: string;
    accounts: Account[];
}

interface Props {
    users: {
        data: User[];
        links: any[];
        meta: {
            current_page: number;
            last_page: number;
            per_page: number;
            total: number;
        };
    };
    filters: {
        search?: string;
    };
}

const props = defineProps<Props>();

const searchForm = useForm({
    search: props.filters.search || '',
});

const searchUsers = () => {
    router.get(
        '/admin/users',
        { search: searchForm.search },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
};

const userToDelete = ref<User | null>(null);
const deleteInProgress = ref(false);

const confirmDelete = (user: User) => {
    userToDelete.value = user;
};

const deleteUser = () => {
    if (!userToDelete.value) return;
    deleteInProgress.value = true;
    router.delete(`/admin/users/${userToDelete.value.id}`, {
        onFinish: () => {
            deleteInProgress.value = false;
            userToDelete.value = null;
        },
    });
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });
};
</script>

<template>
    <AdminLayout title="User Management">
        <div class="space-y-4 sm:space-y-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Users</h1>
                    <p class="text-gray-600 mt-1">Manage all user accounts</p>
                </div>
                <Link
                    href="/admin/users/create"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                >
                    <Plus :size="20" />
                    <span class="hidden sm:inline">Create User</span>
                    <span class="sm:hidden">New User</span>
                </Link>
            </div>

            <!-- Search Bar -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <form @submit.prevent="searchUsers" class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1 relative">
                        <Search
                            :size="20"
                            class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"
                        />
                        <input
                            v-model="searchForm.search"
                            type="text"
                            placeholder="Search by name or email..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-gray-900"
                        />
                    </div>
                    <button
                        type="submit"
                        class="px-6 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition-colors whitespace-nowrap"
                    >
                        Search
                    </button>
                </form>
            </div>

            <!-- Users Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    User
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Email
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Accounts
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Role
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Joined
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="user in users?.data || []" :key="user.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
                                            <User :size="20" class="text-gray-500" />
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ user.email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2 text-sm text-gray-900">
                                        <Wallet :size="16" class="text-gray-400" />
                                        {{ user.accounts.length }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="[
                                            'px-2 py-1 text-xs rounded-full',
                                            user.is_admin
                                                ? 'bg-purple-100 text-purple-800'
                                                : 'bg-gray-100 text-gray-800',
                                        ]"
                                    >
                                        {{ user.is_admin ? 'Admin' : 'User' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ formatDate(user.created_at) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-3">
                                        <Link
                                            :href="`/admin/users/${user.id}`"
                                            class="text-red-600 hover:text-red-900"
                                        >
                                            View
                                        </Link>
                                        <Link
                                            :href="`/admin/users/${user.id}/edit`"
                                            class="text-gray-400 hover:text-gray-600 transition-colors"
                                            title="Edit user"
                                        >
                                            <Pencil :size="16" />
                                        </Link>
                                        <button
                                            @click="confirmDelete(user)"
                                            class="text-gray-400 hover:text-red-600 transition-colors"
                                            title="Delete user"
                                        >
                                            <Trash2 :size="16" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="users?.meta?.last_page > 1" class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing
                            <span class="font-medium">{{ ((users?.meta?.current_page || 1) - 1) * (users?.meta?.per_page || 20) + 1 }}</span>
                            to
                            <span class="font-medium">
                                {{ Math.min((users?.meta?.current_page || 1) * (users?.meta?.per_page || 20), users?.meta?.total || 0) }}
                            </span>
                            of
                            <span class="font-medium">{{ users?.meta?.total || 0 }}</span>
                            results
                        </div>
                        <div class="flex gap-2">
                            <Link
                                v-for="link in users?.links || []"
                                :key="link.label"
                                :href="link.url || '#'"
                                :class="[
                                    'px-3 py-1 rounded border',
                                    link.active
                                        ? 'bg-red-600 text-white border-red-600'
                                        : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50',
                                    !link.url ? 'opacity-50 cursor-not-allowed' : '',
                                ]"
                                v-html="link.label"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div
            v-if="userToDelete"
            class="fixed inset-0 backdrop-blur-sm bg-black/30 z-50 flex items-center justify-center p-4"
            @click="userToDelete = null"
        >
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6" @click.stop>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <Trash2 :size="20" class="text-red-600" />
                    </div>
                    <h2 class="text-lg font-semibold text-gray-900">Delete User</h2>
                </div>
                <p class="text-sm text-gray-600 mb-2">
                    Are you sure you want to delete <span class="font-medium text-gray-900">{{ userToDelete.name }}</span>?
                </p>
                <p class="text-sm text-gray-500 mb-4">{{ userToDelete.email }}</p>
                <div class="text-sm text-amber-800 bg-amber-50 border border-amber-200 rounded p-3 mb-6 space-y-1">
                    <p><strong>This action cannot be undone.</strong></p>
                    <p>All related records including accounts, transactions, cards, transfers, beneficiaries, loans, investments, deposits, and bill payments will be permanently deleted.</p>
                </div>
                <div class="flex justify-end gap-3">
                    <button
                        @click="userToDelete = null"
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
