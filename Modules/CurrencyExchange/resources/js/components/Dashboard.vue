<template>
  <div class="min-h-screen bg-gray-950 text-white flex flex-col md:flex-row">

    <!-- Sidebar Filters -->
    <div :class="['w-full md:w-80 bg-gray-900 border-r border-gray-800 p-6 overflow-y-auto transition-all',
                   sidebarOpen ? 'block' : 'hidden md:block', 'md:min-h-screen']">

      <div class="flex items-center justify-between mb-6">
        <h2 class="text-white font-bold text-lg">Currency Exchange</h2>
        <a href="/dashboard" class="text-gray-500 hover:text-white text-xs transition">← Dashboard</a>
      </div>

      <!-- Base Currency -->
      <div class="mb-6">
        <label class="block text-sm font-semibold text-gray-300 mb-3">Base Currency</label>
        <div class="flex flex-wrap gap-2">
          <button v-for="cur in mainCurrencies" :key="cur"
            @click="baseCurrency = cur"
            :class="baseCurrency === cur
              ? 'bg-blue-600 text-white border-blue-600'
              : 'bg-gray-800 text-gray-400 border-gray-700 hover:border-blue-500'"
            class="text-xs px-3 py-1.5 rounded-lg border transition">
            {{ cur }}
          </button>
        </div>
      </div>

      <!-- Date -->
      <div class="mb-6">
        <label class="block text-sm font-semibold text-gray-300 mb-3">Date</label>
        <input type="date" v-model="selectedDate" @change="loadRates"
          :max="today"
          class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500 transition">
        <button @click="resetToToday"
          class="mt-2 w-full bg-gray-800 hover:bg-gray-700 text-gray-300 text-xs px-4 py-2 rounded-lg transition">
          Reset to Today
        </button>
      </div>

      <!-- Currencies Filter -->
      <div class="mb-6">
        <label class="block text-sm font-semibold text-gray-300 mb-3">Select Currencies</label>
        <div class="space-y-2 max-h-64 overflow-y-auto">
          <label v-for="cur in allCurrencies" :key="cur"
            class="flex items-center gap-2 cursor-pointer p-1.5 hover:bg-gray-800 rounded transition">
            <input type="checkbox" :value="cur" v-model="selectedCurrencies"
              class="w-4 h-4 rounded border-gray-600 bg-gray-700 text-blue-600">
            <span class="text-sm text-gray-300 font-mono">{{ cur }}</span>
            <span class="text-xs text-gray-500">{{ getCurrencyName(cur) }}</span>
          </label>
        </div>
      </div>

      <!-- Variation Type -->
      <div class="mb-6">
        <label class="block text-sm font-semibold text-gray-300 mb-3">Variation Type</label>
        <div class="space-y-2">
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" v-model="variationType" value="percentage" class="w-4 h-4">
            <span class="text-sm text-gray-300">Percentage (%)</span>
          </label>
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" v-model="variationType" value="absolute" class="w-4 h-4">
            <span class="text-sm text-gray-300">Absolute (RON)</span>
          </label>
        </div>
      </div>

      <!-- Legend -->
      <div class="bg-gray-800 border border-gray-700 rounded-lg p-3 text-xs text-gray-400 space-y-1">
        <div><span class="text-green-400">▲ Green</span> = Increase</div>
        <div><span class="text-red-400">▼ Red</span> = Decrease</div>
        <div><span class="text-gray-400">→ Gray</span> = Stable</div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-4 md:p-6 overflow-y-auto">

      <!-- Mobile Header -->
      <div class="md:hidden flex items-center justify-between mb-4">
        <h1 class="text-lg font-bold">Currency Exchange</h1>
        <button @click="sidebarOpen = !sidebarOpen" class="p-2 hover:bg-gray-800 rounded">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
      </div>

      <!-- Header -->
      <div class="mb-6">
        <h1 class="text-2xl font-bold hidden md:block">Currency Exchange</h1>
        <p class="text-gray-400 text-sm mt-1">Banca Națională a României (BNR) — Live rates</p>
        <div class="flex items-center gap-2 mt-2" v-if="rateDate">
          <span class="text-gray-500 text-sm">Rate date:</span>
          <span class="text-white text-sm font-medium">{{ rateDate }}</span>
          <span class="bg-green-500/10 text-green-400 text-xs px-2 py-0.5 rounded-full">Live</span>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="flex items-center justify-center py-20">
        <div class="w-8 h-8 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
        <span class="text-gray-400 ml-3">Loading rates...</span>
      </div>

      <!-- Error -->
      <div v-else-if="error" class="bg-red-500/10 border border-red-500/20 text-red-400 rounded-lg p-4 mb-6">
        {{ error }}
      </div>

      <div v-else class="space-y-6">

        <!-- Summary Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
          <div v-for="cur in selectedCurrencies.filter(c => c !== baseCurrency)" :key="cur"
            @click="baseCurrency = cur"
            class="bg-gray-900 border border-gray-800 rounded-xl p-4 hover:border-blue-500/50 transition cursor-pointer">
            <div class="flex items-start justify-between mb-2">
              <div>
                <div class="text-blue-400 font-bold text-lg">{{ cur }}</div>
                <div class="text-xs text-gray-500">{{ getCurrencyName(cur) }}</div>
              </div>
              <div :class="['text-xs font-bold px-2 py-1 rounded',
                getVariation(cur) >= 0 ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400']">
                {{ getVariation(cur) >= 0 ? '+' : '' }}{{ getVariation(cur).toFixed(4) }}%
              </div>
            </div>
            <div class="text-white text-xl font-bold">
              {{ baseCurrency === 'RON'
                ? formatRate(getRate(cur))
                : formatRate(convertRate(1, cur, baseCurrency))
              }}
            </div>
            <div class="text-xs text-gray-500 mt-1">
              1 {{ cur }} = <span class="text-blue-400 font-semibold">{{ baseCurrency === 'RON' ? formatRate(getRate(cur)) : formatRate(convertRate(1, cur, baseCurrency)) }}</span> {{ baseCurrency }}
            </div>
          </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
            <h2 class="text-white font-semibold mb-4">Rate Comparison vs {{ baseCurrency }}</h2>
            <canvas ref="barChartRef"></canvas>
          </div>
          <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
            <div class="flex flex-col gap-3 mb-4">
              <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                  <h2 class="text-white font-semibold">Variation ({{ variationType === 'percentage' ? '%' : 'RON' }})</h2>
                  <p class="text-gray-400 text-xs mt-1">Interval: {{ chartStartDate }} → {{ chartEndDate }}</p>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                  <label class="text-gray-400 text-xs">Currency</label>
                  <select v-model="selectedLineCurrency"
                    class="bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500 transition">
                    <option disabled value="" v-if="!lineCurrencyOptions.length">No currency available</option>
                    <option v-for="cur in lineCurrencyOptions" :key="cur" :value="cur">{{ cur }}</option>
                  </select>
                </div>
              </div>
              <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-2 flex-wrap">
                  <label class="text-gray-400 text-xs">Start</label>
                  <input type="date" v-model="chartStartDate" :max="today"
                    class="bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500 transition">
                </div>
                </div>
            </div>
            <canvas ref="lineChartRef"></canvas>
          </div>
        </div>

        <!-- Table + Converter -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

          <!-- Table -->
          <div class="lg:col-span-2 bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-800 flex items-center justify-between">
              <h2 class="text-white font-semibold">All Rates vs {{ baseCurrency }}</h2>
              <input v-model="tableSearch" placeholder="Search..."
                class="bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:border-blue-500 w-36">
            </div>
            <div class="overflow-y-auto max-h-96">
              <table class="w-full text-sm">
                <thead class="sticky top-0 bg-gray-900 border-b border-gray-800">
                  <tr>
                    <th @click="sortBy('currency')" class="text-left text-gray-400 text-xs font-medium px-6 py-3 cursor-pointer hover:text-white">
                      Currency {{ sortField === 'currency' ? (sortDir === 'asc' ? '↑' : '↓') : '' }}
                    </th>
                    <th @click="sortBy('rate')" class="text-right text-gray-400 text-xs font-medium px-6 py-3 cursor-pointer hover:text-white">
                      Rate (RON) {{ sortField === 'rate' ? (sortDir === 'asc' ? '↑' : '↓') : '' }}
                    </th>
                    <th class="text-right text-gray-400 text-xs font-medium px-6 py-3">vs {{ baseCurrency }}</th>
                    <th class="text-right text-gray-400 text-xs font-medium px-6 py-3">Change</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in filteredRates" :key="item.currency"
                    @click="baseCurrency = item.currency"
                    class="border-b border-gray-800/50 hover:bg-gray-800/30 transition cursor-pointer">
                    <td class="px-6 py-3">
                      <div class="text-blue-400 text-xs font-bold font-mono">{{ item.currency }}</div>
                      <div class="text-gray-500 text-xs">{{ getCurrencyName(item.currency) }}</div>
                    </td>
                    <td class="px-6 py-3 text-right text-white text-xs font-mono">{{ formatRate(item.rate) }}</td>
                    <td class="px-6 py-3 text-right text-green-400 text-xs font-mono">
                      {{ baseCurrency === 'RON' ? formatRate(getRate(item.currency)) : formatRate(convertRate(1, item.currency, baseCurrency)) }}
                    </td>
                    <td class="px-6 py-3 text-right text-xs font-mono"
                      :class="getVariation(item.currency) >= 0 ? 'text-green-400' : 'text-red-400'">
                      {{ getVariation(item.currency) >= 0 ? '+' : '' }}{{ getVariation(item.currency).toFixed(2) }}%
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Converter -->
          <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
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
                  <option value="RON">RON</option>
                  <option v-for="cur in allCurrencies" :key="cur" :value="cur">{{ cur }}</option>
                </select>
              </div>
              <div>
                <label class="block text-xs text-gray-400 mb-1">To</label>
                <select v-model="convertTo"
                  class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 transition">
                  <option value="RON">RON</option>
                  <option v-for="cur in allCurrencies" :key="cur" :value="cur">{{ cur }}</option>
                </select>
              </div>
              <div class="bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3">
                <p class="text-gray-400 text-xs mb-1">Result</p>
                <p class="text-green-400 text-xl font-bold">
                  {{ formatRate(convertRate(convertAmount, convertFrom, convertTo)) }}
                  <span class="text-sm">{{ convertTo }}</span>
                </p>
                <p class="text-gray-500 text-xs mt-1">
                  1 {{ convertFrom }} = {{ formatRate(convertRate(1, convertFrom, convertTo)) }} {{ convertTo }}
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
  rates, loading, error, rateDate,
  mainCurrencies, allCurrencies,
  fetchRates, getRate, convert
} = useCurrency();

