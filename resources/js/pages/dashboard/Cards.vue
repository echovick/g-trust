<script setup lang="ts">
import { ref } from 'vue';
import { router, useForm, Link } from '@inertiajs/vue3';
import {
    CreditCard, Settings, Lock, Unlock, AlertTriangle, DollarSign,
    Globe, Wifi, ShoppingCart, CheckCircle, XCircle, Plus
} from 'lucide-vue-next';
import DashboardLayout from '@/layouts/DashboardLayout.vue';

interface Account {
    id: number;
    account_name: string;
    account_number: string;
}

interface Card {
    id: number;
    card_number: string;
    card_holder_name: string;
    card_type: 'debit' | 'credit';
    expiry_date: string;
    cvv?: string;
    status: 'active' | 'frozen' | 'blocked' | 'expired' | 'lost';
    daily_limit?: number;
    contactless_enabled: boolean;
    online_transactions_enabled: boolean;
    international_transactions_enabled: boolean;
    account: Account;
}

interface Props {
    cards: Card[];
}

const props = defineProps<Props>();

const selectedCard = ref<Card | null>(null);
const showSettingsModal = ref(false);

const settingsForm = useForm({
    daily_limit: 0,
    contactless_enabled: false,
    online_transactions_enabled: false,
    international_transactions_enabled: false,
});

const openSettings = (card: Card) => {
    selectedCard.value = card;
    settingsForm.daily_limit = card.daily_limit || 0;
    settingsForm.contactless_enabled = card.contactless_enabled;
    settingsForm.online_transactions_enabled = card.online_transactions_enabled;
    settingsForm.international_transactions_enabled = card.international_transactions_enabled;
    showSettingsModal.value = true;
};

const saveSettings = () => {
    if (!selectedCard.value) return;
    settingsForm.put(`/dashboard/cards/${selectedCard.value.id}/settings`, {
        onSuccess: () => {
            showSettingsModal.value = false;
        }
    });
};

const freezeCard = (cardId: number) => {
    if (confirm('Are you sure you want to freeze this card?')) {
        router.post(`/dashboard/cards/${cardId}/freeze`);
    }
};

const unfreezeCard = (cardId: number) => {
    router.post(`/dashboard/cards/${cardId}/unfreeze`);
};

const reportLost = (cardId: number) => {
    if (confirm('Are you sure you want to report this card as lost? A replacement card will be issued.')) {
        router.post(`/dashboard/cards/${cardId}/report-lost`);
    }
};

const maskCardNumber = (cardNumber: string) => {
    return cardNumber.replace(/(.{4})(.{4})(.{4})(.{4})/, '$1 •••• •••• $4');
};

const getStatusColor = (status: string) => {
    switch (status) {
        case 'active': return 'bg-green-50 text-green-700';
        case 'frozen': return 'bg-blue-50 text-blue-700';
        case 'lost': return 'bg-red-50 text-red-700';
        case 'expired': return 'bg-gray-50 text-gray-700';
        default: return 'bg-yellow-50 text-yellow-700';
    }
};

const getStatusIcon = (status: string) => {
    switch (status) {
        case 'active': return CheckCircle;
        case 'frozen': return Lock;
        case 'lost': return AlertTriangle;
        default: return XCircle;
    }
};
</script>

