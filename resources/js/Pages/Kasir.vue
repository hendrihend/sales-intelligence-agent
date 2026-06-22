<script setup>
import AuthenticatedLayout from '@/Layouts/Sidebar.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    transaction: {
        type: Object,
        default: null
    },
    searchQuery: {
        type: String,
        default: ''
    }
});

const searchInput = ref(props.searchQuery || '');
const isSearching = ref(false);

const formatPrice = (v) => {
    return v ? parseInt(v).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '0';
};

const formatDate = (d) => {
    return new Date(d).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' });
};

const searchTransaction = () => {
    if (!searchInput.value.trim()) return;
    
    isSearching.value = true;
    router.get('/kasir', { search_id: searchInput.value }, {
        preserveState: true,
        preserveScroll: true,
        onFinish: () => isSearching.value = false
    });
};

const isProcessing = ref(false);

const processPayment = (method) => {
    if (!props.transaction) return;
    
    if (confirm(`Konfirmasi pembayaran sebesar Rp ${formatPrice(props.transaction.total)} menggunakan ${method.toUpperCase()}?`)) {
        isProcessing.value = true;
        router.post(`/kasir/${props.transaction.id}/pay`, { payment_method: method }, {
            preserveScroll: true,
            onFinish: () => isProcessing.value = false
        });
    }
};

// Update searchInput when prop changes (in case of back navigation)
watch(() => props.searchQuery, (newVal) => {
    if (newVal !== searchInput.value) {
        searchInput.value = newVal || '';
    }
});
</script>

