<script setup>
import AuthenticatedLayout from '@/Layouts/Sidebar.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    products: {
        type: Array,
        default: () => []
    }
});

// State Filter
const searchQuery = ref('');
const selectedCategory = ref('');
const selectedStatus = ref('');

// State Modal CRUD & Toast
const isModalOpen = ref(false);
const isEditMode = ref(false);
const currentProductId = ref(null);
const showToast = ref(false);
const toastMessage = ref('');

// Form menggunakan struktur harga baru
const form = useForm({
    name: '',
    category: '',
    purchase_price: 0, // Harga Beli dari Distributor
    price: 0,          // Harga Jual ke Customer
    stock: 0
});

// Dropdown Dinamis
const categories = computed(() => {
    const cats = props.products.map(p => p.category).filter(Boolean);
    return [...new Set(cats)];
});

const statuses = computed(() => {
    const stats = props.products.map(p => p.ai_status).filter(Boolean);
    return [...new Set(stats)];
});

// Filter Data
const filteredProducts = computed(() => {
    return props.products.filter(product => {
        const matchesSearch = product.name.toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchesCategory = !selectedCategory.value || product.category === selectedCategory.value;
        const matchesStatus = !selectedStatus.value || product.ai_status === selectedStatus.value;
        return matchesSearch && matchesCategory && matchesStatus;
    });
});

// Format Rupiah Otomatis
const formatRupiah = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(value || 0);
};

// Fungsi Menghitung Nominal Diskon (Berdasarkan Status AI)
const calculateDiscount = (sellingPrice, aiStatus) => {
    if (aiStatus === 'Deadstock') return sellingPrice * 0.20; // Diskon 20%
    if (aiStatus === 'Slow-Moving') return sellingPrice * 0.10; // Diskon 10%
    return 0; // Tidak ada diskon
};

// Toast Notifikasi
const triggerToast = (message) => {
    toastMessage.value = message;
    showToast.value = true;
    setTimeout(() => { showToast.value = false; }, 3000);
};

// Navigasi Modal
const openAddModal = () => {
    isEditMode.value = false;
    currentProductId.value = null;
    form.reset();
    isModalOpen.value = true;
};

const openEditModal = (product) => {
    isEditMode.value = true;
    currentProductId.value = product.id;
    form.name = product.name;
    form.category = product.category ?? '';
    form.purchase_price = product.purchase_price ?? 0; // Antisipasi jika kolom belum ada di DB
    form.price = product.price;
    form.stock = product.stock;
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    form.reset();
};

const submitForm = () => {
    // jadikan input kategori menjadi huruf kapital
    if (form.category){
        form.category = form.category.toUpperCase();
    }
    
    if (isEditMode.value) {
        form.put(route('stocks.update', currentProductId.value), {
            onSuccess: () => {
                closeModal();
                triggerToast('Data produk berhasil diperbarui!');
            }
        });
    } else {
        form.post(route('stocks.store'), {
            onSuccess: () => {
                closeModal();
                triggerToast('Produk baru berhasil ditambahkan!');
            }
        });
    }
};

// const deleteProduct = (id) => {
//     if (confirm('Apakah Anda yakin ingin menghapus produk ini?')) {
//         form.delete(route('stocks.destroy', id), {
//             onSuccess: () => triggerToast('Produk telah dihapus!')
//         });
//     }
// };
</script>