<template>
    <DashboardLayout title="My Cards">
        <div class="mb-8 flex items-center justify-between">
            <p class="text-gray-600">Manage your debit and credit cards</p>
            <Link
                href="/dashboard/card-requests/create"
                class="inline-flex items-center gap-2 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors"
            >
                <Plus :size="20" />
                <span class="hidden sm:inline">Request New Card</span>
                <span class="sm:hidden">Request</span>
            </Link>
        </div>

        <div v-if="cards.length > 0" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div v-for="card in cards" :key="card.id" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <!-- Card Visual -->
                <div class="relative bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-6 mb-6 text-white overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-red-500 opacity-20 rounded-full -mr-16 -mt-16"></div>
                    <div class="absolute bottom-0 left-0 w-24 h-24 bg-red-400 opacity-10 rounded-full -ml-12 -mb-12"></div>

                    <div class="relative z-10">
                        <div class="flex items-start justify-between mb-12">
                            <div>
                                <div class="text-xs text-gray-400 mb-1 uppercase">{{ card.card_type }}</div>
                                <div class="font-semibold">{{ card.account.account_name }}</div>
                            </div>
                            <CreditCard :size="32" class="text-red-400" />
                        </div>

                        <div class="font-mono text-lg tracking-wider mb-6">
                            {{ maskCardNumber(card.card_number) }}
                        </div>

                        <div class="flex items-end justify-between">
                            <div>
                                <div class="text-xs text-gray-400 mb-1">Card Holder</div>
                                <div class="font-medium">{{ card.card_holder_name }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-gray-400 mb-1">Expires</div>
                                <div class="font-medium">{{ card.expiry_date }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Info -->
                <div class="space-y-4">
                    <!-- Status -->
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Status</span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium"
                            :class="getStatusColor(card.status)"
                        >
                            <component :is="getStatusIcon(card.status)" :size="14" />
                            {{ card.status.charAt(0).toUpperCase() + card.status.slice(1) }}
                        </span>
                    </div>

                    <!-- Daily Limit -->
                    <div v-if="card.daily_limit" class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Daily Limit</span>
                        <span class="text-sm font-medium text-gray-900">
                            ${{ card.daily_limit.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
                        </span>
                    </div>

                    <!-- Features -->
                    <div class="border-t pt-4">
                        <div class="text-xs font-semibold text-gray-500 uppercase mb-3">Features</div>
                        <div class="grid grid-cols-3 gap-3">
                            <div class="flex flex-col items-center gap-1.5 p-2 rounded-lg"
                                :class="card.contactless_enabled ? 'bg-green-50' : 'bg-gray-50'"
                            >
                                <Wifi :size="16" :class="card.contactless_enabled ? 'text-green-600' : 'text-gray-400'" />
                                <span class="text-xs text-center" :class="card.contactless_enabled ? 'text-green-700 font-medium' : 'text-gray-500'">
                                    Contactless
                                </span>
                            </div>
                            <div class="flex flex-col items-center gap-1.5 p-2 rounded-lg"
                                :class="card.online_transactions_enabled ? 'bg-green-50' : 'bg-gray-50'"
                            >
                                <ShoppingCart :size="16" :class="card.online_transactions_enabled ? 'text-green-600' : 'text-gray-400'" />
                                <span class="text-xs text-center" :class="card.online_transactions_enabled ? 'text-green-700 font-medium' : 'text-gray-500'">
                                    Online
                                </span>
                            </div>
                            <div class="flex flex-col items-center gap-1.5 p-2 rounded-lg"
                                :class="card.international_transactions_enabled ? 'bg-green-50' : 'bg-gray-50'"
                            >
                                <Globe :size="16" :class="card.international_transactions_enabled ? 'text-green-600' : 'text-gray-400'" />
                                <span class="text-xs text-center" :class="card.international_transactions_enabled ? 'text-green-700 font-medium' : 'text-gray-500'">
                                    International
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="border-t pt-4 flex gap-2">
                        <button
                            @click="openSettings(card)"
                            class="flex-1 flex items-center justify-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                        >
                            <Settings :size="16" />
                            Settings
                        </button>

                        <button v-if="card.status === 'active'"
                            @click="freezeCard(card.id)"
                            class="flex-1 flex items-center justify-center gap-2 px-4 py-2 border border-blue-300 bg-blue-50 rounded-lg text-sm font-medium text-blue-700 hover:bg-blue-100 transition-colors"
                        >
                            <Lock :size="16" />
                            Freeze
                        </button>

                        <button v-else-if="card.status === 'frozen'"
                            @click="unfreezeCard(card.id)"
                            class="flex-1 flex items-center justify-center gap-2 px-4 py-2 border border-green-300 bg-green-50 rounded-lg text-sm font-medium text-green-700 hover:bg-green-100 transition-colors"
                        >
                            <Unlock :size="16" />
                            Unfreeze
                        </button>

                        <button v-if="card.status !== 'lost'"
                            @click="reportLost(card.id)"
                            class="flex-1 flex items-center justify-center gap-2 px-4 py-2 border border-red-300 bg-red-50 rounded-lg text-sm font-medium text-red-700 hover:bg-red-100 transition-colors"
                        >
                            <AlertTriangle :size="16" />
                            Report Lost
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <CreditCard :size="32" class="text-gray-400" />
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No cards yet</h3>
            <p class="text-gray-600 mb-6">Get started by requesting a debit or credit card</p>
            <Link
                href="/dashboard/card-requests/create"
                class="inline-flex items-center gap-2 px-6 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors"
            >
                <Plus :size="20" />
                Request New Card
            </Link>
        </div>

        <!-- Settings Modal -->
        <div v-if="showSettingsModal" class="fixed inset-0 backdrop-blur-sm bg-black/30 z-50 flex items-center justify-center p-4"
            @click.self="showSettingsModal = false"
        >
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Card Settings</h3>

                <form @submit.prevent="saveSettings" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Daily Spending Limit
                        </label>
                        <div class="relative">
                            <DollarSign :size="20" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                            <input
                                v-model="settingsForm.daily_limit"
                                type="number"
                                step="0.01"
                                min="0"
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-gray-900"
                                placeholder="0.00"
                            />
                        </div>
                        <div v-if="settingsForm.errors.daily_limit" class="mt-2 text-sm text-red-600">
                            {{ settingsForm.errors.daily_limit }}
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input
                                type="checkbox"
                                v-model="settingsForm.contactless_enabled"
                                class="mt-1 w-4 h-4 text-red-500 border-gray-300 rounded focus:ring-red-500"
                            />
                            <div>
                                <div class="font-medium text-gray-900">Contactless Payments</div>
                                <div class="text-sm text-gray-600">Allow tap-to-pay transactions</div>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 cursor-pointer">
                            <input
                                type="checkbox"
                                v-model="settingsForm.online_transactions_enabled"
                                class="mt-1 w-4 h-4 text-red-500 border-gray-300 rounded focus:ring-red-500"
                            />
                            <div>
                                <div class="font-medium text-gray-900">Online Transactions</div>
                                <div class="text-sm text-gray-600">Allow e-commerce purchases</div>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 cursor-pointer">
                            <input
                                type="checkbox"
                                v-model="settingsForm.international_transactions_enabled"
                                class="mt-1 w-4 h-4 text-red-500 border-gray-300 rounded focus:ring-red-500"
                            />
                            <div>
                                <div class="font-medium text-gray-900">International Transactions</div>
                                <div class="text-sm text-gray-600">Allow purchases outside your country</div>
                            </div>
                        </label>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button
                            type="button"
                            @click="showSettingsModal = false"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="settingsForm.processing"
                            class="flex-1 px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-medium transition-colors disabled:opacity-50"
                        >
                            {{ settingsForm.processing ? 'Saving...' : 'Save Changes' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </DashboardLayout>
</template>
