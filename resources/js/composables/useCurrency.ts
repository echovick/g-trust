import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

export type Currency = 'USD' | 'EUR' | 'GBP' | 'NGN';

export interface CurrencyConfig {
    code: Currency;
    symbol: string;
    name: string;
    locale: string;
}

export const currencies: Record<Currency, CurrencyConfig> = {
    USD: {
        code: 'USD',
        symbol: '$',
        name: 'US Dollar',
        locale: 'en-US',
    },
    EUR: {
        code: 'EUR',
        symbol: '€',
        name: 'Euro',
        locale: 'de-DE',
    },
    GBP: {
        code: 'GBP',
        symbol: '£',
        name: 'British Pound',
        locale: 'en-GB',
    },
    NGN: {
        code: 'NGN',
        symbol: '₦',
        name: 'Nigerian Naira',
        locale: 'en-NG',
    },
};

// Exchange rates (in production, these would come from an API)
export const exchangeRates: Record<Currency, Record<Currency, number>> = {
    USD: { USD: 1, EUR: 0.92, GBP: 0.79, NGN: 1550 },
    EUR: { USD: 1.09, EUR: 1, GBP: 0.86, NGN: 1689 },
    GBP: { USD: 1.27, EUR: 1.16, GBP: 1, NGN: 1962 },
    NGN: { USD: 0.00065, EUR: 0.00059, GBP: 0.00051, NGN: 1 },
};

// Get the user's preferred currency from Inertia shared auth data
function getInitialCurrency(): Currency {
    try {
        const page = usePage();
        const preferredCurrency = (page.props.auth as any)?.user?.preferred_currency;
        if (preferredCurrency && preferredCurrency in currencies) {
            return preferredCurrency as Currency;
        }
    } catch {
        // usePage() may fail outside of Vue setup context; fall back to default
    }
    return 'USD';
}

// Global state for current currency - initialized from user's preferred currency
const currentCurrency = ref<Currency>(getInitialCurrency());
let initialized = false;

export function useCurrency() {
    // Ensure we initialize from user's preferred currency on first composable use
    if (!initialized) {
        try {
            const page = usePage();
            const preferredCurrency = (page.props.auth as any)?.user?.preferred_currency;
            if (preferredCurrency && preferredCurrency in currencies) {
                currentCurrency.value = preferredCurrency as Currency;
            }
        } catch {
            // usePage() may fail outside of Vue setup context
        }
        initialized = true;
    }

    const setCurrency = (currency: Currency) => {
        currentCurrency.value = currency;

        // Save to backend (only if we're in the dashboard)
        if (window.location.pathname.startsWith('/dashboard')) {
            router.post('/dashboard/currency',
                { currency },
                {
                    preserveState: true,
                    preserveScroll: true,
                    preserveUrl: true,
                }
            );
        }
    };

    const currencyConfig = computed(() => currencies[currentCurrency.value]);

    const convertAmount = (
        amount: number,
        fromCurrency: Currency,
        toCurrency?: Currency
    ): number => {
        const target = toCurrency || currentCurrency.value;
        if (fromCurrency === target) return amount;

        const rate = exchangeRates[fromCurrency][target];
        return amount * rate;
    };

    const formatCurrency = (
        amount: number,
        currency?: Currency,
        showCode: boolean = false
    ): string => {
        const curr = currency || currentCurrency.value;
        const config = currencies[curr];

        const formatted = new Intl.NumberFormat(config.locale, {
            style: 'currency',
            currency: curr,
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }).format(amount);

        return showCode ? `${formatted} ${curr}` : formatted;
    };

    const getSymbol = (currency?: Currency): string => {
        const curr = currency || currentCurrency.value;
        return currencies[curr].symbol;
    };

    return {
        currentCurrency: computed(() => currentCurrency.value),
        currencyConfig,
        currencies,
        setCurrency,
        convertAmount,
        formatCurrency,
        getSymbol,
    };
}