<template>
    <Head title="Cek Transaksi Kasir" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Cek Transaksi Kasir</h2>
        </template>

        <div class="max-w-4xl mx-auto py-6">
            <!-- Search Box -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8 transition-all hover:shadow-md">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    Pencarian ID Transaksi
                </h3>
                <form @submit.prevent="searchTransaction" class="flex flex-col sm:flex-row gap-4">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="text-gray-400 font-medium">#</span>
                        </div>
                        <input
                            v-model="searchInput"
                            type="text"
                            placeholder="Masukkan ID Transaksi (Contoh: TRX-IN001)"
                            class="w-full pl-10 pr-4 py-3.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all text-gray-800 font-medium"
                            required
                        />
                    </div>
                    <button type="submit" :disabled="isSearching" class="px-8 py-3.5 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-semibold rounded-xl hover:shadow-lg hover:-translate-y-0.5 transition-all disabled:opacity-70 disabled:hover:translate-y-0 flex items-center justify-center gap-2 min-w-[140px]">
                        <span v-if="isSearching" class="flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Mencari...
                        </span>
                        <span v-else>Cari Data</span>
                    </button>
                </form>
            </div>

            <!-- Result Box -->
            <transition name="fade" mode="out-in">
                <div v-if="transaction" :key="transaction.id" class="bg-white rounded-2xl shadow-lg border border-gray-100 relative overflow-hidden">
                    <!-- Decorator Line -->
                    <div class="h-2 w-full bg-gradient-to-r from-emerald-400 via-teal-400 to-emerald-500"></div>
                    
                    <div class="p-8 md:p-10">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 pb-6 border-b border-gray-100 gap-4">
                            <div>
                                <h3 class="text-3xl font-bold text-gray-800 tracking-tight">INVOICE</h3>
                                <p class="text-emerald-600 font-semibold mt-1">ID Transaksi: #{{ transaction.formatted_id || transaction.id }}</p>
                            </div>
                            <div class="text-left md:text-right">
                                <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-1">Tanggal Terbit</p>
                                <p class="text-gray-800 font-medium">{{ formatDate(transaction.transaction_date) }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                            <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                                <p class="text-xs text-gray-400 uppercase tracking-wider font-bold mb-3 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                    Kasir Bertugas
                                </p>
                                <p class="text-lg text-gray-800 font-bold">{{ transaction.cashier?.name || 'Kasir Toko' }}</p>
                                <p class="text-sm text-gray-500 capitalize">{{ transaction.cashier?.role || 'Cashier' }}</p>
                            </div>
                            <div :class="[
                                'rounded-xl p-5 border flex flex-col justify-center items-end',
                                transaction.status === 'pending' ? 'bg-amber-50 border-amber-100' : 'bg-emerald-50 border-emerald-100'
                            ]">
                                <p :class="[
                                    'text-xs uppercase tracking-wider font-bold mb-1',
                                    transaction.status === 'pending' ? 'text-amber-600/70' : 'text-emerald-600/70'
                                ]">Status Pembayaran</p>
                                <div :class="[
                                    'px-4 py-1.5 text-white rounded-full text-sm font-bold tracking-wide shadow-sm inline-block uppercase',
                                    transaction.status === 'pending' ? 'bg-amber-500' : 'bg-emerald-500'
                                ]">
                                    {{ transaction.status === 'pending' ? 'PENDING' : 'LUNAS' }}
                                </div>
                                <p v-if="transaction.payment_method && transaction.payment_method !== 'unpaid'" class="text-xs font-semibold mt-2 text-gray-500 capitalize">Via {{ transaction.payment_method }}</p>
                            </div>
                        </div>

                        <div class="mb-10">
                            <p class="text-sm text-gray-800 font-bold mb-4 uppercase tracking-wider border-l-4 border-emerald-500 pl-3">Detail Item</p>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="bg-gray-50/50">
                                            <th class="py-4 px-4 font-semibold text-gray-500 text-xs uppercase tracking-wider rounded-tl-lg">Produk</th>
                                            <th class="py-4 px-4 font-semibold text-gray-500 text-xs uppercase tracking-wider text-center">Kuantitas</th>
                                            <th class="py-4 px-4 font-semibold text-gray-500 text-xs uppercase tracking-wider text-right">Harga Satuan</th>
                                            <th class="py-4 px-4 font-semibold text-gray-500 text-xs uppercase tracking-wider text-right rounded-tr-lg">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <tr v-for="item in transaction.items" :key="item.id" class="hover:bg-gray-50/30 transition-colors">
                                            <td class="py-4 px-4 text-gray-800 font-medium">{{ item.product?.name || 'Produk Tidak Diketahui' }}</td>
                                            <td class="py-4 px-4 text-gray-600 text-center">{{ item.qty }}</td>
                                            <td class="py-4 px-4 text-gray-600 text-right">Rp {{ formatPrice(item.price) }}</td>
                                            <td class="py-4 px-4 text-gray-800 font-semibold text-right">Rp {{ formatPrice(item.subtotal) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="flex flex-col md:flex-row justify-between items-center gap-6 bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-6 md:p-8 border border-gray-200">
                            <div class="text-center md:text-left">
                                <p class="text-sm text-gray-500 mb-1">Terima kasih atas kunjungan Anda!</p>
                                <p class="text-xs text-gray-400">Harap simpan struk ini sebagai bukti pembelian.</p>
                            </div>
                            <div class="text-center md:text-right bg-white p-5 rounded-xl shadow-sm border border-emerald-100 min-w-[240px]">
                                <p class="text-xs text-emerald-600 font-bold uppercase tracking-wider mb-1">Total Pembayaran</p>
                                <p class="text-3xl font-extrabold text-gray-900 tracking-tight">Rp {{ formatPrice(transaction.total) }}</p>
                            </div>
                        </div>

                        <!-- Action Buttons for Cashier -->
                        <div v-if="transaction.status === 'pending'" class="mt-8 border-t border-gray-100 pt-8">
                            <h4 class="text-center text-gray-600 font-bold uppercase tracking-wide mb-4">Selesaikan Pembayaran</h4>
                            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                                <button 
                                    @click="processPayment('cash')" 
                                    :disabled="isProcessing"
                                    class="px-8 py-3.5 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold rounded-xl shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all disabled:opacity-50 flex items-center justify-center gap-2"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                    Bayar Tunai (Cash)
                                </button>
                                <button 
                                    @click="processPayment('debit')" 
                                    :disabled="isProcessing"
                                    class="px-8 py-3.5 bg-gradient-to-r from-blue-500 to-indigo-500 text-white font-bold rounded-xl shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all disabled:opacity-50 flex items-center justify-center gap-2"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                                    Kartu Debit/Kredit
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Not Found Box -->
                <div v-else-if="searchQuery && !isSearching" key="not-found" class="bg-white rounded-2xl shadow-sm border border-red-100 p-12 text-center transition-all">
                    <div class="w-24 h-24 bg-red-50 text-red-400 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner border border-red-100">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.27 16.5c-.77.833.192 2.5 1.732 2.5z" /></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">Transaksi Tidak Ditemukan</h3>
                    <p class="text-gray-500 max-w-md mx-auto">Kami tidak dapat menemukan data untuk ID Transaksi <span class="font-bold text-gray-700">#{{ searchQuery }}</span>. Pastikan ID yang Anda masukkan sudah benar.</p>
                </div>
            </transition>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease, transform 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(10px);
}
</style>
