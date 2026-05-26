<template>
  <div class="min-h-screen bg-gray-950 text-white flex flex-col md:flex-row">

    <!-- Sidebar -->
    <div :class="['w-full md:w-80 bg-gray-900 border-r border-gray-800 p-6 overflow-y-auto transition-all',
                   sidebarOpen ? 'block' : 'hidden md:block', 'md:min-h-screen']">

      <div class="flex items-center justify-between mb-6">
        <h2 class="text-white font-bold text-lg">Currency Exchange</h2>
        <a href="/dashboard" class="text-gray-500 hover:text-white text-xs transition">← Dashboard</a>
      </div>

      <!-- From / To radio + currency -->
      <div class="mb-6">
        <div class="flex gap-4 mb-3">
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" v-model="directionMode" value="from" class="w-4 h-4">
            <span class="text-sm font-semibold text-gray-300">From</span>
          </label>
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" v-model="directionMode" value="to" class="w-4 h-4">
            <span class="text-sm font-semibold text-gray-300">To</span>
          </label>
        </div>
        <select v-model="fromCurrency"
          class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500 transition">
          <option v-for="cur in availableCurrencies" :key="cur" :value="cur">
            {{ cur }} — {{ getCurrencyName(cur) }}
          </option>
        </select>
      </div>

      <!-- Rate date -->
      <div class="mb-6">
        <label class="block text-sm font-semibold text-gray-300 mb-3">Rate Date</label>
        <input type="date" v-model="selectedDate" @change="loadRates" :max="today"
          class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500 transition">
        <button @click="resetToToday"
          class="mt-2 w-full bg-gray-800 hover:bg-gray-700 text-gray-300 text-xs px-4 py-2 rounded-lg transition">
          Reset to Today
        </button>
      </div>

      <!-- Chart start date -->
      <div class="mb-6">
        <label class="block text-sm font-semibold text-gray-300 mb-3">Chart Start Date</label>
        <input type="date" v-model="chartStartDate" :max="chartEndDate"
          class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500 transition">
        <button @click="chartStartDate = subtractDays(chartEndDate, 10)"
          class="mt-2 w-full bg-gray-800 hover:bg-gray-700 text-gray-300 text-xs px-4 py-2 rounded-lg transition">
          Reset (10 days)
        </button>
      </div>

      <!-- Select currencies -->
      <div class="mb-6">
        <label class="block text-sm font-semibold text-gray-300 mb-3">Select Currencies</label>
        <div class="space-y-1 max-h-52 overflow-y-auto">
          <label v-for="cur in availableCurrencies.filter(c => c !== fromCurrency)" :key="cur"
            class="flex items-center gap-2 cursor-pointer p-1.5 hover:bg-gray-800 rounded transition">
            <input type="checkbox" :value="cur" v-model="selectedCurrencies"
              class="w-4 h-4 rounded border-gray-600 bg-gray-700 text-blue-600">
            <span class="text-sm text-gray-300 font-mono">{{ cur }}</span>
            <span class="text-xs text-gray-500">{{ getCurrencyName(cur) }}</span>
          </label>
        </div>
      </div>

      <!-- Variation type -->
      <div class="mb-6">
        <label class="block text-sm font-semibold text-gray-300 mb-3">Variation Type</label>
        <div class="space-y-2">
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" v-model="variationType" value="percentage" class="w-4 h-4">
            <span class="text-sm text-gray-300">Percentage (%)</span>
          </label>
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" v-model="variationType" value="absolute" class="w-4 h-4">
            <span class="text-sm text-gray-300">Absolute</span>
          </label>
        </div>
      </div>

      <!-- Chart style -->
      <div class="mb-6">
        <label class="block text-sm font-semibold text-gray-300 mb-3">Chart Style</label>
        <div class="space-y-2">
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" v-model="chartStyle" value="area" class="w-4 h-4">
            <span class="text-sm text-gray-300">Area (filled + points)</span>
          </label>
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" v-model="chartStyle" value="line" class="w-4 h-4">
            <span class="text-sm text-gray-300">Line (thin, no fill)</span>
          </label>
        </div>
      </div>

      <!-- Legend -->
      <div class="bg-gray-800 border border-gray-700 rounded-lg p-3 text-xs text-gray-400 space-y-1">
        <div><span class="text-green-400">▲</span> = Increase</div>
        <div><span class="text-red-400">▼</span> = Decrease</div>
        <div><span class="text-gray-400">→</span> = Stable</div>
      </div>
    </div>

    <!-- Main content -->
    <div class="flex-1 p-4 md:p-6 overflow-y-auto">

      <div class="md:hidden flex items-center justify-between mb-4">
        <h1 class="text-lg font-bold">Currency Exchange</h1>
        <button @click="sidebarOpen = !sidebarOpen" class="p-2 hover:bg-gray-800 rounded">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
      </div>

      <div class="mb-6">
        <h1 class="text-2xl font-bold hidden md:block">Currency Exchange</h1>
        <p class="text-gray-400 text-sm mt-1">
          Exchange rates sourced from
          <a href="https://www.bnr.ro" target="_blank" rel="noopener noreferrer"
             class="text-blue-400 hover:text-blue-300 underline underline-offset-2 transition">bnr.ro</a>
          — updated daily by the National Bank of Romania
        </p>
        <div class="flex items-center gap-2 mt-2" v-if="rateDate">
          <span class="text-gray-500 text-sm">Rate date:</span>
          <span class="text-white text-sm font-medium">{{ rateDate }}</span>
          <span v-if="comparisonDate" class="text-gray-500 text-sm ml-2">
            vs <span class="text-white font-medium">{{ comparisonDate }}</span>
          </span>
        </div>
      </div>

      <div v-if="loading" class="flex items-center justify-center py-20">
        <div class="w-8 h-8 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
        <span class="text-gray-400 ml-3">Loading rates...</span>
      </div>

      <div v-else-if="error" class="bg-red-500/10 border border-red-500/20 text-red-400 rounded-lg p-4 mb-6">{{ error }}</div>

      <div v-else class="space-y-6">

        <!-- Summary cards -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
          <div v-for="cur in cardCurrencies" :key="cur"
            class="bg-gray-900 border border-gray-800 rounded-xl p-4 hover:border-blue-500/50 transition">
            <div class="flex items-start justify-between mb-2">
              <div>
                <div class="text-blue-400 font-bold text-lg">{{ cur }}</div>
                <div class="text-xs text-gray-500">{{ getCurrencyName(cur) }}</div>
              </div>
              <div :class="['text-xs font-bold px-2 py-1 rounded flex items-center gap-1',
                getVariation(cur) > 0 ? 'bg-green-500/20 text-green-400' :
                getVariation(cur) < 0 ? 'bg-red-500/20 text-red-400'    :
                                        'bg-gray-700 text-gray-400']">
                <span>{{ getVariation(cur) > 0 ? '▲' : getVariation(cur) < 0 ? '▼' : '→' }}</span>
                <span>{{ formatVariation(getVariation(cur), cur) }}</span>
              </div>
            </div>
            <div class="text-white text-xl font-bold">{{ formatRate(getCardRate(cur)) }}</div>
            <div class="text-xs text-gray-500 mt-1">
              <template v-if="directionMode === 'from'">
                1 <span class="text-blue-400 font-semibold">{{ fromCurrency }}</span>
                = {{ formatRate(getCardRate(cur)) }} {{ cur }}
              </template>
              <template v-else>
                1 <span class="text-blue-400 font-semibold">{{ cur }}</span>
                = {{ formatRate(getCardRate(cur)) }} {{ fromCurrency }}
              </template>
            </div>
          </div>
        </div>

        <!-- Charts row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

          <!-- Charts column -->
          <div class="lg:col-span-2 space-y-6">

            <!-- Variation chart -->
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
              <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                <div>
                  <h2 class="text-white font-semibold">
                    {{ chartFrom }} → {{ chartTo }}
                    Variation
                    ({{ variationType === 'percentage' ? '%' : chartTo }})
                  </h2>
                  <p class="text-gray-400 text-xs mt-1">{{ chartStartDate }} → {{ chartEndDate }}</p>
                </div>
                <div class="flex items-center gap-2">
                  <label class="text-gray-400 text-xs">Currency</label>
                  <select v-model="selectedLineCurrency"
                    class="bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500 transition">
                    <option disabled value="">—</option>
                    <option v-for="cur in lineCurrencyOptions" :key="cur" :value="cur">{{ cur }}</option>
                  </select>
                </div>
              </div>
              <div v-if="chartLoading" class="flex items-center justify-center py-12">
                <div class="w-6 h-6 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                <span class="text-gray-400 text-sm ml-2">Loading chart data...</span>
              </div>
              <canvas v-show="!chartLoading" ref="variationChartRef"></canvas>
            </div>

            <!-- Nominal chart -->
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
              <div class="mb-4">
                <h2 class="text-white font-semibold">
                  {{ chartFrom }} → {{ chartTo }} Rate
                </h2>
                <p class="text-gray-400 text-xs mt-1">{{ chartStartDate }} → {{ chartEndDate }}</p>
              </div>
              <div v-if="chartLoading" class="flex items-center justify-center py-12">
                <div class="w-6 h-6 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                <span class="text-gray-400 text-sm ml-2">Loading chart data...</span>
              </div>
              <canvas v-show="!chartLoading" ref="nominalChartRef"></canvas>
            </div>
          </div>

          <!-- Converter -->
          <div class="bg-gray-900 border border-gray-800 rounded-xl p-6 h-fit">
            <h2 class="text-white font-semibold mb-4">Converter</h2>
            <div class="space-y-4">
              <div>
                <label class="block text-xs text-gray-400 mb-1">Amount</label>
                <input type="number" v-model="convertAmount" min="0" step="0.01"
                  class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 transition">
              </div>
              <div>
                <label class="block text-xs text-gray-400 mb-1">From</label>
                <select v-model="convertFrom"
                  class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 transition">
                  <option v-for="cur in availableCurrencies" :key="cur" :value="cur">{{ cur }}</option>
                </select>
              </div>
              <div>
                <label class="block text-xs text-gray-400 mb-1">To</label>
                <select v-model="convertTo"
                  class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 transition">
                  <option v-for="cur in availableCurrencies" :key="cur" :value="cur">{{ cur }}</option>
                </select>
              </div>
              <button @click="swapConverter"
                class="w-full bg-gray-700 hover:bg-gray-600 text-gray-300 text-xs px-4 py-2 rounded-lg transition flex items-center justify-center gap-2">
                <span>⇅</span> Swap currencies
              </button>
              <div class="bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3">
                <p class="text-gray-400 text-xs mb-1">Result</p>
                <p class="text-green-400 text-xl font-bold">
                  {{ formatRate(convert(convertAmount, convertFrom, convertTo)) }}
                  <span class="text-sm">{{ convertTo }}</span>
                </p>
                <p class="text-gray-500 text-xs mt-1">
                  1 {{ convertFrom }} = {{ formatRate(convert(1, convertFrom, convertTo)) }} {{ convertTo }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, nextTick } from 'vue';
