<script setup>
import AuthenticatedLayout from '@/Layouts/Sidebar.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
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
const imagePreview = ref(null);

// State Modal Tambah Stok
const isAddStockModalOpen = ref(false);
const stockToAdd = ref(1);
const stockToEditId = ref(null);
const stockToEditName = ref('');
const stockToEditCurrent = ref(0);

const addStockForm = useForm({
    name: '',
    category: '',
    purchase_price: 0,
    price: 0,
    stock: 0,
});

// Form menggunakan struktur harga baru
const form = useForm({
    name: '',
    category: '',
    purchase_price: 0, // Harga Beli dari Distributor
    price: 0,          // Harga Jual ke Customer
    stock: 0,
    image: null
});

const handleImageChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.image = file;
        imagePreview.value = URL.createObjectURL(file);
    }
};

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

// URL Gambar Produk
const getProductImage = (product) => {
    return product.image ? '/images/products/' + product.image : 'https://images.unsplash.com/photo-1587829191301-32b86b2b94f5?w=100&h=100&fit=crop';
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
    imagePreview.value = null;
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
    form.image = null; // Reset file, hanya kirim jika user pilih file baru
    imagePreview.value = product.image ? '/images/products/' + product.image : null;
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    form.reset();
    imagePreview.value = null;
};

const submitForm = () => {
    // jadikan input kategori menjadi huruf kapital
    if (form.category){
        form.category = form.category.toUpperCase();
    }
    
    if (isEditMode.value) {
        form.post(route('stocks.update.post', currentProductId.value), {
            forceFormData: true,
            onSuccess: () => {
                closeModal();
                triggerToast('Data produk berhasil diperbarui!');
            }
        });
    } else {
        form.post(route('stocks.store'), {
            forceFormData: true,
            onSuccess: () => {
                closeModal();
                triggerToast('Produk baru berhasil ditambahkan!');
            }
        });
    }
};

// Navigasi & Submit Tambah Stok Cepat
const openAddStockModal = (product) => {
    stockToEditId.value = product.id;
    stockToEditName.value = product.name;
    stockToEditCurrent.value = product.stock;
    stockToAdd.value = 1;

    addStockForm.name = product.name;
    addStockForm.category = product.category ?? '';
    addStockForm.purchase_price = product.purchase_price ?? 0;
    addStockForm.price = product.price;
    addStockForm.stock = product.stock;
    isAddStockModalOpen.value = true;
};

const closeAddStockModal = () => {
    isAddStockModalOpen.value = false;
};

const submitAddStock = () => {
    addStockForm.stock = stockToEditCurrent.value + stockToAdd.value;
    if (addStockForm.category){
        addStockForm.category = addStockForm.category.toUpperCase();
    }
    
    addStockForm.post(route('stocks.update.post', stockToEditId.value), {
        forceFormData: true,
        onSuccess: () => {
            closeAddStockModal();
            triggerToast(`Berhasil menambahkan ${stockToAdd.value} stok untuk ${stockToEditName.value}!`);
        }
    });
};
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
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img :src="getProductImage(product)" class="w-10 h-10 rounded object-cover border border-gray-200" />
                                        <span class="text-[#111827] font-medium">{{ product.name }}</span>
                                    </div>
                                </td>
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

                                <td class="px-6 py-4 flex items-center space-x-2">
                                    <button @click="openEditModal(product)" class="text-xs font-medium text-blue-600 hover:text-blue-800 bg-blue-50/50 hover:bg-blue-100 px-3 py-1.5 rounded transition">
                                        Ubah
                                    </button>
                                    <button @click="openAddStockModal(product)" class="text-xs font-medium text-emerald-600 hover:text-emerald-800 bg-emerald-50/50 hover:bg-emerald-100 px-3 py-1.5 rounded transition flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        Stok
                                    </button>
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
                            <input v-model="form.purchase_price" type="number" min="0" max="1000000000000" required class="w-full text-sm border-gray-200 rounded-md focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Harga Jual (Customer)</label>
                            <input v-model="form.price" type="number" min="0" max="1000000000000" required class="w-full text-sm border-gray-200 rounded-md focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Stok</label>
                        <input v-model="form.stock" type="number" min="0" required class="w-full text-sm border-gray-200 rounded-md focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 w-1/2" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Gambar Produk (Opsional)</label>
                        <div class="flex items-center gap-4">
                            <div v-if="imagePreview" class="w-16 h-16 rounded-lg overflow-hidden border border-gray-200 flex-shrink-0">
                                <img :src="imagePreview" class="w-full h-full object-cover" />
                            </div>
                            <div class="flex-1">
                                <input type="file" accept="image/*" @change="handleImageChange" class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 cursor-pointer" />
                                <p class="text-[10px] text-gray-400 mt-1">Format: JPG, PNG, WebP. Maks 2MB.</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 pt-5 border-t border-gray-100 mt-6">
                        <button type="button" @click="closeModal" class="px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-md transition">Batal</button>
                        <button type="submit" :disabled="form.processing" class="px-4 py-2 text-sm font-medium text-white bg-[#4F46E5] hover:bg-indigo-700 rounded-md shadow-sm disabled:opacity-50 transition">
                            {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Tambah Stok Cepat -->
        <div v-if="isAddStockModalOpen" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm flex items-center justify-center z-50 p-4 transition-opacity">
            <div class="bg-white rounded-xl shadow-2xl max-w-sm w-full p-6 border border-gray-100 text-center">
                <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </div>
                <h3 class="text-lg font-bold text-[#111827]">Tambah Stok</h3>
                <p class="text-sm text-gray-500 mt-1 mb-5">
                    <span class="font-semibold text-gray-800">{{ stockToEditName }}</span><br/>
                    Stok saat ini: <span class="font-bold">{{ stockToEditCurrent }} pcs</span>
                </p>
                <form @submit.prevent="submitAddStock" class="space-y-4 text-left">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2 text-center">Jumlah Ditambahkan</label>
                        <div class="flex items-center justify-center gap-3">
                            <button type="button" @click="stockToAdd > 1 ? stockToAdd-- : null" class="w-10 h-10 rounded-full border border-gray-200 flex items-center justify-center text-gray-600 hover:bg-gray-50 active:bg-gray-100 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" /></svg>
                            </button>
                            <input v-model="stockToAdd" type="number" min="1" required class="w-24 text-center text-xl font-bold border-gray-200 rounded-lg focus:border-emerald-500 focus:ring-emerald-500 py-2" />
                            <button type="button" @click="stockToAdd++" class="w-10 h-10 rounded-full border border-gray-200 flex items-center justify-center text-gray-600 hover:bg-gray-50 active:bg-gray-100 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                            </button>
                        </div>
                    </div>
                    <div class="flex justify-center space-x-3 pt-4 border-t border-gray-100 mt-6">
                        <button type="button" @click="closeAddStockModal" class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg transition w-1/2">Batal</button>
                        <button type="submit" :disabled="addStockForm.processing" class="px-5 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 rounded-lg shadow-md disabled:opacity-50 transition w-1/2 flex justify-center items-center gap-2">
                            <svg v-if="addStockForm.processing" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            {{ addStockForm.processing ? 'Menyimpan...' : 'Simpan' }}
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