<script setup>
import AuthenticatedLayout from '@/Layouts/Sidebar.vue';
import { Head } from '@inertiajs/vue3';
import { ref, nextTick } from 'vue';

const userMessage = ref('');
const isTyping = ref(false);
const chatContainer = ref(null);

const messages = ref([
    {
        id: 1,
        role: 'assistant',
        content: 'Halo! 👋 Saya adalah Sales Intelligence Agent. Saya bisa membantu Anda menganalisis data penjualan, memberikan rekomendasi strategi, dan menjawab pertanyaan tentang performa bisnis Anda. Ada yang bisa saya bantu?',
        time: '10:00',
    },
]);

const quickPrompts = [
    '📊 Analisis penjualan bulan ini',
    '📈 Prediksi tren penjualan',
    '💡 Rekomendasi strategi marketing',
    '🏷️ Produk terlaris minggu ini',
];

const scrollToBottom = async () => {
    await nextTick();
    if (chatContainer.value) {
        chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
    }
};

const sendMessage = async (text = null) => {
    const msg = text || userMessage.value.trim();
    if (!msg) return;

    const now = new Date();
    const timeStr = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');

    messages.value.push({
        id: Date.now(),
        role: 'user',
        content: msg,
        time: timeStr,
    });

    userMessage.value = '';
    isTyping.value = true;
    await scrollToBottom();

    // Simulate AI response
    setTimeout(async () => {
        const responses = [
            'Berdasarkan analisis data penjualan Anda, saya melihat tren peningkatan sebesar **12.5%** dibandingkan bulan lalu. Produk kategori elektronik menjadi kontributor utama pertumbuhan ini.',
            'Saya merekomendasikan untuk fokus pada **3 strategi utama**: (1) Optimalkan stok produk terlaris, (2) Tingkatkan promosi di channel digital, (3) Berikan program loyalitas untuk pelanggan repeat.',
            'Dari data yang tersedia, **Laptop ASUS ROG** dan **SSD Samsung NVMe** adalah produk dengan margin keuntungan tertinggi. Pertimbangkan untuk meningkatkan stok kedua produk ini.',
            'Analisis sentimen pelanggan menunjukkan **87% positif**. Area perbaikan utama ada di kecepatan pengiriman dan ketersediaan stok produk populer.',
        ];

        messages.value.push({
            id: Date.now(),
            role: 'assistant',
            content: responses[Math.floor(Math.random() * responses.length)],
            time: timeStr,
        });

        isTyping.value = false;
        await scrollToBottom();
    }, 1500);
};

const handleKeydown = (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
};
</script>

<template>
    <Head title="Agent AI" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center space-x-3">
                <div class="relative">
                    <div class="w-10 h-10 bg-gradient-to-br from-violet-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg shadow-violet-200">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5" /></svg>
                    </div>
                    <div class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-emerald-400 rounded-full border-2 border-white"></div>
                </div>
                <div>
                    <h2 class="font-semibold text-lg text-gray-800 leading-tight">Sales Intelligence Agent</h2>
                    <p class="text-xs text-emerald-500 font-medium">● Online — Siap membantu</p>
                </div>
            </div>
        </template>

        <div class="flex flex-col h-[calc(100vh-12rem)]">
            <!-- Chat Messages -->
            <div ref="chatContainer" class="flex-1 overflow-y-auto space-y-4 pb-4 pr-2 -mr-2">
                <div v-for="msg in messages" :key="msg.id" class="flex" :class="msg.role === 'user' ? 'justify-end' : 'justify-start'">
                    <!-- AI Message -->
                    <div v-if="msg.role === 'assistant'" class="flex items-start space-x-3 max-w-[75%]">
                        <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-br from-violet-500 to-purple-600 rounded-lg flex items-center justify-center mt-1">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5" /></svg>
                        </div>
                        <div class="bg-white rounded-2xl rounded-tl-md px-4 py-3 shadow-sm border border-gray-100">
                            <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap" v-html="msg.content.replace(/\*\*(.*?)\*\*/g, '<strong class=\'text-gray-900\'>$1</strong>')"></p>
                            <p class="text-[10px] text-gray-400 mt-2">{{ msg.time }}</p>
                        </div>
                    </div>

                    <!-- User Message -->
                    <div v-else class="max-w-[75%]">
                        <div class="bg-emerald-500 text-white rounded-2xl rounded-tr-md px-4 py-3 shadow-sm">
                            <p class="text-sm leading-relaxed">{{ msg.content }}</p>
                            <p class="text-[10px] text-emerald-200 mt-2 text-right">{{ msg.time }}</p>
                        </div>
                    </div>
                </div>

                <!-- Typing Indicator -->
                <div v-if="isTyping" class="flex items-start space-x-3">
                    <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-br from-violet-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5" /></svg>
                    </div>
                    <div class="bg-white rounded-2xl rounded-tl-md px-4 py-3 shadow-sm border border-gray-100">
                        <div class="flex space-x-1.5">
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Prompts -->
            <div v-if="messages.length <= 1" class="flex flex-wrap gap-2 pb-4">
                <button
                    v-for="prompt in quickPrompts"
                    :key="prompt"
                    @click="sendMessage(prompt)"
                    class="px-4 py-2 bg-white border border-gray-200 rounded-full text-sm text-gray-600 hover:bg-emerald-50 hover:border-emerald-200 hover:text-emerald-700 transition-all duration-200 shadow-sm"
                >
                    {{ prompt }}
                </button>
            </div>

            <!-- Input Area -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-3">
                <div class="flex items-end space-x-3">
                    <div class="flex-1">
                        <textarea
                            v-model="userMessage"
                            @keydown="handleKeydown"
                            rows="1"
                            placeholder="Ketik pertanyaan Anda tentang penjualan..."
                            class="w-full resize-none border-0 focus:ring-0 text-sm text-gray-700 placeholder-gray-400 outline-none p-1"
                            style="max-height: 120px;"
                        ></textarea>
                    </div>
                    <button
                        @click="sendMessage()"
                        :disabled="!userMessage.trim() || isTyping"
                        :class="[
                            'flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-200',
                            userMessage.trim() && !isTyping
                                ? 'bg-emerald-500 hover:bg-emerald-600 text-white shadow-sm shadow-emerald-200'
                                : 'bg-gray-100 text-gray-400 cursor-not-allowed'
                        ]"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" /></svg>
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