import { useCurrency } from '../composables/useCurrency';
import Chart from 'chart.js/auto';

const {
  loading, error, rateDate,
  allCurrencies,
  fetchRates, fetchHistorical, getRate, convert,
} = useCurrency();

// ─── Constants ────────────────────────────────────────────────────────────────
const CURRENCY_ORDER = [
  'RON','USD','EUR','JPY','GBP','CHF','CNY','CAD','AUD','SGD','HKD',
  'SEK','NOK','DKK','NZD','INR','KRW','MXN','BRL','PLN','CZK','THB',
  'MYR','ZAR','ILS','HUF','PHP','IDR','RSD','MDL','EGP','ISK','UAH',
  'TRY','RUB','XDR','XAU',
];

const CURRENCY_NAMES = {
  'RON':'Romanian Leu',     'EUR':'Euro',              'USD':'US Dollar',
  'GBP':'British Pound',    'CHF':'Swiss Franc',       'JPY':'Japanese Yen',
  'CAD':'Canadian Dollar',  'AUD':'Australian Dollar', 'SEK':'Swedish Krona',
  'NOK':'Norwegian Krone',  'HUF':'Hungarian Forint',  'CZK':'Czech Koruna',
  'BGN':'Bulgarian Lev',    'PLN':'Polish Zloty',      'DKK':'Danish Krone',
  'TRY':'Turkish Lira',     'CNY':'Chinese Yuan',      'INR':'Indian Rupee',
  'XAU':'Gold (troy oz)',   'XDR':'SDR',               'SGD':'Singapore Dollar',
  'HKD':'Hong Kong Dollar', 'NZD':'New Zealand Dollar','KRW':'South Korean Won',
  'MXN':'Mexican Peso',     'BRL':'Brazilian Real',    'THB':'Thai Baht',
  'MYR':'Malaysian Ringgit','ZAR':'South African Rand','ILS':'Israeli Shekel',
  'PHP':'Philippine Peso',  'IDR':'Indonesian Rupiah', 'RSD':'Serbian Dinar',
  'MDL':'Moldovan Leu',     'EGP':'Egyptian Pound',    'ISK':'Icelandic Króna',
  'UAH':'Ukrainian Hryvnia','RUB':'Russian Ruble',
};

