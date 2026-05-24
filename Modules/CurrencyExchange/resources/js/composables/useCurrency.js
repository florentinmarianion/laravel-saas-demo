import { ref, computed } from 'vue';
import axios from 'axios';

export function useCurrency() {
    const rates      = ref({});
    const loading    = ref(false);
    const error      = ref(null);
    const rateDate   = ref('');

    const mainCurrencies = ['EUR', 'USD', 'GBP', 'CHF', 'CAD', 'AUD', 'RON'];

    const allCurrencies = computed(() => Object.keys(rates.value));

    const baseUrl = document.querySelector('meta[name="base-url"]')?.content || '';
    
    async function fetchRates(date = null) {
        loading.value = true;
        error.value   = null;

        try {
            if (date) {
                const response = await axios.get(`${baseUrl}/currency/historical`, { params: { date } });
                rates.value    = response.data;
                rateDate.value = response.data[Object.keys(response.data)[0]]?.date || date;
            } else {
                // Always use /rates for today - BNR may not have today yet
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

    function getRate(currency) {
        if (currency === 'RON') return 1;
        return rates.value[currency]?.rate || 0;
    }

    function convert(amount, from, to) {
        if (from === to) return amount;

        let inRON = from === 'RON' ? amount : amount * getRate(from);
        let result = to === 'RON' ? inRON : inRON / getRate(to);

        return result;
    }

    return {
        rates,
        loading,
        error,
        rateDate,
        mainCurrencies,
        allCurrencies,
        fetchRates,
        getRate,
        convert,
    };
}