const CURRENCY_NAMES = {
  'RON': 'Romanian Leu', 'EUR': 'Euro', 'USD': 'US Dollar',
  'GBP': 'British Pound', 'CHF': 'Swiss Franc', 'JPY': 'Japanese Yen',
  'CAD': 'Canadian Dollar', 'AUD': 'Australian Dollar', 'SEK': 'Swedish Krona',
  'NOK': 'Norwegian Krone', 'HUF': 'Hungarian Forint', 'CZK': 'Czech Koruna',
  'BGN': 'Bulgarian Lev', 'PLN': 'Polish Zloty', 'DKK': 'Danish Krone',
  'TRY': 'Turkish Lira', 'CNY': 'Chinese Yuan', 'INR': 'Indian Rupee',
  'XAU': 'Gold (troy oz)', 'XDR': 'SDR',
};

const today         = new Date().toISOString().split('T')[0];
function subtractDays(dateString, days) {
  const date = new Date(dateString + 'T12:00:00');
  date.setDate(date.getDate() - days);
  return date.toISOString().split('T')[0];
}
const selectedDate  = ref('');
const baseCurrency  = ref('EUR');
const tableSearch   = ref('');
const sortField     = ref('currency');
const sortDir       = ref('asc');
const convertAmount = ref(1);
const convertFrom   = ref('EUR');
const convertTo     = ref('RON');
const variationType = ref('percentage');
const sidebarOpen   = ref(false);