// ─── Date helpers ─────────────────────────────────────────────────────────────
const today = new Date().toISOString().split('T')[0];

function subtractDays(ds, days) {
  const d = new Date(ds + 'T12:00:00');
  d.setDate(d.getDate() - days);
  return d.toISOString().split('T')[0];
}

function previousWorkingDay(ds) {
  const d = new Date(ds + 'T12:00:00');
  d.setDate(d.getDate() - 1);
  while (d.getDay() === 0 || d.getDay() === 6) d.setDate(d.getDate() - 1);
  return d.toISOString().split('T')[0];
}

function getPreviousDate(ds) {
  const d = new Date(ds + 'T12:00:00');
  d.setDate(d.getDate() - 1);
  return d.toISOString().split('T')[0];
}

// ─── State ────────────────────────────────────────────────────────────────────
const fromCurrency         = ref('EUR');
const directionMode        = ref('from');
const selectedDate         = ref('');
const convertAmount        = ref(1);
const convertFrom          = ref('EUR');
const convertTo            = ref('RON');
const variationType        = ref('percentage');
const chartStyle           = ref('area');
const sidebarOpen          = ref(false);
const yesterdayRates       = ref({});
const todayHistorical      = ref({});
const historicalRates      = ref([]);
const chartStartDate       = ref(subtractDays(today, 10));
const chartLoading         = ref(false);
const variationChartRef    = ref(null);
const nominalChartRef      = ref(null);
const selectedLineCurrency = ref('');
let variationChart         = null;
let nominalChart           = null;

