<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

const isSidebarOpen = ref(true);
</script>

<template>
    <div class="flex h-screen bg-gray-100 font-sans antialiased">
        <aside 
            :class="[
                'bg-slate-950 text-white w-64 min-h-screen p-4 flex flex-col transition-all duration-300 z-20 shadow-xl',
                isSidebarOpen ? 'translate-x-0 relative' : '-translate-x-64 absolute md:hidden'
            ]"
        >
            <div class="flex items-center justify-between pb-6 mb-6 border-b border-slate-800">
                <div class="flex items-center space-x-3 px-2">
                    <svg class="w-8 h-8 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z" />
                    </svg>
                    <span class="text-lg font-bold tracking-wider text-emerald-400">REKAPKU ADM</span>
                </div>
            </div>

            <nav class="flex-1 space-y-1">
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-3 mb-2">Main Menu</div>
                
                <Link 
                    :href="route('dashboard')" 
                    :class="[
                        'flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-200',
                        route().current('dashboard') 
                            ? 'bg-emerald-500/10 text-emerald-400 font-medium' 
                            : 'text-slate-400 hover:bg-slate-900 hover:text-white'
                    ]"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" /></svg>
                    <span>Dashboard</span>
                </Link>
                <Link 
                    :href="route('stocks.index')" 
                    :class="[
                        'flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-200',
                        route().current('stocks.index') 
                            ? 'bg-emerald-500/10 text-emerald-400 font-medium' 
                            : 'text-slate-400 hover:bg-slate-900 hover:text-white'
                    ]"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" /></svg>
                    <span>Stock</span>
                </Link>
            </nav>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow-sm h-16 flex items-center justify-between px-6 border-b border-gray-200 z-10">
                <button 
                    @click="isSidebarOpen = !isSidebarOpen" 
                    class="text-gray-500 focus:outline-none hover:text-gray-700 transition-colors"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
                </button>
            </header>

            <div class="flex-1 overflow-x-hidden overflow-y-auto p-6 bg-gray-50">
                <div v-if="$slots.header" class="mb-6">
                    <slot name="header" />
                </div>
                
                <main>
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>