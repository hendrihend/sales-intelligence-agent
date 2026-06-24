<script setup>
import AuthenticatedLayout from '@/Layouts/Sidebar.vue';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Line, Bar, Doughnut } from 'vue-chartjs';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    BarElement,
    ArcElement,
    Title,
    Tooltip,
    Legend,
    Filler,
} from 'chart.js';

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    BarElement,
    ArcElement,
    Title,
    Tooltip,
    Legend,
    Filler,
);

// --- Receive props from DashboardController ---
const props = defineProps({
    summaryCards: {
        type: Array,
        default: () => [],
    },
    revenueChart: {
        type: Object,
        default: () => ({ currentYear: [], lastYear: [], currentYearLabel: '', lastYearLabel: '' }),
    },
    categoryChart: {
        type: Object,
        default: () => ({ labels: [], currentMonth: [], lastMonth: [] }),
    },
    paymentChart: {
        type: Object,
        default: () => ({ labels: [], currentMonth: [] }),
    },
    topProducts: {
        type: Array,
        default: () => [],
    },
    recentTransactions: {
        type: Array,
        default: () => [],
    },
    discountSuggestions: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({ year: new Date().getFullYear() }),
    },
});

// --- Year Filter ---
const selectedYear = ref(props.filters.year);
const availableYears = computed(() => {
    const currentYear = new Date().getFullYear();
    return Array.from({ length: 5 }, (_, i) => currentYear - i);
});

const updateYear = () => {
    router.get(route('dashboard'), { year: selectedYear.value }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

// --- Line Chart: Pendapatan Bulanan ---
const monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];

const revenueChartData = computed(() => ({
    labels: monthLabels,
    datasets: [
        {
            label: props.revenueChart.currentYearLabel || 'Tahun Ini',
            data: props.revenueChart.currentYear,
            borderColor: '#10b981',
            backgroundColor: (ctx) => {
                if (!ctx.chart?.ctx) return 'rgba(16, 185, 129, 0.1)';
                const gradient = ctx.chart.ctx.createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, 'rgba(16, 185, 129, 0.3)');
                gradient.addColorStop(1, 'rgba(16, 185, 129, 0.0)');
                return gradient;
            },
            borderWidth: 3,
            pointRadius: 4,
            pointHoverRadius: 7,
            pointBackgroundColor: '#10b981',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            tension: 0.4,
            fill: true,
        },
        {
            label: props.revenueChart.lastYearLabel || 'Tahun Lalu',
            data: props.revenueChart.lastYear,
            borderColor: '#94a3b8',
            backgroundColor: 'transparent',
            borderWidth: 2,
            pointRadius: 0,
            pointHoverRadius: 5,
            pointBackgroundColor: '#94a3b8',
            borderDash: [5, 5],
            tension: 0.4,
            fill: false,
        },
    ],
}));

const revenueChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    interaction: { mode: 'index', intersect: false },
    plugins: {
        legend: {
            position: 'top',
            align: 'end',
            labels: {
                usePointStyle: true,
                pointStyle: 'circle',
                padding: 20,
                font: { size: 12, family: "'Inter', sans-serif" },
                color: '#64748b',
            },
        },
        tooltip: {
            backgroundColor: '#1e293b',
            titleColor: '#f8fafc',
            bodyColor: '#cbd5e1',
            titleFont: { size: 13, weight: '600' },
            bodyFont: { size: 12 },
            padding: 12,
            cornerRadius: 10,
            displayColors: true,
            callbacks: {
                label: (ctx) => {
                    const val = ctx.parsed.y;
                    return ` ${ctx.dataset.label}: Rp ${val.toLocaleString('id-ID')}`;
                },
            },
        },
    },
    scales: {
        x: {
            grid: { display: false },
            ticks: { font: { size: 12, family: "'Inter', sans-serif" }, color: '#94a3b8' },
        },
        y: {
            grid: { color: '#f1f5f9', drawBorder: false },
            ticks: {
                font: { size: 11, family: "'Inter', sans-serif" },
                color: '#94a3b8',
                callback: (val) => {
                    if (val >= 1000000) return `Rp ${(val / 1000000).toFixed(0)}jt`;
                    if (val >= 1000) return `Rp ${(val / 1000).toFixed(0)}rb`;
                    return `Rp ${val}`;
                },
            },
        },
    },
};

