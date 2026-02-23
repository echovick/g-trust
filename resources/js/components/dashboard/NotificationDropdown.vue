<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { Bell, Check, X } from 'lucide-vue-next';
import axios from 'axios';

interface Notification {
    id: string;
    type: string;
    data: any;
    read_at: string | null;
    created_at: string;
}

const showDropdown = ref(false);
const notifications = ref<Notification[]>([]);
const unreadCount = ref(0);
const loading = ref(false);

const fetchNotifications = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/dashboard/notifications');
        notifications.value = response.data.notifications || [];
        unreadCount.value = response.data.unread_count || 0;
    } catch (error) {
        console.error('Failed to fetch notifications:', error);
        notifications.value = [];
        unreadCount.value = 0;
    } finally {
        loading.value = false;
    }
};

const markAsRead = async (notificationId: string) => {
    try {
        await axios.post(`/dashboard/notifications/${notificationId}/mark-as-read`);
        const notification = notifications.value.find(n => n.id === notificationId);
        if (notification) {
            notification.read_at = new Date().toISOString();
        }
        unreadCount.value = Math.max(0, unreadCount.value - 1);
    } catch (error) {
        console.error('Failed to mark notification as read:', error);
    }
};

const markAllAsRead = async () => {
    try {
        await axios.post('/dashboard/notifications/mark-all-as-read');
        if (notifications.value && Array.isArray(notifications.value)) {
            notifications.value.forEach(n => {
                if (!n.read_at) {
                    n.read_at = new Date().toISOString();
                }
            });
        }
        unreadCount.value = 0;
    } catch (error) {
        console.error('Failed to mark all notifications as read:', error);
    }
};

const toggleDropdown = () => {
    showDropdown.value = !showDropdown.value;
    if (showDropdown.value && notifications.value.length === 0) {
        fetchNotifications();
    }
};

const handleClickOutside = (event: MouseEvent) => {
    const target = event.target as HTMLElement;
    if (showDropdown.value && !target.closest('.notification-dropdown')) {
        showDropdown.value = false;
    }
};

const formatTime = (dateString: string) => {
    const date = new Date(dateString);
    const now = new Date();
    const diff = now.getTime() - date.getTime();
    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(diff / 3600000);
    const days = Math.floor(diff / 86400000);

    if (minutes < 1) return 'Just now';
    if (minutes < 60) return `${minutes}m ago`;
    if (hours < 24) return `${hours}h ago`;
    if (days < 7) return `${days}d ago`;
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
    <div class="notification-dropdown relative">
        <button
            @click="toggleDropdown"
            class="p-2 rounded-full hover:bg-gray-100 relative transition-colors"
            aria-label="Notifications"
        >
            <Bell :size="20" class="text-gray-600" />
            <span
                v-if="unreadCount > 0"
                class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"
            ></span>
        </button>

        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-if="showDropdown"
                class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50"
            >
                <!-- Header -->
                <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900">Notifications</h3>
                    <button
                        v-if="unreadCount > 0"
                        @click="markAllAsRead"
                        class="text-xs text-red-600 hover:text-red-700 font-medium"
                    >
                        Mark all as read
                    </button>
                </div>

                <!-- Notifications List -->
                <div class="max-h-96 overflow-y-auto">
                    <div v-if="loading" class="p-8 text-center text-gray-500">
                        Loading notifications...
                    </div>

                    <div v-else-if="notifications.length === 0" class="p-8 text-center text-gray-500">
                        <Bell :size="32" class="mx-auto mb-2 text-gray-400" />
                        <p class="text-sm">No notifications yet</p>
                    </div>

                    <div v-else>
                        <div
                            v-for="notification in notifications"
                            :key="notification.id"
                            :class="[
                                'px-4 py-3 border-b border-gray-100 hover:bg-gray-50 transition-colors cursor-pointer',
                                !notification.read_at ? 'bg-red-50' : ''
                            ]"
                            @click="markAsRead(notification.id)"
                        >
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-900 line-clamp-2">
                                        {{ notification.data.message || 'New notification' }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ formatTime(notification.created_at) }}
                                    </p>
                                </div>
                                <div v-if="!notification.read_at" class="flex-shrink-0">
                                    <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div v-if="notifications.length > 0" class="px-4 py-3 border-t border-gray-200 text-center">
                    <button class="text-sm text-red-600 hover:text-red-700 font-medium">
                        View all notifications
                    </button>
                </div>
            </div>
        </Transition>
    </div>
</template>