<template>
    <Head title="Stocks" />

    <AuthenticatedLayout>
        <div class="p-8 bg-gray-50 min-h-screen font-sans">
            <div class="max-w-7xl mx-auto">
            
                <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <h1 class="text-[22px] font-semibold text-[#1F2937] tracking-tight">Sales Intelligence Manajemen Stok</h1>
                    <button @click="openAddModal" class="px-5 py-2.5 bg-[#4F46E5] text-white font-medium text-sm rounded hover:bg-indigo-700 transition shadow-sm">
                        + Tambah Produk Baru
                    </button>
                </div>

                <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-6 bg-white p-5 rounded-lg border border-gray-100 shadow-sm">
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-widest mb-2">Cari Nama Produk</label>
                        <input v-model="searchQuery" type="text" placeholder="Masukkan nama barang..." class="w-full text-sm border-gray-200 rounded-md focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2"/>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-widest mb-2">Filter Kategori</label>
                        <select v-model="selectedCategory" class="w-full text-sm border-gray-200 rounded-md focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 text-gray-700">
                            <option value="">Semua Kategori</option>
                            <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-widest mb-2">Filter Status Intelijen</label>
                        <select v-model="selectedStatus" class="w-full text-sm border-gray-200 rounded-md focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 text-gray-700">
                            <option value="">Semua Status</option>
                            <option v-for="stat in statuses" :key="stat" :value="stat">{{ stat }}</option>
                        </select>
                    </div>
                </div>

                <div class="bg-white rounded-lg border border-gray-100 shadow-sm overflow-hidden">
                    <table class="min-w-full text-left text-sm text-[#4B5563]">
                        <thead class="bg-[#F8FAFC] text-[11px] font-bold text-gray-500 uppercase tracking-widest border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4">Nama Produk</th>
                                <th class="px-6 py-4">Kategori</th>
                                <th class="px-6 py-4">Stok</th>
                                <th class="px-6 py-4">Terjual</th>
                                <th class="px-6 py-4">Harga Beli</th>
                                <th class="px-6 py-4">Harga Jual</th>
                                <!-- <th class="px-6 py-4">Potensi Diskon (AI)</th> -->
                                <th class="px-6 py-4">Opsi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-if="filteredProducts.length === 0">
                                <td colspan="8" class="px-6 py-8 text-center text-gray-400">Tidak ada data produk.</td>
                            </tr>
                            
                            <tr v-else v-for="product in filteredProducts" :key="product.id" class="hover:bg-gray-50/50 transition duration-150">
                                <td class="px-6 py-4 text-[#111827] font-medium">{{ product.name }}</td>
                                <td class="px-6 py-4">{{ product.category ?? '-' }}</td>
                                <td class="px-6 py-4">{{ product.stock ?? 0 }} pcs</td>
                                <td class="px-6 py-4">{{ product.units_sold_30_days ?? 0 }} pcs</td>
                                <td class="px-6 py-4 text-gray-400">{{ formatRupiah(product.purchase_price) }}</td>
                                <td class="px-6 py-4 font-medium text-green-600">{{ formatRupiah(product.price) }}</td>
                                
                                <!-- <td class="px-6 py-4">
                                    <div v-if="calculateDiscount(product.price, product.ai_status) > 0" class="flex flex-col">
                                        <span class="text-red-500 font-semibold">- {{ formatRupiah(calculateDiscount(product.price, product.ai_status)) }}</span>
                                        <span class="text-[10px] text-gray-400 mt-0.5">({{ product.ai_status }})</span>
                                    </div>
                                    <span v-else class="text-gray-300">-</span>
                                </td> -->

                                <td class="px-6 py-4 flex items-center space-x-3">
                                    <button @click="openEditModal(product)" class="text-xs font-medium text-blue-600 hover:text-blue-800 bg-blue-50/50 hover:bg-blue-100 px-3 py-1.5 rounded transition">
                                        Ubah
                                    </button>
                                    <!-- <button @click="deleteProduct(product.id)" class="text-xs font-medium text-red-500 hover:text-red-700 hover:bg-red-50 px-2 py-1.5 rounded transition">
                                        Hapus
                                    </button> -->
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div v-if="isModalOpen" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm flex items-center justify-center z-40 p-4 transition-opacity">
            <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full p-6 border border-gray-100">
                <h3 class="text-lg font-bold text-[#111827] mb-5">
                    {{ isEditMode ? 'Ubah Data Produk' : 'Tambah Produk Baru' }}
                </h3>
                <form @submit.prevent="submitForm" class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nama Produk</label>
                        <input v-model="form.name" type="text" required class="w-full text-sm border-gray-200 rounded-md focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Kategori</label>
                        <input 
                            v-model="form.category" 
                            list="category-options"
                            placeholder="Pilih atau ketik kategori baru..." 
                            class="w-full text-sm border-gray-200 rounded-md focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 text-gray-700" />
                        <datalist id="category-options">
                            <option v-for="cat in categories" :key="cat" :value="cat"></option>
                        </datalist>

                        <span v-if="form.errors.category" class="text-xs text-red-500 mt-1 block">{{ form.errors.category }}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Harga Beli (Distributor)</label>
                            <input v-model="form.purchase_price" type="number" min="0" required class="w-full text-sm border-gray-200 rounded-md focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Harga Jual (Customer)</label>
                            <input v-model="form.price" type="number" min="0" required class="w-full text-sm border-gray-200 rounded-md focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Stok Saat Ini</label>
                        <input v-model="form.stock" type="number" min="0" required class="w-full text-sm border-gray-200 rounded-md focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 w-1/2" />
                    </div>
                    <div class="flex justify-end space-x-3 pt-5 border-t border-gray-100 mt-6">
                        <button type="button" @click="closeModal" class="px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-md transition">Batal</button>
                        <button type="submit" :disabled="form.processing" class="px-4 py-2 text-sm font-medium text-white bg-[#4F46E5] hover:bg-indigo-700 rounded-md shadow-sm disabled:opacity-50 transition">
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <transition name="toast-slide">
            <div v-if="showToast" class="fixed bottom-6 right-6 z-50 flex items-center bg-[#10B981] text-white px-5 py-3 rounded-lg shadow-xl border border-green-400">
                <svg class="w-5 h-5 mr-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <p class="text-sm font-medium tracking-wide">{{ toastMessage }}</p>
            </div>
        </transition>
    </AuthenticatedLayout>
</template>

<style scoped>
.toast-slide-enter-active, .toast-slide-leave-active { transition: all 0.3s ease; }
.toast-slide-enter-from, .toast-slide-leave-to { opacity: 0; transform: translateY(20px); }
</style>