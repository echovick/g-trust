import type { Currency } from '@/composables/useCurrency';

export const currencySymbols: Record<Currency, string> = {
    USD: '$',
    EUR: '€',
    GBP: '£',
};

export const currencyLocales: Record<Currency, string> = {
    USD: 'en-US',
    EUR: 'de-DE',
    GBP: 'en-GB',
};

/**
 * Format a number as currency
 */
export function formatCurrency(
    amount: number,
    currency: Currency = 'USD',
    options?: Intl.NumberFormatOptions
): string {
    const locale = currencyLocales[currency];

    return new Intl.NumberFormat(locale, {
        style: 'currency',
        currency,
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
        ...options,
    }).format(amount);
}

/**
 * Get currency symbol
 */
export function getCurrencySymbol(currency: Currency = 'USD'): string {
    return currencySymbols[currency];
}

/**
 * Convert amount from one currency to another (simple conversion using static rates)
 * In production, these rates would come from an API
 */
export function convertCurrency(
    amount: number,
    from: Currency,
    to: Currency
): number {
    // Exchange rates relative to USD
    const rates: Record<Currency, number> = {
        USD: 1,
        EUR: 0.92,
        GBP: 0.79,
    };

    // Convert to USD first, then to target currency
    const usdAmount = amount / rates[from];
    return usdAmount * rates[to];
}

/**
 * Format amount with currency symbol prefix
 */
export function formatWithSymbol(
    amount: number,
    currency: Currency = 'USD'
): string {
    const symbol = getCurrencySymbol(currency);
    const formatted = amount.toLocaleString(currencyLocales[currency], {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });

    return `${symbol}${formatted}`;
}