const selectedCurrencies = ref(['RON', 'USD', 'GBP', 'CHF', 'CAD', 'AUD']);

// ─── Computed ─────────────────────────────────────────────────────────────────
const availableCurrencies = computed(() => {
  const bnrSet = new Set(['RON', ...allCurrencies.value]);
  return CURRENCY_ORDER.filter(c => bnrSet.has(c));
});

const cardCurrencies = computed(() => {
  const sel = new Set(selectedCurrencies.value);
  return availableCurrencies.value.filter(c => c !== fromCurrency.value && sel.has(c));
});

const lineCurrencyOptions = computed(() => cardCurrencies.value);
const chartEndDate        = computed(() => selectedDate.value || today);

// Derived chart axis labels — avoids repetition in template and chart builders
const chartFrom = computed(() =>
  directionMode.value === 'from' ? fromCurrency.value : selectedLineCurrency.value
);
const chartTo = computed(() =>
  directionMode.value === 'from' ? selectedLineCurrency.value : fromCurrency.value
);

// The date we're comparing against for variation badges
const comparisonDate = computed(() => {
  const ref = selectedDate.value || rateDate.value;
  return ref ? previousWorkingDay(ref) : null;
});

// ─── Helpers ──────────────────────────────────────────────────────────────────
function getCurrencyName(cur) { return CURRENCY_NAMES[cur] || cur; }