// --- Bar Chart: Penjualan per Kategori ---
const categoryChartData = computed(() => ({
    labels: props.categoryChart.labels.length > 0 ? props.categoryChart.labels : ['Belum ada data'],
    datasets: [
        {
            label: 'Bulan Ini',
            data: props.categoryChart.currentMonth.length > 0 ? props.categoryChart.currentMonth : [0],
            backgroundColor: '#10b981',
            borderRadius: 8,
            borderSkipped: false,
            barPercentage: 0.6,
        },
        {
            label: 'Bulan Lalu',
            data: props.categoryChart.lastMonth.length > 0 ? props.categoryChart.lastMonth : [0],
            backgroundColor: '#e2e8f0',
            borderRadius: 8,
            borderSkipped: false,
            barPercentage: 0.6,
        },
    ],
}));

const categoryChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'top',
            align: 'end',
            labels: {
                usePointStyle: true,
                pointStyle: 'circle',
                padding: 20,
                font: { size: 12, family: "'Inter', sans-serif" },
                color: '#64748b',
            },
        },
        tooltip: {
            backgroundColor: '#1e293b',
            titleColor: '#f8fafc',
            bodyColor: '#cbd5e1',
            padding: 12,
            cornerRadius: 10,
            callbacks: {
                label: (ctx) => ` ${ctx.dataset.label}: ${ctx.parsed.y} transaksi`,
            },
        },
    },
    scales: {
        x: {
            grid: { display: false },
            ticks: { font: { size: 11, family: "'Inter', sans-serif" }, color: '#94a3b8' },
        },
        y: {
            grid: { color: '#f1f5f9', drawBorder: false },
            ticks: { font: { size: 11, family: "'Inter', sans-serif" }, color: '#94a3b8' },
        },
    },
};

// --- Doughnut Chart: Metode Pembayaran (dari paymentChart data) ---
const paymentChartData = computed(() => {
    const labels = props.paymentChart.labels.length > 0 ? props.paymentChart.labels : ['Belum ada data'];
    const data = props.paymentChart.currentMonth.length > 0 ? props.paymentChart.currentMonth : [1];
    const colors = ['#10b981', '#3b82f6', '#8b5cf6', '#f59e0b', '#ec4899', '#06b6d4', '#f43f5e'];

    return {
        labels,
        datasets: [{
            data,
            backgroundColor: colors.slice(0, labels.length),
            borderWidth: 0,
            spacing: 4,
            borderRadius: 6,
        }],
    };
});

const paymentChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    cutout: '72%',
    plugins: {
        legend: {
            position: 'bottom',
            labels: {
                usePointStyle: true,
                pointStyle: 'circle',
                padding: 16,
                font: { size: 11, family: "'Inter', sans-serif" },
                color: '#64748b',
            },
        },
        tooltip: {
            backgroundColor: '#1e293b',
            titleColor: '#f8fafc',
            bodyColor: '#cbd5e1',
            padding: 12,
            cornerRadius: 10,
            callbacks: {
                label: (ctx) => ` ${ctx.label}: ${ctx.parsed} transaksi`,
            },
        },
    },
};

// --- Status helpers ---
const statusColor = (status) => {
    const map = {
        success: 'bg-emerald-50 text-emerald-600',
        pending: 'bg-amber-50 text-amber-600',
        refund: 'bg-red-50 text-red-600',
    };
    return map[status] || '';
};

const statusLabel = (status) => {
    const map = { success: 'Berhasil', pending: 'Menunggu', refund: 'Refund' };
    return map[status] || status;
};

