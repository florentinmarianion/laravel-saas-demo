import { ref, computed } from 'vue';
import axios from 'axios';

export function useCurrency() {
    const rates    = ref({});
    const loading  = ref(false);
    const error    = ref(null);
    const rateDate = ref('');

    const mainCurrencies = ['EUR', 'USD', 'GBP', 'CHF', 'CAD', 'AUD', 'RON'];
    const allCurrencies  = computed(() => Object.keys(rates.value));
    const baseUrl        = document.querySelector('meta[name="base-url"]')?.content || '';

    // ─── In-memory cache — evită request-uri duplicate pentru aceeași dată ────
    const historicalCache = new Map();

    async function fetchRates(date = null) {
        loading.value = true;
        error.value   = null;
        try {
            if (date) {
                const response = await axios.get(`${baseUrl}/currency/historical`, { params: { date } });
                rates.value    = response.data;
                rateDate.value = response.data[Object.keys(response.data)[0]]?.date || date;
            } else {
                const response = await axios.get(`${baseUrl}/currency/rates`);
                rates.value    = response.data;
                rateDate.value = response.data[Object.keys(response.data)[0]]?.date || '';
            }
        } catch (e) {
            error.value = 'Failed to fetch rates from BNR.';
        } finally {
            loading.value = false;
        }
    }

    async function fetchHistorical(date) {
        if (!date) return null;

        // Returnează din cache dacă există
        if (historicalCache.has(date)) return historicalCache.get(date);

        try {
            const response = await axios.get(`${baseUrl}/currency/historical`, { params: { date } });
            const data = response.data;
            if (data && typeof data === 'object' && Object.keys(data).length > 0) {
                historicalCache.set(date, data);
                return data;
            }
            return null;
        } catch {
            return null;
        }
    }

    function getRate(currency) {
        if (currency === 'RON') return 1;
        return rates.value[currency]?.rate || 0;
    }

    function convert(amount, from, to) {
        if (from === to) return amount;
        const fromRate = getRate(from);
        const toRate   = getRate(to);
        if (!fromRate || !toRate) return 0;
        const inRON = from === 'RON' ? amount : amount * fromRate;
        return to === 'RON' ? inRON : inRON / toRate;
    }

    return {
        rates,
        loading,
        error,
        rateDate,
        mainCurrencies,
        allCurrencies,
        baseUrl,
        fetchRates,
        fetchHistorical,
        getRate,
        convert,
    };
}