const selectedCurrencies = ref(['EUR', 'USD', 'GBP', 'CHF', 'CAD', 'AUD']);
const chartStartDate = ref(subtractDays(today, 10));
const lineCurrencyOptions = computed(() => {
  const opts = selectedCurrencies.value.filter(c => c !== baseCurrency.value);
  if (baseCurrency.value !== 'RON' && !opts.includes('RON')) {
    opts.unshift('RON');
  }
  return opts;
});
const selectedLineCurrency = ref(lineCurrencyOptions.value[0] || '');
const historicalRates = ref([]);

// Yesterday rates for variation
const yesterdayRates = ref({});

const barChartRef  = ref(null);
const lineChartRef = ref(null);
let barChart = null;
let lineChart = null;

function getCurrencyName(cur) {
  return CURRENCY_NAMES[cur] || cur;
}

function formatRate(val) {
  if (val === null || val === undefined || isNaN(val)) return '—';
  return parseFloat(val).toLocaleString('en-US', { minimumFractionDigits: 4, maximumFractionDigits: 4 });
}

function convertRate(amount, from, to) {
  return convert(amount, from, to);
}

function getVariation(currency) {
  // Get rates in RON (the base from BNR)
  const todayRate = getRate(currency);
  const comparisonRate = yesterdayRates.value && yesterdayRates.value[currency]?.rate;

  if (!todayRate || !comparisonRate || isNaN(todayRate) || isNaN(comparisonRate)) {
    return 0;
  }

  if (variationType.value === 'absolute') {
    return todayRate - comparisonRate;
  }

  // Percentage change
  const percentChange = ((todayRate - comparisonRate) / comparisonRate) * 100;
  return percentChange;
}

