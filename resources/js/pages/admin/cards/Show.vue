<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { ArrowLeft, CreditCard, Freeze, Unlock, Lock, AlertTriangle, Settings, Activity, Globe, Smartphone, CheckCircle, XCircle } from 'lucide-vue-next';

interface Card {
    id: number;
    card_number: string;
    card_holder_name: string;
    card_type: string;
    status: string;
    daily_limit: number;
    currency: string;
    cvv: string;
    expiry_date: string;
    issue_date: string;
    contactless_enabled: boolean;
    online_transactions_enabled: boolean;
    international_transactions_enabled: boolean;
    account: {
        id: number;
        account_number: string;
        account_name: string;
        balance: number;
        available_balance: number;
        user: {
            id: number;
            name: string;
            email: string;
        };
    };
}

interface Transaction {
    id: number;
    transaction_type: string;
    category: string;
    description: string;
    amount: number;
    currency: string;
    transaction_date: string;
    status: string;
    merchant_name?: string;
}

interface Props {
    card: Card;
    recent_transactions: Transaction[];
}

const props = defineProps<Props>();

const showFreezeModal = ref(false);
const showUnfreezeModal = ref(false);
const showBlockModal = ref(false);
const showLostModal = ref(false);
const showLimitModal = ref(false);

const freezeForm = useForm({
    reason: '',
});

const blockForm = useForm({
    reason: '',
});

const lostForm = useForm({
    reason: '',
});

const limitForm = useForm({
    daily_limit: props.card.daily_limit,
});

const freeze = () => {
    freezeForm.post(`/admin/cards/${props.card.id}/freeze`, {
        preserveScroll: true,
        onSuccess: () => {
            showFreezeModal.value = false;
            freezeForm.reset();
        },
    });
};

const unfreeze = () => {
    router.post(`/admin/cards/${props.card.id}/unfreeze`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            showUnfreezeModal.value = false;
        },
    });
};

const block = () => {
    blockForm.post(`/admin/cards/${props.card.id}/block`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            showBlockModal.value = false;
            blockForm.reset();
        },
    });
};

const reportLost = () => {
    lostForm.post(`/admin/cards/${props.card.id}/report-lost`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            showLostModal.value = false;
            lostForm.reset();
        },
    });
};

const updateLimit = () => {
    limitForm.put(`/admin/cards/${props.card.id}/limits`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            showLimitModal.value = false;
        },
    });
};

const toggleFeature = (feature: string) => {
    router.post(`/admin/cards/${props.card.id}/toggle-feature`, { feature }, {
        preserveScroll: true,
    });
};

const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
        active: 'bg-green-100 text-green-800',
        frozen: 'bg-blue-100 text-blue-800',
        blocked: 'bg-red-100 text-red-800',
        lost: 'bg-gray-100 text-gray-800',
        expired: 'bg-yellow-100 text-yellow-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const maskCardNumber = (cardNumber: string) => {
    return cardNumber.replace(/\d(?=\d{4})/g, '*');
};
</script>