function formatRate(val) {
  if (val == null || isNaN(val)) return '—';
  return parseFloat(val).toLocaleString('en-US', { minimumFractionDigits: 4, maximumFractionDigits: 4 });
}

// Formats the variation badge: sign + value + unit (currency or %)
function formatVariation(val, cardCurrency) {
  const sign = val > 0 ? '+' : '';
  if (variationType.value === 'percentage') {
    return `${sign}${Math.abs(val).toFixed(4)}%`;
  }
  // Absolute: unit is the "result" currency of the card rate
  const unit = directionMode.value === 'from' ? cardCurrency : fromCurrency.value;
  return `${sign}${Math.abs(val).toFixed(4)} ${unit}`;
}

// ─── Rate helpers ─────────────────────────────────────────────────────────────
// Rate displayed on card: 1 fromCurrency = X cur  (from) | 1 cur = X fromCurrency (to)
function getCardRate(cur) {
  return directionMode.value === 'from'
    ? convert(1, fromCurrency.value, cur)
    : convert(1, cur, fromCurrency.value);
}

// Cross-rate from a raw BNR snapshot (all values in RON), respecting directionMode
function getCrossRate(data, currency) {
  if (!data || !Object.keys(data).length) return null;
  const fromRON = fromCurrency.value === 'RON' ? 1 : data[fromCurrency.value]?.rate;
  const toRON   = currency === 'RON'           ? 1 : data[currency]?.rate;
  if (!fromRON || !toRON) return null;
  return directionMode.value === 'to' ? toRON / fromRON : fromRON / toRON;
}

// Day-over-day variation in display units
function getVariation(currency) {
  const todayCross = getCrossRate(todayHistorical.value, currency);
  const yestCross  = getCrossRate(yesterdayRates.value,  currency);
  if (!todayCross || !yestCross) return 0;
  if (variationType.value === 'absolute') return todayCross - yestCross;
  return ((todayCross - yestCross) / yestCross) * 100;
}

// ─── Converter swap ───────────────────────────────────────────────────────────
function swapConverter() {
  [convertFrom.value, convertTo.value] = [convertTo.value, convertFrom.value];
}

// ─── Rate loading ─────────────────────────────────────────────────────────────
async function loadComparisonRates(referenceDate) {
  const [todayData, yestData] = await Promise.all([
    fetchHistorical(referenceDate),
    fetchHistorical(previousWorkingDay(referenceDate)),
  ]);
  todayHistorical.value = todayData || {};
  yesterdayRates.value  = yestData  || {};
}

async function loadRates() {
  if (!selectedDate.value) {
    await fetchRates();
    await loadComparisonRates(rateDate.value || today);
    return;
  }
  chartStartDate.value = subtractDays(selectedDate.value, 10);
  await fetchRates(selectedDate.value);
  await loadComparisonRates(selectedDate.value);
}

async function resetToToday() {
  selectedDate.value   = '';
  chartStartDate.value = subtractDays(today, 10);
  await fetchRates();
  await loadComparisonRates(rateDate.value || today);
  await loadLineChartHistory();
}

// ─── Fetch a single date's cross-rate (with weekend/holiday fallback) ─────────
async function fetchCrossRateForDate(date) {
  let d = date;
  for (let i = 0; i < 4; i++) {
    const data = await fetchHistorical(d);
    if (data && Object.keys(data).length > 0) {
      const fromRON = fromCurrency.value === 'RON'         ? 1 : data[fromCurrency.value]?.rate;
      const toRON   = selectedLineCurrency.value === 'RON' ? 1 : data[selectedLineCurrency.value]?.rate;
      if (fromRON && toRON) {
        // Use the date from the response, not the requested date (important after fallback)
        const anchor  = selectedLineCurrency.value === 'RON' ? fromCurrency.value : selectedLineCurrency.value;
        const dateKey = data[anchor]?.date || d;
        const rate    = directionMode.value === 'to' ? toRON / fromRON : fromRON / toRON;
        return { date: dateKey, rate };
      }
    }
    d = getPreviousDate(d);
  }
  return null;
}

