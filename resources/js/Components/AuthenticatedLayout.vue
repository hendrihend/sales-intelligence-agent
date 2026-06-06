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
                    href="#" 
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-200 text-slate-400 hover:bg-slate-900 hover:text-white"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>Transaksi</span>
                </Link>

                <Link 
                    href="#" 
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-200 text-slate-400 hover:bg-slate-900 hover:text-white"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z" /></svg>
                    <span>Laporan Analisis</span>
                </Link>

                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-3 pt-6 mb-2">Sistem</div>

                <Link 
                    href="#" 
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-200 text-slate-400 hover:bg-slate-900 hover:text-white"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    <span>Pengaturan</span>
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

                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <span class="inline-flex rounded-md">
                                    <button
                                        type="button"
                                        class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none"
                                    >
                                        {{ $page.props.auth.user.name }}
                                        <svg class="-me-0.5 ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1.25 1.25 0 011.414 0L10 10.586l3.293-3.293a1.25 1.25 0 111.768 1.768l-4 4a1.25 1.25 0 01-1.768 0l-4-4a1.25 1.25 0 010-1.768z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </span>
                            </template>

                            <template #content>
                                <DropdownLink :href="route('profile.edit')"> Profile </DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button"> Log Out </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>
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