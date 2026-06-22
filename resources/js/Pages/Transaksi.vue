<script setup>
import AuthenticatedLayout from '@/Layouts/Sidebar.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, onMounted, watch, computed } from 'vue';

const page = usePage();

const transactions = ref([]);
const loading = ref(false);

const props = defineProps({
    products: {
        type: Array,
        default: () => [],
    }
});

const selectedCategory = ref('');

const categories = computed(() => {
    const cats = new Set(props.products.map(p => p.category));
    return Array.from(cats).filter(Boolean).sort();
});

const filteredProducts = computed(() => {
    if (!selectedCategory.value) return props.products;
    return props.products.filter(p => p.category === selectedCategory.value);
});

function productImage(p) {
    // use local image from public/images/products/
    return p.image || '/images/products/placeholder.svg';
}

function formatPrice(v) {
    return v.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

const cart = ref([]);
const checkoutOpen = ref(false);
const pinned = ref(false);
const showPoPopup = ref(false);
const currentPo = ref(null);
const form = ref({
    customer_name: '',
    customer_phone: '',
    customer_email: '',
    customer_city: '',
    description: '',
    payment_method: 'qris'
});

const notifications = ref([]);

function addToCart(p) {
    const existing = cart.value.find(i => i.id === p.id);
    if (existing) existing.qty++;
    else cart.value.push({ ...p, qty: 1 });
    checkoutOpen.value = true;
}

function removeFromCart(i) {
    cart.value.splice(i, 1);
}

function cartTotal() {
    return cart.value.reduce((s, it) => s + (it.price || 0) * (it.qty || 1), 0);
}

async function loadNotifications() {
    try {
        const res = await fetch('/api/notifications');
        if (res.ok) {
            const data = await res.json();
            notifications.value = data.data || data;
        }
    } catch (e) { console.error(e); }
}

async function loadTransactions() {
    loading.value = true;
    try {
        const res = await fetch('/api/transaksi');
        if (res.ok) {
            const data = await res.json();
            transactions.value = data.data || data;
        } else {
            console.error('Failed to fetch transactions');
        }
    } catch (e) {
        console.error(e);
    } finally {
        loading.value = false;
    }
}

async function submit() {
    try {
        const payload = { ...form.value };
        payload.amount = parseFloat(payload.amount) || 0;
        const res = await fetch('/api/transaksi', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload),
        });
        if (res.status === 201) {
            form.value = { customer_name: '', customer_phone: '', customer_email: '', customer_city: '', amount: '', type: 'sale', description: '' };
            await loadTransactions();
            await loadNotifications();
        } else {
            const err = await res.json();
            console.error(err);
            alert('Gagal menyimpan transaksi');
        }
    } catch (e) {
        console.error(e);
        alert('Gagal mengirim permintaan');
    }
}

function submitCheckout() {
    if (!cart.value.length) {
        alert('Keranjang kosong');
        return;
    }

    const payload = {
        customer_name: form.value.customer_name,
        payment_method: form.value.payment_method,
        items: cart.value.map(i => ({ id: i.id, price: i.price, qty: i.qty })),
    };

    // Kirim data ke backend menggunakan Inertia router
    router.post('/transaksi', payload, {
        preserveScroll: true,
        onSuccess: () => {
            const flash = page.props.flash || {};
            if (flash.po_popup) {
                // Set data untuk popup PO berdasarkan flash message
                currentPo.value = {
                    id: flash.po_popup.transaction_id,
                    customer_name: form.value.customer_name || 'Pelanggan Umum',
                    payment_method: form.value.payment_method,
                    status: form.value.payment_method === 'qris' ? 'Lunas' : 'Pending',
                    items: [...cart.value],
                    total: flash.po_popup.total,
                    date: new Date().toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' })
                };

                // Tampilkan popup
                showPoPopup.value = true;

                // Reset keranjang dan form
                cart.value = [];
                checkoutOpen.value = false;
                form.value = { customer_name: '', customer_phone: '', customer_email: '', customer_city: '', description: '', payment_method: 'qris' };
            }
        },
        onError: (errors) => {
            console.error(errors);
            alert('Terjadi kesalahan saat memproses pembayaran. Periksa inputan Anda dan pastikan form diisi dengan benar.');
        }
    });
}

onMounted(() => { loadTransactions(); loadNotifications(); });

// open checkout by default on larger screens for two-column layout
onMounted(() => {
    try {
        if (window.innerWidth >= 768) checkoutOpen.value = true;
    } catch (e) {}
});

// load pinned state from localStorage
onMounted(() => {
    try {
        const p = localStorage.getItem('transaksi_checkout_pinned');
        if (p === '1') {
            pinned.value = true;
            checkoutOpen.value = true;
        }
    } catch (e) { /* ignore */ }
});