// ─── Load historical data for both charts ─────────────────────────────────────
async function loadLineChartHistory() {
  if (!selectedLineCurrency.value || !fromCurrency.value) {
    historicalRates.value = [];
    return;
  }

  chartLoading.value = true;

  const start = chartStartDate.value || today;
  const end   = chartEndDate.value;

  // Max 45 visible points, evenly spaced
  const total = Math.ceil((new Date(end + 'T12:00:00') - new Date(start + 'T12:00:00')) / 86400000) + 1;
  const step  = Math.max(1, Math.ceil(total / 45));

  const dates = [];
  let cur     = new Date(start + 'T12:00:00');
  const endD  = new Date(end + 'T12:00:00');
  while (cur <= endD) {
    dates.push(cur.toISOString().split('T')[0]);
    cur.setDate(cur.getDate() + step);
  }
  if (dates[dates.length - 1] !== end) dates.push(end);

  // One extra point before the interval as baseline for the first variation value
  const baselineDate = previousWorkingDay(start);
  const baseline     = await fetchCrossRateForDate(baselineDate);

  const points = [];
  const seen   = new Set();

  if (baseline) {
    points.push({ ...baseline, baseline: true });
    seen.add(baseline.date);
  }

  for (const date of dates) {
    const rec = await fetchCrossRateForDate(date);
    if (rec && !isNaN(rec.rate) && !seen.has(rec.date)) {
      points.push(rec);
      seen.add(rec.date);
    }
  }

  historicalRates.value = points;
  chartLoading.value    = false;
}

// ─── Shared dataset style ─────────────────────────────────────────────────────
function getDatasetStyle(pointColors) {
  const isArea = chartStyle.value === 'area';
  return {
    borderWidth:          isArea ? 2   : 1.5,
    tension:              isArea ? 0.4 : 0.3,
    pointRadius:          isArea ? 6   : 1,
    pointHoverRadius:     isArea ? 8   : 4,
    fill:                 isArea,
    pointBackgroundColor: isArea ? pointColors : 'transparent',
    pointBorderColor:     isArea ? pointColors : 'transparent',
  };
}

// ─── Shared Chart.js options ──────────────────────────────────────────────────
function baseChartOptions(tooltipLabelFn) {
  return {
    responsive: true,
    plugins: {
      legend: { display: false },
      tooltip: {
        backgroundColor: 'rgba(0,0,0,0.9)',
        callbacks: {
          title: ctx => ctx[0].label,
          label: tooltipLabelFn,
        },
      },
    },
    scales: {
      x: { ticks: { color: '#6b7280', font: { size: 11 } }, grid: { color: '#1f2937' } },
      y: { ticks: { color: '#6b7280', font: { size: 11 } }, grid: { color: '#1f2937' } },
    },
  };
}