// --- Check if we have data ---
const hasData = computed(() => {
    return props.revenueChart.currentYear.some(v => v > 0) ||
           props.topProducts.length > 0 ||
           props.recentTransactions.length > 0;
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="font-bold text-xl text-gray-800 leading-tight">Dashboard Overview</h2>
                    <p class="text-sm text-gray-500 mt-0.5">Pantau kinerja dan aktivitas toko Anda.</p>
                </div>
                <div class="flex items-center gap-3">
                    <a :href="route('transaksi.export.excel')" class="inline-flex items-center px-4 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-sm font-medium rounded-lg transition-colors border border-emerald-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2-2H5a2 2 0 01-2-2z" /></svg>
                        Export Excel
                    </a>
                    <a :href="route('transaksi.export.pdf')" class="inline-flex items-center px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 text-sm font-medium rounded-lg transition-colors border border-rose-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                        Export PDF
                    </a>
                </div>
            </div>
        </template>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
            <div
                v-for="card in summaryCards"
                :key="card.label"
                class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200"
            >
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ card.label }}</p>
                    <div
                        :class="[
                            'p-2.5 rounded-lg',
                            card.color === 'emerald' ? 'bg-emerald-50' : '',
                            card.color === 'blue' ? 'bg-blue-50' : '',
                            card.color === 'violet' ? 'bg-violet-50' : '',
                            card.color === 'amber' ? 'bg-amber-50' : '',
                        ]"
                    >
                        <svg v-if="card.icon === 'revenue'" class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <svg v-if="card.icon === 'transaction'" class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                        <svg v-if="card.icon === 'customer'" class="w-5 h-5 text-violet-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                        <svg v-if="card.icon === 'efficiency'" class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900">{{ card.value }}</h3>
                <div class="flex items-center mt-2 space-x-1">
                    <span
                        :class="[
                            'text-xs font-semibold px-1.5 py-0.5 rounded',
                            card.changeType === 'up' ? 'text-emerald-700 bg-emerald-50' : 'text-red-700 bg-red-50'
                        ]"
                    >
                        {{ card.change }}
                    </span>
                    <span class="text-xs text-gray-400">vs bulan lalu</span>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Revenue Line Chart -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Pendapatan</h3>
                        <p class="text-sm text-gray-400 mt-0.5">Perbandingan pendapatan tahun {{ selectedYear }} vs tahun {{ selectedYear - 1 }}</p>
                    </div>
                    <div>
                        <select v-model="selectedYear" @change="updateYear" class="border-gray-200 rounded-lg text-sm text-gray-700 focus:ring-emerald-500 focus:border-emerald-500 font-medium">
                            <option v-for="year in availableYears" :key="year" :value="year">{{ year }}</option>
                        </select>
                    </div>
                </div>
                <div style="height: 320px;">
                    <Line :data="revenueChartData" :options="revenueChartOptions" />
                </div>
            </div>

            <!-- Doughnut Chart -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="mb-4">
                    <h3 class="text-lg font-bold text-gray-800">Metode Pembayaran</h3>
                    <p class="text-sm text-gray-400 mt-0.5">Transaksi per metode pembayaran bulan ini</p>
                </div>
                <div style="height: 280px;" class="flex items-center justify-center">
                    <Doughnut :data="paymentChartData" :options="paymentChartOptions" />
                </div>
            </div>
        </div>

        <!-- Second Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Bar Chart -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Penjualan per Kategori</h3>
                        <p class="text-sm text-gray-400 mt-0.5">Jumlah transaksi per kategori produk</p>
                    </div>
                </div>
                <div style="height: 280px;">
                    <Bar :data="categoryChartData" :options="categoryChartOptions" />
                </div>
            </div>

            <!-- Top Products -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Produk Terlaris</h3>
                        <p class="text-sm text-gray-400 mt-0.5">Top 5 produk dengan penjualan tertinggi</p>
                    </div>
                </div>
                <div v-if="topProducts.length > 0" class="space-y-4">
                    <div v-for="(product, index) in topProducts" :key="product.name" class="flex items-center space-x-4">
                        <div class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center text-sm font-bold"
                            :class="[
                                index === 0 ? 'bg-amber-100 text-amber-700' : '',
                                index === 1 ? 'bg-gray-100 text-gray-600' : '',
                                index === 2 ? 'bg-orange-100 text-orange-700' : '',
                                index > 2 ? 'bg-gray-50 text-gray-400' : '',
                            ]"
                        >
                            {{ index + 1 }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ product.name }}</p>
                            <div class="flex items-center space-x-3 mt-1">
                                <span class="text-xs text-gray-400">{{ product.sold }} terjual</span>
                                <span class="text-xs font-semibold text-emerald-600">{{ product.revenue }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-1.5 mt-2">
                                <div
                                    class="h-1.5 rounded-full transition-all duration-500"
                                    :class="[
                                        index === 0 ? 'bg-emerald-500' : '',
                                        index === 1 ? 'bg-blue-500' : '',
                                        index === 2 ? 'bg-violet-500' : '',
                                        index === 3 ? 'bg-amber-500' : '',
                                        index === 4 ? 'bg-pink-500' : '',
                                    ]"
                                    :style="{ width: product.progress + '%' }"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else class="text-center py-8 text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                    <p class="text-sm">Belum ada data penjualan</p>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Aktivitas Terakhir</h3>
                    <!-- <p class="text-sm text-gray-400 mt-0.5">Transaksi terbaru dari database</p> -->
                </div>
            </div>
            <div v-if="recentTransactions.length > 0" class="divide-y divide-gray-50">
                <div
                    v-for="activity in recentTransactions"
                    :key="activity.id"
                    class="flex items-center justify-between py-3.5 hover:bg-gray-50/50 -mx-2 px-2 rounded-lg transition-colors"
                >
                    <div class="flex items-center space-x-3 min-w-0">
                        <div class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center bg-emerald-50">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ activity.title }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ activity.time }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3 flex-shrink-0 ml-4">
                        <span class="text-sm font-semibold text-gray-800">{{ activity.amount }}</span>
                        <span :class="statusColor(activity.status)" class="text-xs font-medium px-2 py-1 rounded-full">
                            {{ statusLabel(activity.status) }}
                        </span>
                    </div>
                </div>
            </div>
            <div v-else class="text-center py-8 text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                <p class="text-sm">Belum ada transaksi tercatat</p>
            </div>
        </div>

        <!-- AI Discount Suggestions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-6">
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center space-x-3">
                    <div class="p-2.5 rounded-lg bg-gradient-to-br from-violet-500 to-purple-600 shadow-lg shadow-violet-200">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" /></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Rekomendasi Diskon AI</h3>
                        <p class="text-sm text-gray-400 mt-0.5">Saran diskon untuk produk deadstock & stok rendah</p>
                    </div>
                </div>
                <span v-if="discountSuggestions.length > 0" class="text-xs font-semibold px-2.5 py-1 rounded-full bg-violet-50 text-violet-600">
                    {{ discountSuggestions.length }} saran
                </span>
            </div>

            <div v-if="discountSuggestions.length > 0" class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left py-3 px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Produk</th>
                            <th class="text-left py-3 px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Kategori</th>
                            <th class="text-center py-3 px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Sisa Stok</th>
                            <th class="text-center py-3 px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Hari Tidak Laku</th>
                            <th class="text-center py-3 px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Diskon</th>
                            <th class="text-right py-3 px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Harga Asli</th>
                            <th class="text-right py-3 px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Harga Diskon</th>
                            <th class="text-left py-3 px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="item in discountSuggestions" :key="item.id" class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-3.5 px-3">
                                <p class="font-medium text-gray-800">{{ item.product_name }}</p>
                                <p v-if="item.note" class="text-xs text-gray-400 mt-0.5 max-w-xs truncate">{{ item.note }}</p>
                            </td>
                            <td class="py-3.5 px-3">
                                <span class="text-xs font-medium px-2 py-1 rounded-full bg-blue-50 text-blue-600">{{ item.category }}</span>
                            </td>
                            <td class="py-3.5 px-3 text-center">
                                <span :class="[
                                    'text-sm font-semibold',
                                    item.stock_remaining <= 0 ? 'text-red-600' : item.stock_remaining <= 2 ? 'text-orange-500' : item.stock_remaining < 5 ? 'text-amber-500' : item.stock_remaining > 20 ? 'text-red-500' : 'text-gray-600'
                                ]">
                                    {{ item.stock_remaining <= 0 ? 'Habis' : item.stock_remaining }}
                                </span>
                            </td>
                            <td class="py-3.5 px-3 text-center">
                                <span v-if="item.days_inactive" :class="[
                                    'text-xs font-semibold px-2 py-1 rounded-full',
                                    item.days_inactive >= 60 ? 'bg-red-50 text-red-600' : item.days_inactive >= 45 ? 'bg-amber-50 text-amber-600' : 'bg-yellow-50 text-yellow-600'
                                ]">
                                    {{ item.days_inactive }} hari
                                </span>
                                <span v-else class="text-gray-400">-</span>
                            </td>
                            <td class="py-3.5 px-3 text-center">
                                <span class="inline-flex items-center space-x-1 text-sm font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" /></svg>
                                    <span>{{ item.discount_percent }}%</span>
                                </span>
                            </td>
                            <td class="py-3.5 px-3 text-right">
                                <span class="text-gray-400 line-through">{{ item.original_price }}</span>
                            </td>
                            <td class="py-3.5 px-3 text-right">
                                <span class="font-semibold text-emerald-600">{{ item.discounted_price }}</span>
                            </td>
                            <td class="py-3.5 px-3">
                                <span :class="[
                                    'text-xs font-medium px-2 py-1 rounded-full',
                                    item.status === 'deadstock' ? 'bg-red-50 text-red-600' : '',
                                    item.status === 'out_of_stock' ? 'bg-gray-800 text-white' : '',
                                    item.status === 'low_stock' ? 'bg-amber-50 text-amber-600' : '',
                                    !['deadstock','out_of_stock','low_stock'].includes(item.status) ? 'bg-gray-100 text-gray-600' : ''
                                ]">
                                    {{ item.status === 'deadstock' ? 'Deadstock' : item.status === 'out_of_stock' ? 'Stok Habis' : item.status === 'low_stock' ? 'Stok Rendah' : item.status }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-else class="text-center py-10">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-violet-50 flex items-center justify-center">
                    <svg class="w-8 h-8 text-violet-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" /></svg>
                </div>
                <p class="text-sm text-gray-400 font-medium">Belum ada rekomendasi diskon dari AI</p>
                <p class="text-xs text-gray-300 mt-1">Sistem akan otomatis menganalisis produk deadstock</p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>