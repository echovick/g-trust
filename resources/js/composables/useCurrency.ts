import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';

export type Currency = 'USD' | 'EUR' | 'GBP';

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
};

// Exchange rates (in production, these would come from an API)
export const exchangeRates: Record<Currency, Record<Currency, number>> = {
    USD: { USD: 1, EUR: 0.92, GBP: 0.79 },
    EUR: { USD: 1.09, EUR: 1, GBP: 0.86 },
    GBP: { USD: 1.27, EUR: 1.16, GBP: 1 },
};

// Global state for current currency
const currentCurrency = ref<Currency>('USD');

export function useCurrency() {
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