// ─── Variation chart ──────────────────────────────────────────────────────────
function buildVariationChart() {
  if (!variationChartRef.value) return;
  if (variationChart) variationChart.destroy();

  const points = historicalRates.value;
  if (!points.length) return;

  const hasBaseline = points[0]?.baseline === true;
  const visible     = hasBaseline ? points.slice(1) : points;
  if (!visible.length) return;

  const absUnit  = ' ' + chartTo.value;
  const unit     = variationType.value === 'percentage' ? '%' : absUnit;

  const labels     = visible.map(p => p.date);
  const variations = visible.map((p, i) => {
    const prevRate = i === 0
      ? (hasBaseline ? points[0].rate : null)
      : visible[i - 1].rate;
    if (!prevRate) return 0;
    const diff = p.rate - prevRate;
    return variationType.value === 'absolute' ? diff : (diff / prevRate) * 100;
  });
  const colors = variations.map(v => v >= 0 ? '#10b981' : '#ef4444');

  variationChart = new Chart(variationChartRef.value, {
    type: 'line',
    data: {
      labels,
      datasets: [{
        data: variations,
        borderColor: '#3b82f6',
        backgroundColor: 'rgba(59,130,246,0.1)',
        ...getDatasetStyle(colors),
      }],
    },
    options: baseChartOptions(ctx => {
      const i    = ctx.dataIndex;
      const sign = variations[i] >= 0 ? '+' : '';
      return `${sign}${formatRate(variations[i])}${unit}  |  1 ${chartFrom.value} = ${formatRate(visible[i].rate)} ${chartTo.value}`;
    }),
  });
}

// ─── Nominal chart ────────────────────────────────────────────────────────────
function buildNominalChart() {
  if (!nominalChartRef.value) return;
  if (nominalChart) nominalChart.destroy();

  const points = historicalRates.value;
  if (!points.length) return;

  const hasBaseline = points[0]?.baseline === true;
  const visible     = hasBaseline ? points.slice(1) : points;
  if (!visible.length) return;

  const labels = visible.map(p => p.date);
  const values = visible.map(p => p.rate);
  const pointColors = values.map((v, i) =>
    i === 0 ? '#6b7280' : v >= values[i - 1] ? '#10b981' : '#ef4444'
  );

  nominalChart = new Chart(nominalChartRef.value, {
    type: 'line',
    data: {
      labels,
      datasets: [{
        data: values,
        borderColor: '#8b5cf6',
        backgroundColor: 'rgba(139,92,246,0.1)',
        ...getDatasetStyle(pointColors),
      }],
    },
    options: {
      ...baseChartOptions(ctx => `1 ${chartFrom.value} = ${formatRate(ctx.raw)} ${chartTo.value}`),
      scales: {
        x: { ticks: { color: '#6b7280', font: { size: 11 } }, grid: { color: '#1f2937' } },
        y: { ticks: { color: '#6b7280', font: { size: 11 } }, grid: { color: '#1f2937' }, grace: '2%' },
      },
    },
  });
}

function buildCharts() {
  buildVariationChart();
  buildNominalChart();
}

// ─── Default line currency ────────────────────────────────────────────────────
function pickDefaultLineCurrency(previous) {
  const opts = lineCurrencyOptions.value;
  if (!opts.length) return '';
  if (previous && opts.includes(previous)) return previous;
  if (opts.includes('RON')) return 'RON';
  return opts[0];
}

// ─── Watchers ─────────────────────────────────────────────────────────────────
watch(fromCurrency, () => {
  selectedCurrencies.value   = selectedCurrencies.value.filter(c => c !== fromCurrency.value);
  selectedLineCurrency.value = pickDefaultLineCurrency(selectedLineCurrency.value);
});

watch(selectedCurrencies, () => {
  selectedLineCurrency.value = pickDefaultLineCurrency(selectedLineCurrency.value);
}, { deep: true });

watch([selectedLineCurrency, chartStartDate, selectedDate, directionMode, fromCurrency], async () => {
  await loadLineChartHistory();
});

watch([historicalRates, variationType, chartStyle], async () => {
  await nextTick();
  buildCharts();
}, { deep: true });

// ─── Mount ────────────────────────────────────────────────────────────────────
onMounted(async () => {
  await fetchRates();
  await loadComparisonRates(rateDate.value || today);
  selectedLineCurrency.value = pickDefaultLineCurrency('');
  await loadLineChartHistory();
});
</script>

<style scoped>
::-webkit-scrollbar { width: 6px; height: 6px; }
::-webkit-scrollbar-track { background: #111827; }
::-webkit-scrollbar-thumb { background: #374151; border-radius: 4px; }
::-webkit-scrollbar-thumb:hover { background: #4b5563; }
</style>