watch(pinned, (v) => {
    try {
        localStorage.setItem('transaksi_checkout_pinned', v ? '1' : '0');
        if (v) checkoutOpen.value = true;
    } catch (e) {}
});
</script>

<template>
    <Head title="Transaksi" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Transaksi</h2>
        </template>

        <div class="mb-6">
            <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center text-sm font-bold">1</span>
                    Pilih Produk
                </h2>
                
                <div class="flex items-center justify-between mb-5">
                    <h4 class="font-semibold text-lg text-gray-700">Katalog Produk Komputer</h4>
                    <select v-model="selectedCategory" class="pl-4 pr-10 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none appearance-none">
                        <option value="">Semua Kategori</option>
                        <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
                    </select>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                    <div v-for="p in filteredProducts" :key="p.id" class="bg-gradient-to-br from-gray-50 to-white rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 border border-gray-200 hover:border-emerald-300">
                        <!-- Product Image -->
                        <div class="relative overflow-hidden bg-gray-100 h-48">
                            <img :src="productImage(p)" alt="" class="w-full h-full object-cover hover:scale-110 transition-transform duration-300" />
                            <div v-if="p.discount > 0" class="absolute top-2 right-2 bg-violet-600 text-white px-2 py-1 rounded text-xs font-bold shadow-sm">
                                Diskon -{{ p.discount }}%
                            </div>
                            <div v-else class="absolute top-2 left-2 bg-emerald-500 text-white px-2 py-1 rounded text-xs font-semibold">Best Seller</div>
                        </div>
                        
                        <!-- Product Info -->
                        <div class="p-4">
                            <!-- Product Name -->
                            <div class="font-semibold text-gray-800 mb-4 line-clamp-2 h-12">{{ p.name }}</div>
                            
                            <!-- Description -->
                            <div class="text-xs text-gray-500 mb-2 line-clamp-2 h-8">{{ p.desc }}</div>
                            
                            <!-- Price & Discount -->
                            <div class="mb-3">
                                <div class="text-lg font-bold text-emerald-600">Rp {{ formatPrice(p.price) }}</div>
                                <div v-if="p.discount > 0" class="text-xs text-gray-400 line-through">Rp {{ formatPrice(p.original_price) }}</div>
                            </div>
                            
                            <!-- Seller & Rating -->
                            <div class="mb-4 border-t pt-2">
                                <div class="text-xs text-gray-600 mb-1 truncate">{{ p.seller }}</div>
                                <div class="flex items-center gap-1">
                                    <div class="flex text-yellow-400">
                                        <span v-for="i in 5" :key="i" class="text-xs">★</span>
                                    </div>
                                    <span class="text-xs text-gray-600">{{ p.rating }}</span>
                                    <span class="text-xs text-gray-400">({{ p.reviews }})</span>
                                </div>
                            </div>
                            
                            <!-- Add to Cart Button -->
                            <button @click.prevent="addToCart(p)" class="w-full px-3 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-lg font-semibold hover:shadow-lg transition-all duration-200 text-sm">
                                + Keranjang
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Cart Summary Section -->
                <div v-if="cart.length > 0" class="mt-8 bg-gradient-to-r from-emerald-50 to-teal-50 p-6 rounded-lg border border-emerald-200">
                    <h3 class="font-bold text-gray-800 mb-4">📦 Ringkasan Keranjang Anda</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div class="bg-white rounded-lg p-4">
                            <div class="text-sm text-gray-600 mb-1">Jumlah Item</div>
                            <div class="text-2xl font-bold text-emerald-600">{{ cart.reduce((sum, c) => sum + c.qty, 0) }}</div>
                        </div>
                        <div class="bg-white rounded-lg p-4">
                            <div class="text-sm text-gray-600 mb-1">Total Harga</div>
                            <div class="text-2xl font-bold text-gray-800">Rp {{ formatPrice(cartTotal()) }}</div>
                        </div>
                        <div class="bg-white rounded-lg p-4 flex items-center justify-between">
                            <button @click.prevent="cart = []" class="px-4 py-2 bg-red-100 text-red-600 rounded-lg text-sm font-semibold hover:bg-red-200">Bersihkan</button>
                            <button @click.prevent="checkoutOpen = true" class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-lg font-semibold hover:shadow-lg">Lanjut</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Checkout Row - Tokopedia Style -->
        <transition name="slide-fade">
        <div v-show="checkoutOpen" class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between mb-6 pb-4 border-b">
                <h3 class="text-xl font-bold text-gray-800">📝 Form Pengiriman & Pembayaran</h3>
                <button @click.prevent="checkoutOpen = false" class="text-2xl text-gray-400 hover:text-gray-600">×</button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Left: Customer Data & Delivery -->
                <div class="md:col-span-2">
                    <div class="mb-6">
                        <h3 class="font-semibold text-gray-800 mb-3">Alamat Pengiriman</h3>
                        <div class="space-y-2">
                            <input v-model="form.customer_name" placeholder="Nama Lengkap" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-emerald-500" />
                            <input v-model="form.customer_phone" placeholder="Nomor HP" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-emerald-500" />
                            <input v-model="form.customer_email" placeholder="Email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-emerald-500" />
                            <input v-model="form.customer_city" placeholder="Kota / Provinsi" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-emerald-500" />
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="font-semibold text-gray-800 mb-3">Catatan untuk Penjual</h3>
                        <textarea v-model="form.description" placeholder="Catatan atau permintaan khusus (opsional)" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-emerald-500 h-24"></textarea>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h3 class="font-semibold text-blue-900 mb-3">Daftar Produk yang Dipesan</h3>
                        <div class="space-y-3">
                            <div v-if="cart.length === 0" class="text-sm text-gray-500">Keranjang Anda masih kosong</div>
                            <div v-for="c in cart" :key="c.id" class="flex justify-between items-center pb-3 border-b last:border-b-0">
                                <div class="flex-1">
                                    <div class="font-medium text-gray-800">{{ c.name }}</div>
                                    <div class="text-sm text-gray-500">{{ c.qty }} × Rp {{ formatPrice(c.price) }}</div>
                                </div>
                                <div class="font-semibold text-gray-800">Rp {{ formatPrice(c.qty * c.price) }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Order Summary & Payment -->
                <div class="md:col-span-1">
                    <div class="bg-gradient-to-b from-gray-50 to-white border border-gray-200 rounded-xl p-5 sticky top-20">
                        <h3 class="font-semibold text-gray-800 mb-4">Ringkasan Belanja</h3>
                        
                        <div class="space-y-3 mb-4 pb-4 border-b">
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Subtotal</span>
                                <span class="font-medium">Rp {{ formatPrice(cartTotal()) }}</span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Ongkir</span>
                                <span class="font-medium text-emerald-600">Gratis</span>
                            </div>
                        </div>

                        <div class="bg-emerald-50 rounded-lg p-3 mb-4">
                            <div class="flex justify-between items-center">
                                <span class="font-semibold text-gray-800">Total Tagihan</span>
                                <span class="text-2xl font-bold text-emerald-600">Rp {{ formatPrice(cartTotal()) }}</span>
                            </div>
                        </div>

                        <div class="mb-5">
                            <p class="text-sm font-semibold text-gray-700 mb-2">Metode Pembayaran</p>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="cursor-pointer">
                                    <input type="radio" v-model="form.payment_method" value="qris" class="peer sr-only" />
                                    <div class="p-2 border rounded-lg text-center peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-700 hover:bg-gray-50 transition">
                                        <div class="font-bold text-sm">QRIS</div>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" v-model="form.payment_method" value="kasir" class="peer sr-only" />
                                    <div class="p-2 border rounded-lg text-center peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-700 hover:bg-gray-50 transition">
                                        <div class="font-bold text-sm">Kasir</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <button @click.prevent="submitCheckout" :disabled="cart.length === 0 || !form.payment_method" class="w-full px-4 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-lg font-semibold hover:shadow-lg transition-all duration-200 disabled:opacity-50">
                                Bayar & Simpan Transaksi
                            </button>
                            <button @click.prevent="cart = []; checkoutOpen = false" class="w-full px-4 py-3 bg-gray-200 text-gray-800 rounded-lg font-semibold hover:bg-gray-300 transition-all duration-200">
                                Batalkan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </transition>
        
        <!-- Activities Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Aktivitas Transaksi</h3>
                <div class="space-y-3">
                    <div v-if="loading" class="text-sm text-gray-500">Memuat...</div>
                    <div v-if="!loading && transactions.length === 0" class="text-sm text-gray-500">Data tidak ditemukan. Silakan tambahkan data baru untuk melihat aktivitas terakhir.</div>
                    <ul v-if="transactions.length" class="space-y-3">
                        <li v-for="t in transactions" :key="t.id" class="p-4 border rounded-lg hover:bg-gray-50 transition">
                            <div class="flex justify-between mb-2">
                                <div class="inline-block px-2 py-1 bg-emerald-100 text-emerald-800 rounded text-xs font-semibold">{{ t.type === 'sale' ? 'Penjualan' : 'Pembelian' }}</div>
                                <span class="text-xs text-gray-400">{{ new Date(t.created_at).toLocaleDateString('id-ID') }}</span>
                            </div>
                            <div class="font-semibold text-gray-800">Rp {{ formatPrice(t.amount) }}</div>
                            <div class="text-sm text-gray-600 mt-1">{{ t.customer_name }} <span class="text-xs text-gray-400">({{ t.customer_city }})</span></div>
                            <div class="text-xs text-gray-500 mt-1">{{ t.customer_phone }} · {{ t.customer_email }}</div>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4">🔔 Notifikasi Pembelian</h3>
                <div class="space-y-3">
                    <div v-if="notifications.length === 0" class="text-sm text-gray-500">Belum ada notifikasi pembelian</div>
                    <ul v-if="notifications.length" class="space-y-3">
                        <li v-for="n in notifications" :key="n.id" class="p-4 border-l-4 border-emerald-500 bg-emerald-50 rounded-r">
                            <div class="font-semibold text-gray-800">{{ n.customer_name }}</div>
                            <div class="text-sm text-gray-600">{{ n.customer_city }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ n.customer_phone }} · {{ n.customer_email }}</div>
                            <div class="text-xs text-gray-400 mt-2">{{ new Date(n.created_at).toLocaleString('id-ID') }}</div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

    <!-- PO / Receipt Popup -->
    <div v-if="showPoPopup" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden transform transition-all">
            <!-- Header Popup -->
            <div class="bg-gradient-to-r from-emerald-500 to-teal-500 p-6 text-center relative">
                <button @click="showPoPopup = false" class="absolute top-4 right-4 text-white/80 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-3 shadow-inner">
                    <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                </div>
                <h3 class="text-2xl font-bold text-white">Pembayaran Berhasil!</h3>
            </div>
            
            <!-- Body Popup -->
            <div class="p-6">
                <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-100">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider">No. Transaksi</p>
                        <p class="font-bold text-gray-800">{{ currentPo?.id }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500 uppercase tracking-wider">Tanggal</p>
                        <p class="font-medium text-gray-800">{{ currentPo?.date }}</p>
                    </div>
                </div>
                
                <div class="mb-6">
                    <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Pelanggan</p>
                    <p class="font-medium text-gray-800">{{ currentPo?.customer_name }}</p>
                </div>
                
                <div class="space-y-3 mb-6 pb-4 border-b border-gray-100">
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Detail Barang</div>
                    <div v-for="item in currentPo?.items" :key="item.id" class="flex justify-between text-sm">
                        <div class="flex-1 pr-4">
                            <span class="text-gray-800 font-medium block truncate">{{ item.name }}</span>
                            <span class="text-gray-500 text-xs">{{ item.qty }} × Rp {{ formatPrice(item.price) }}</span>
                        </div>
                        <span class="font-semibold text-gray-800 whitespace-nowrap">Rp {{ formatPrice(item.qty * item.price) }}</span>
                    </div>
                </div>
                
                <div class="flex justify-between items-end mb-8">
                    <div>
                        <p class="text-sm text-gray-500">Status</p>
                        <p :class="['font-semibold', currentPo?.status === 'Lunas' ? 'text-emerald-600' : 'text-amber-500']">{{ currentPo?.status }}</p>
                        <p v-if="currentPo?.status === 'Pending'" class="text-xs text-amber-600 mt-1">Harap bawa ID ke Kasir</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Total Pembayaran</p>
                        <p class="text-2xl font-bold text-gray-900">Rp {{ formatPrice(currentPo?.total) }}</p>
                    </div>
                </div>
                
                <div class="flex gap-3">
                    <button @click="showPoPopup = false" class="flex-1 py-3 bg-gray-100 text-gray-800 font-semibold rounded-xl hover:bg-gray-200 transition-colors">
                        Tutup
                    </button>
                    <button @click="showPoPopup = false" class="flex-1 py-3 bg-emerald-50 text-emerald-600 font-semibold rounded-xl hover:bg-emerald-100 transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                        Cetak Struk
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.slide-fade-enter-active {
    transition: all .25s ease;
}
.slide-fade-leave-active {
    transition: all .2s ease;
}
.slide-fade-enter-from {
    opacity: 0;
    transform: translateY(-10px);
}
.slide-fade-enter-to {
    opacity: 1;
    transform: translateY(0);
}
.slide-fade-leave-from {
    opacity: 1;
    transform: translateY(0);
}
.slide-fade-leave-to {
    opacity: 0;
    transform: translateY(-6px);
}

/* pinned checkout overlay styling for smaller screens adjustments */
@media (max-width: 768px) {
    .fixed.w-96 { width: calc(100% - 2rem); right: 1rem; }
}
</style>