async function loadRates() {
    if (!selectedDate.value) {
        await fetchRates();
        yesterdayRates.value = {};
        return;
    }
    chartStartDate.value = subtractDays(selectedDate.value, 10);
    await fetchRates(selectedDate.value);
    await loadComparisonRates();
}

async function loadComparisonRates() {
    const targetDate = selectedDate.value;
    if (!targetDate) return;

    const dateObj = new Date(targetDate + 'T12:00:00');
    const dayOfWeek = dateObj.getDay(); // 0=Sun, 1=Mon, 6=Sat

    let offset = 1;
    if (dayOfWeek === 1) offset = 3; // Monday → compare Friday
    if (dayOfWeek === 0) offset = 2; // Sunday → compare Friday
    if (dayOfWeek === 6) offset = 1; // Saturday → compare Friday

    const comparisonDate = new Date(dateObj);
    comparisonDate.setDate(comparisonDate.getDate() - offset);
    const comparisonDateStr = comparisonDate.toISOString().split('T')[0];

    console.log(`Target: ${targetDate} (day ${dayOfWeek}), Comparison: ${comparisonDateStr}`);

    try {
        const response = await fetch(`${baseUrl}/currency/historical?date=${comparisonDateStr}`);
        const data = await response.json();
        if (data && Object.keys(data).length > 0) {
            yesterdayRates.value = data;
        } else {
            yesterdayRates.value = {};
        }
    } catch (e) {
        yesterdayRates.value = {};
    }
}

function getPreviousDate(dateString) {
  const date = new Date(dateString + 'T12:00:00');
  date.setDate(date.getDate() - 1);
  return date.toISOString().split('T')[0];
}

function addMonths(dateString, months) {
  const date = new Date(dateString + 'T12:00:00');
  date.setMonth(date.getMonth() + months);
  return date.toISOString().split('T')[0];
}

