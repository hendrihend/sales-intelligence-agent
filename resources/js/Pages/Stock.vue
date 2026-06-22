<script setup>
import AuthenticatedLayout from '@/Layouts/Sidebar.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const searchQuery = ref('');

const props = defineProps({
    stockItems: {
        type: Array,
        default: () => [],
    }
});

const filteredItems = () => {
    if (!searchQuery.value) return props.stockItems;
    return props.stockItems.filter(item =>
        item.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        item.sku.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};

const statusLabel = (status) => {
    const map = { available: 'Tersedia', low: 'Stok Rendah', empty: 'Habis' };
    return map[status] || status;
};

const statusClass = (status) => {
    const map = {
        available: 'bg-emerald-50 text-emerald-700 border-emerald-200',
        low: 'bg-amber-50 text-amber-700 border-amber-200',
        empty: 'bg-red-50 text-red-700 border-red-200',
    };
    return map[status] || '';
};

const totalItems = () => props.stockItems.reduce((sum, item) => sum + item.qty, 0);
const lowStockCount = () => props.stockItems.filter(i => i.status === 'low').length;
const emptyStockCount = () => props.stockItems.filter(i => i.status === 'empty').length;
const totalValue = () => props.stockItems.reduce((sum, item) => sum + (item.qty * item.price), 0);
</script>

<template>
    <Head title="Stock" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Stock</h2>
                <button class="inline-flex items-center px-4 py-2 bg-emerald-500 text-white text-sm font-medium rounded-lg hover:bg-emerald-600 transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    Tambah Item
                </button>
            </div>
        </template>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-6">
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Item</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ totalItems().toLocaleString('id-ID') }}</h3>
                    </div>
                    <div class="p-3 bg-blue-50 rounded-lg">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Nilai Inventaris</p>
                        <h3 class="text-xl font-bold text-gray-900 mt-1">{{ formatCurrency(totalValue()) }}</h3>
                    </div>
                    <div class="p-3 bg-emerald-50 rounded-lg">
                        <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Stok Rendah</p>
                        <h3 class="text-2xl font-bold text-amber-600 mt-1">{{ lowStockCount() }}</h3>
                    </div>
                    <div class="p-3 bg-amber-50 rounded-lg">
                        <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.27 16.5c-.77.833.192 2.5 1.732 2.5z" /></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Stok Habis</p>
                        <h3 class="text-2xl font-bold text-red-600 mt-1">{{ emptyStockCount() }}</h3>
                    </div>
                    <div class="p-3 bg-red-50 rounded-lg">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search & Filter -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="relative flex-1">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Cari item berdasarkan nama atau SKU..."
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all"
                    />
                </div>
                <select class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm text-gray-600 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                    <option>Semua Kategori</option>
                    <option>Elektronik</option>
                    <option>Aksesoris</option>
                    <option>Komponen</option>
                </select>
                <select class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm text-gray-600 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                    <option>Semua Status</option>
                    <option>Tersedia</option>
                    <option>Stok Rendah</option>
                    <option>Habis</option>
                </select>
            </div>
        </div>

        <!-- Stock Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left py-3.5 px-5 font-semibold text-gray-500 uppercase text-xs tracking-wider">Item</th>
                            <th class="text-left py-3.5 px-5 font-semibold text-gray-500 uppercase text-xs tracking-wider">SKU</th>
                            <th class="text-left py-3.5 px-5 font-semibold text-gray-500 uppercase text-xs tracking-wider">Kategori</th>
                            <th class="text-right py-3.5 px-5 font-semibold text-gray-500 uppercase text-xs tracking-wider">Qty</th>
                            <th class="text-right py-3.5 px-5 font-semibold text-gray-500 uppercase text-xs tracking-wider">Harga</th>
                            <th class="text-center py-3.5 px-5 font-semibold text-gray-500 uppercase text-xs tracking-wider">Status</th>
                            <th class="text-center py-3.5 px-5 font-semibold text-gray-500 uppercase text-xs tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="item in filteredItems()" :key="item.id" class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-3.5 px-5 font-medium text-gray-800">{{ item.name }}</td>
                            <td class="py-3.5 px-5 text-gray-500 font-mono text-xs">{{ item.sku }}</td>
                            <td class="py-3.5 px-5 text-gray-500">{{ item.category }}</td>
                            <td class="py-3.5 px-5 text-right font-semibold" :class="item.qty === 0 ? 'text-red-600' : 'text-gray-800'">{{ item.qty }}</td>
                            <td class="py-3.5 px-5 text-right">
                                <div v-if="item.discountPercent > 0">
                                    <span class="text-xs text-gray-400 line-through mr-1.5">{{ formatCurrency(item.price) }}</span>
                                    <span class="text-emerald-600 font-bold">{{ formatCurrency(item.finalPrice) }}</span>
                                    <div class="mt-0.5 inline-flex items-center space-x-1 px-1.5 py-0.5 rounded-md bg-emerald-50 text-emerald-600 border border-emerald-100">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" /></svg>
                                        <span class="text-[10px] font-bold">{{ item.discountPercent }}% Diskon AI</span>
                                    </div>
                                </div>
                                <div v-else class="text-gray-600 font-medium">
                                    {{ formatCurrency(item.price) }}
                                </div>
                            </td>
                            <td class="py-3.5 px-5 text-center">
                                <span :class="statusClass(item.status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border">
                                    {{ statusLabel(item.status) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-5 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <button class="p-1.5 text-gray-400 hover:text-blue-500 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </button>
                                    <button class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