<template>
    <Head :title="`Card ${maskCardNumber(card.card_number)}`" />

    <AdminLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div>
                <Link href="/admin/cards" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4">
                    <ArrowLeft class="h-4 w-4 mr-1" />
                    Back to Cards
                </Link>
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Card Details</h1>
                        <p class="mt-1 text-sm text-gray-500">{{ maskCardNumber(card.card_number) }}</p>
                    </div>
                    <span :class="getStatusColor(card.status)" class="px-3 py-1 text-sm font-semibold rounded-full uppercase">
                        {{ card.status }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Card Info -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Card Visual -->
                    <div class="bg-gradient-to-br from-orange-500 to-orange-700 rounded-xl p-8 text-white shadow-xl">
                        <div class="flex justify-between items-start mb-12">
                            <CreditCard class="h-12 w-12" />
                            <span class="text-sm uppercase font-semibold">{{ card.card_type }}</span>
                        </div>
                        <div class="space-y-6">
                            <div class="text-2xl tracking-wider font-mono">
                                {{ card.card_number.match(/.{1,4}/g)?.join(' ') }}
                            </div>
                            <div class="flex justify-between items-end">
                                <div>
                                    <div class="text-xs opacity-75">CARD HOLDER</div>
                                    <div class="text-lg font-semibold uppercase">{{ card.card_holder_name }}</div>
                                </div>
                                <div>
                                    <div class="text-xs opacity-75">EXPIRES</div>
                                    <div class="text-lg font-semibold">{{ new Date(card.expiry_date).toLocaleDateString('en-US', { month: '2-digit', year: '2-digit' }) }}</div>
                                </div>
                                <div>
                                    <div class="text-xs opacity-75">CVV</div>
                                    <div class="text-lg font-semibold">{{ card.cvv }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Actions -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Card Actions</h2>
                        <div class="grid grid-cols-2 gap-4">
                            <button
                                v-if="card.status === 'active'"
                                @click="showFreezeModal = true"
                                class="inline-flex items-center justify-center px-4 py-2 border border-blue-300 rounded-md shadow-sm text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                <Freeze class="h-4 w-4 mr-2" />
                                Freeze Card
                            </button>

                            <button
                                v-if="card.status === 'frozen'"
                                @click="showUnfreezeModal = true"
                                class="inline-flex items-center justify-center px-4 py-2 border border-green-300 rounded-md shadow-sm text-sm font-medium text-green-700 bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                            >
                                <Unlock class="h-4 w-4 mr-2" />
                                Unfreeze Card
                            </button>

                            <button
                                v-if="!['blocked', 'lost'].includes(card.status)"
                                @click="showBlockModal = true"
                                class="inline-flex items-center justify-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                            >
                                <Lock class="h-4 w-4 mr-2" />
                                Block Card
                            </button>

                            <button
                                v-if="card.status !== 'lost'"
                                @click="showLostModal = true"
                                class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                            >
                                <AlertTriangle class="h-4 w-4 mr-2" />
                                Report Lost
                            </button>

                            <button
                                @click="showLimitModal = true"
                                class="inline-flex items-center justify-center px-4 py-2 border border-orange-300 rounded-md shadow-sm text-sm font-medium text-orange-700 bg-orange-50 hover:bg-orange-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
                            >
                                <Settings class="h-4 w-4 mr-2" />
                                Update Limits
                            </button>
                        </div>
                    </div>

                    <!-- Card Settings -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Card Settings</h2>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between py-3 border-b">
                                <div class="flex items-center">
                                    <Smartphone class="h-5 w-5 text-gray-400 mr-3" />
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">Contactless Payments</div>
                                        <div class="text-sm text-gray-500">Enable tap-to-pay functionality</div>
                                    </div>
                                </div>
                                <button
                                    @click="toggleFeature('contactless_enabled')"
                                    :class="card.contactless_enabled ? 'bg-orange-600' : 'bg-gray-200'"
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2"
                                >
                                    <span :class="card.contactless_enabled ? 'translate-x-5' : 'translate-x-0'" class="inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out" />
                                </button>
                            </div>

                            <div class="flex items-center justify-between py-3 border-b">
                                <div class="flex items-center">
                                    <Globe class="h-5 w-5 text-gray-400 mr-3" />
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">Online Transactions</div>
                                        <div class="text-sm text-gray-500">Allow online and e-commerce purchases</div>
                                    </div>
                                </div>
                                <button
                                    @click="toggleFeature('online_transactions_enabled')"
                                    :class="card.online_transactions_enabled ? 'bg-orange-600' : 'bg-gray-200'"
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2"
                                >
                                    <span :class="card.online_transactions_enabled ? 'translate-x-5' : 'translate-x-0'" class="inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out" />
                                </button>
                            </div>

                            <div class="flex items-center justify-between py-3">
                                <div class="flex items-center">
                                    <Activity class="h-5 w-5 text-gray-400 mr-3" />
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">International Transactions</div>
                                        <div class="text-sm text-gray-500">Allow transactions outside home country</div>
                                    </div>
                                </div>
                                <button
                                    @click="toggleFeature('international_transactions_enabled')"
                                    :class="card.international_transactions_enabled ? 'bg-orange-600' : 'bg-gray-200'"
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2"
                                >
                                    <span :class="card.international_transactions_enabled ? 'translate-x-5' : 'translate-x-0'" class="inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out" />
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Transactions -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Recent Transactions</h2>
                        <div class="space-y-4">
                            <div v-for="transaction in recent_transactions" :key="transaction.id" class="flex items-center justify-between py-3 border-b last:border-0">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ transaction.merchant_name || transaction.description }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ new Date(transaction.transaction_date).toLocaleString() }}
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div :class="transaction.transaction_type === 'credit' ? 'text-green-600' : 'text-gray-900'" class="text-sm font-semibold">
                                        {{ transaction.transaction_type === 'credit' ? '+' : '-' }}{{ transaction.currency }} {{ transaction.amount.toLocaleString() }}
                                    </div>
                                    <span :class="transaction.status === 'completed' ? 'text-green-600' : 'text-yellow-600'" class="text-xs">
                                        {{ transaction.status }}
                                    </span>
                                </div>
                            </div>
                            <div v-if="recent_transactions.length === 0" class="text-center text-sm text-gray-500 py-4">
                                No recent transactions
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Account Info -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Linked Account</h2>
                        <div class="space-y-3">
                            <div>
                                <div class="text-xs text-gray-500">Account Number</div>
                                <div class="text-sm font-medium text-gray-900">{{ card.account.account_number }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Account Name</div>
                                <div class="text-sm font-medium text-gray-900">{{ card.account.account_name }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Available Balance</div>
                                <div class="text-sm font-medium text-gray-900">{{ card.currency }} {{ card.account.available_balance.toLocaleString() }}</div>
                            </div>
                            <div class="pt-3 border-t">
                                <Link :href="`/admin/accounts/${card.account.id}`" class="text-sm text-orange-600 hover:text-orange-900">
                                    View Account →
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Card Holder Info -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Card Holder</h2>
                        <div class="space-y-3">
                            <div>
                                <div class="text-xs text-gray-500">Name</div>
                                <div class="text-sm font-medium text-gray-900">{{ card.account.user.name }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Email</div>
                                <div class="text-sm font-medium text-gray-900">{{ card.account.user.email }}</div>
                            </div>
                            <div class="pt-3 border-t">
                                <Link :href="`/admin/users/${card.account.user.id}`" class="text-sm text-orange-600 hover:text-orange-900">
                                    View User →
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Card Details -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Card Information</h2>
                        <div class="space-y-3">
                            <div>
                                <div class="text-xs text-gray-500">Daily Limit</div>
                                <div class="text-sm font-medium text-gray-900">{{ card.currency }} {{ card.daily_limit.toLocaleString() }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Issue Date</div>
                                <div class="text-sm font-medium text-gray-900">{{ new Date(card.issue_date).toLocaleDateString() }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Expiry Date</div>
                                <div class="text-sm font-medium text-gray-900">{{ new Date(card.expiry_date).toLocaleDateString() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Freeze Modal -->
        <div v-if="showFreezeModal" class="fixed z-10 inset-0 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showFreezeModal = false"></div>
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div>
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                            <Freeze class="h-6 w-6 text-blue-600" />
                        </div>
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Freeze Card</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    This will temporarily freeze the card. The cardholder won't be able to make any transactions until the card is unfrozen.
                                </p>
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 text-left mb-1">Reason (Optional)</label>
                                <textarea
                                    v-model="freezeForm.reason"
                                    rows="3"
                                    class="shadow-sm focus:ring-orange-500 focus:border-orange-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                    placeholder="Enter reason for freezing..."
                                />
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <button
                            @click="freeze"
                            :disabled="freezeForm.processing"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-2 sm:text-sm disabled:opacity-50"
                        >
                            Freeze Card
                        </button>
                        <button
                            @click="showFreezeModal = false"
                            type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:mt-0 sm:col-start-1 sm:text-sm"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Unfreeze Modal -->
        <div v-if="showUnfreezeModal" class="fixed z-10 inset-0 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showUnfreezeModal = false"></div>
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div>
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                            <Unlock class="h-6 w-6 text-green-600" />
                        </div>
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Unfreeze Card</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    This will unfreeze the card and restore normal functionality. The cardholder will be able to make transactions again.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <button
                            @click="unfreeze"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:col-start-2 sm:text-sm"
                        >
                            Unfreeze Card
                        </button>
                        <button
                            @click="showUnfreezeModal = false"
                            type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:mt-0 sm:col-start-1 sm:text-sm"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Block Modal -->
        <div v-if="showBlockModal" class="fixed z-10 inset-0 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showBlockModal = false"></div>
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div>
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                            <Lock class="h-6 w-6 text-red-600" />
                        </div>
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Block Card</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    This will permanently block the card. This action is typically used for security reasons or fraud prevention. A replacement card can be issued.
                                </p>
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 text-left mb-1">Reason *</label>
                                <textarea
                                    v-model="blockForm.reason"
                                    rows="3"
                                    class="shadow-sm focus:ring-orange-500 focus:border-orange-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                    placeholder="Enter reason for blocking..."
                                    required
                                />
                                <p v-if="blockForm.errors.reason" class="mt-1 text-sm text-red-600">{{ blockForm.errors.reason }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <button
                            @click="block"
                            :disabled="blockForm.processing || !blockForm.reason"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:col-start-2 sm:text-sm disabled:opacity-50"
                        >
                            Block Card
                        </button>
                        <button
                            @click="showBlockModal = false"
                            type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:mt-0 sm:col-start-1 sm:text-sm"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Lost Modal -->
        <div v-if="showLostModal" class="fixed z-10 inset-0 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showLostModal = false"></div>
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div>
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                            <AlertTriangle class="h-6 w-6 text-yellow-600" />
                        </div>
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Report Card as Lost</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    This will mark the card as lost and disable all transactions. A replacement card process will be initiated.
                                </p>
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 text-left mb-1">Additional Notes (Optional)</label>
                                <textarea
                                    v-model="lostForm.reason"
                                    rows="3"
                                    class="shadow-sm focus:ring-orange-500 focus:border-orange-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                    placeholder="Enter any additional information..."
                                />
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <button
                            @click="reportLost"
                            :disabled="lostForm.processing"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-yellow-600 text-base font-medium text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 sm:col-start-2 sm:text-sm disabled:opacity-50"
                        >
                            Report Lost
                        </button>
                        <button
                            @click="showLostModal = false"
                            type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:mt-0 sm:col-start-1 sm:text-sm"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Update Limit Modal -->
        <div v-if="showLimitModal" class="fixed z-10 inset-0 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showLimitModal = false"></div>
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div>
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-orange-100">
                            <Settings class="h-6 w-6 text-orange-600" />
                        </div>
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Update Daily Limit</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Set the maximum daily spending limit for this card.
                                </p>
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 text-left mb-1">Daily Limit ({{ card.currency }})</label>
                                <input
                                    v-model.number="limitForm.daily_limit"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="shadow-sm focus:ring-orange-500 focus:border-orange-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                    required
                                />
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <button
                            @click="updateLimit"
                            :disabled="limitForm.processing"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-orange-600 text-base font-medium text-white hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:col-start-2 sm:text-sm disabled:opacity-50"
                        >
                            Update Limit
                        </button>
                        <button
                            @click="showLimitModal = false"
                            type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:mt-0 sm:col-start-1 sm:text-sm"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