function addYears(dateString, years) {
  const date = new Date(dateString + 'T12:00:00');
  date.setFullYear(date.getFullYear() + years);
  return date.toISOString().split('T')[0];
}

const chartEndDate = computed(() => {
  return selectedDate.value || today;
});

async function fetchHistoricalRatesForDate(date) {
  try {
    const response = await fetch(`${baseUrl}/currency/historical?date=${date}`);
    return await response.json();
  } catch {
    return null;
  }
}

async function fetchHistoricalRateForCurrency(date, currency) {
  let currentDate = date;
  let attempts = 0;

  while (attempts < 4) {
    const data = await fetchHistoricalRatesForDate(currentDate);
    const hasValidResponse = data && typeof data === 'object' && Object.keys(data).length > 0;

    if (hasValidResponse) {
      if (currency === 'RON') {
        if (baseCurrency.value === 'RON') {
          return { date: currentDate, rate: 1 };
        }
        const baseRecord = data[baseCurrency.value];
        if (baseRecord?.rate) {
          return { date: baseRecord.date || currentDate, rate: 1 / baseRecord.rate };
        }
      } else {
        const record = data[currency];
        if (record?.rate) {
          return { date: record.date || currentDate, rate: record.rate };
        }
      }
    }

    currentDate = getPreviousDate(currentDate);
    attempts += 1;
  }

  return null;
}

function getDiffDays(startDate, endDate) {
  const start = new Date(startDate + 'T12:00:00');
  const end = new Date(endDate + 'T12:00:00');
  const diffTime = end - start;
  return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
}

function buildChartDates(startDate, endDate, groupBy) {
  const dates = [];
  const start = new Date(startDate + 'T12:00:00');
  const end = new Date(endDate + 'T12:00:00');

  if (groupBy === 'quarterly') {
    let current = new Date(start);
    while (current <= end) {
      dates.push(current.toISOString().split('T')[0]);
      current.setMonth(current.getMonth() + 3);
    }
    return dates;
  }

  const totalDays = getDiffDays(startDate, endDate) + 1;
  const maxPoints = 45;
  const step = Math.max(1, Math.ceil(totalDays / maxPoints));
  let current = new Date(start);

  while (current <= end) {
    dates.push(current.toISOString().split('T')[0]);
    current.setDate(current.getDate() + step);
  }

  if (dates[dates.length - 1] !== endDate) {
    dates.push(endDate);
  }

  return dates;
}

async function loadLineChartHistory() {
  if (!selectedLineCurrency.value) {
    historicalRates.value = [];
    return;
  }

  const startDate = chartStartDate.value || today;
  const endDate = chartEndDate.value;
  const dates = buildChartDates(startDate, endDate, 'daily');
  const points = [];
  const seenDates = new Set();

  for (const date of dates) {
    const record = await fetchHistoricalRateForCurrency(date, selectedLineCurrency.value);
    if (record && record.rate !== undefined && record.rate !== null && !isNaN(record.rate) && !seenDates.has(record.date)) {
      points.push({ date: record.date, rate: record.rate });
      seenDates.add(record.date);
    }
  }

  historicalRates.value = points;
}

function resetToToday() {
  selectedDate.value = '';
  fetchRates();
}

function sortBy(field) {
  if (sortField.value === field) {
    sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortField.value = field;
    sortDir.value   = 'asc';
  }
}

const filteredRates = computed(() => {
  let list = Object.values(rates.value);
  if (tableSearch.value) {
    list = list.filter(r => r.currency.toLowerCase().includes(tableSearch.value.toLowerCase()));
  }
  list.sort((a, b) => {
    let valA = sortField.value === 'currency' ? a.currency : a.rate;
    let valB = sortField.value === 'currency' ? b.currency : b.rate;
    if (valA < valB) return sortDir.value === 'asc' ? -1 : 1;
    if (valA > valB) return sortDir.value === 'asc' ? 1 : -1;
    return 0;
  });
  return list;
});

const baseUrl = document.querySelector('meta[name="base-url"]')?.content || '';

function buildBarChart() {
  if (!barChartRef.value) return;
  if (barChart) barChart.destroy();

  const labels = selectedCurrencies.value.filter(c => c !== baseCurrency.value);
  const data   = labels.map(c => baseCurrency.value === 'RON' ? getRate(c) : convertRate(1, c, baseCurrency.value));
  const colors = labels.map(c => {
    const v = getVariation(c);
    return v >= 0 ? 'rgba(16,185,129,0.7)' : 'rgba(239,68,68,0.7)';
  });

  barChart = new Chart(barChartRef.value, {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        data,
        backgroundColor: colors,
        borderRadius: 6,
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: 'rgba(0,0,0,0.8)',
          callbacks: {
            label: ctx => `1 ${baseCurrency.value} = ${formatRate(ctx.raw)} ${labels[ctx.dataIndex]}`
          }
        }
      },
      scales: {
        x: { ticks: { color: '#6b7280', font: { size: 11 } }, grid: { color: '#1f2937' } },
        y: { ticks: { color: '#6b7280', font: { size: 11 } }, grid: { color: '#1f2937' }, beginAtZero: true }
      }
    }
  });
}

function buildLineChart() {
  if (!lineChartRef.value) return;
  if (lineChart) lineChart.destroy();

  const points = historicalRates.value;
  const labels = points.map(p => p.date);
  const data = points.map((point, index) => {
    if (index === 0) return 0;
    const prev = points[index - 1].rate;
    const diff = point.rate - prev;
    return variationType.value === 'absolute' ? diff : (prev ? (diff / prev) * 100 : 0);
  });
  const colors = data.map(v => v >= 0 ? '#10b981' : '#ef4444');

  lineChart = new Chart(lineChartRef.value, {
    type: 'line',
    data: {
      labels,
      datasets: [{
        data,
        borderColor: '#3b82f6',
        backgroundColor: 'rgba(59,130,246,0.1)',
        fill: true,
        tension: 0.4,
        pointRadius: 6,
        pointBackgroundColor: colors,
        pointBorderColor: colors,
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: 'rgba(0,0,0,0.8)',
          callbacks: {
            label: ctx => `${selectedLineCurrency.value}: ${data[ctx.dataIndex] >= 0 ? '+' : ''}${formatRate(ctx.raw)}${variationType.value === 'percentage' ? '%' : ' RON'}`
          }
        }
      },
      scales: {
        x: { ticks: { color: '#6b7280', font: { size: 11 } }, grid: { color: '#1f2937' } },
        y: { ticks: { color: '#6b7280', font: { size: 11 } }, grid: { color: '#1f2937' } }
      }
    }
  });
}

function getFirstLineCurrencyOption() {
  return selectedCurrencies.value.find(c => c !== baseCurrency.value) || '';
}

watch([selectedCurrencies, baseCurrency], async () => {
  const validOption = getFirstLineCurrencyOption();
  if (!validOption || selectedLineCurrency.value === baseCurrency.value || !selectedCurrencies.value.includes(selectedLineCurrency.value)) {
    selectedLineCurrency.value = validOption;
  }
  await loadLineChartHistory();
}, { deep: true });

watch([selectedLineCurrency, chartStartDate, selectedDate], async () => {
  await loadLineChartHistory();
});

watch([rates, baseCurrency, selectedCurrencies, variationType, historicalRates], async () => {
  await nextTick();
  buildBarChart();
  buildLineChart();
}, { deep: true });

onMounted(async () => {
    // Don't set selectedDate - leave empty to use today's rates endpoint
    await fetchRates();
    await loadLineChartHistory();
});
</script>

<style scoped>
::-webkit-scrollbar { width: 6px; height: 6px; }
::-webkit-scrollbar-track { background: #111827; }
::-webkit-scrollbar-thumb { background: #374151; border-radius: 4px; }
::-webkit-scrollbar-thumb:hover { background: #4b5563; }
</style>
