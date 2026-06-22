<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

const isSidebarOpen = ref(true);
</script>

<style scoped>
.sidebar {
    width: 256px;
    min-width: 256px;
    transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                min-width 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                padding 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
}

.sidebar.collapsed {
    width: 0;
    min-width: 0;
    opacity: 0;
    padding-left: 0;
    padding-right: 0;
}

.sidebar-inner {
    width: 256px;
    min-width: 256px;
}

.toggle-icon {
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.toggle-icon.rotated {
    transform: rotate(90deg);
}
</style>

<template>
    <div class="flex h-screen bg-gray-100 font-sans antialiased">
        <aside :class="['sidebar bg-slate-950 text-white min-h-screen flex flex-col z-20 shadow-xl', isSidebarOpen ? '' : 'collapsed']">
            <div class="sidebar-inner flex flex-col flex-1 p-4">
            <div class="flex items-center justify-center pb-6 mb-6 border-b border-slate-800">
                <img src="/images/logo-sidebar.png" alt="SwaPartID" class="h-12 object-contain" />
            </div>

            <nav class="flex-1 space-y-1">
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-3 mb-2">Main Menu</div>
                
                <Link 
                    v-show="$page.props.auth?.user?.role !== 'guest'"
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

                <!-- buatan yaqin -->
                <!-- <Link 
                    v-show="$page.props.auth?.user?.role !== 'guest'"
                    :href="route('stock')" 
                    :class="[
                        'flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-200',
                        route().current('stock') 
                            ? 'bg-emerald-500/10 text-emerald-400 font-medium' 
                            : 'text-slate-400 hover:bg-slate-900 hover:text-white'
                    ]"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                    <span>Stock</span>
                </Link> -->

                <Link 
                    :href="route('transaksi')" 
                    :class="[
                        'flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-200',
                        route().current('transaksi') 
                            ? 'bg-violet-500/10 text-violet-400 font-medium' 
                            : 'text-slate-400 hover:bg-slate-900 hover:text-white'
                    ]"    
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 5L19 12H7.37671M20 16H8L6 3H3M16 5.5H13.5M13.5 5.5H11M13.5 5.5V8M13.5 5.5V3M9 20C9 20.5523 8.55228 21 8 21C7.44772 21 7 20.5523 7 20C7 19.4477 7.44772 19 8 19C8.55228 19 9 19.4477 9 20ZM20 20C20 20.5523 19.5523 21 19 21C18.4477 21 18 20.5523 18 20C18 19.4477 18.4477 19 19 19C19.5523 19 20 19.4477 20 20Z" /></svg>
                    <span>Katalog</span>
                </Link>

                <Link 
                    v-show="$page.props.auth?.user?.role !== 'guest'"
                    :href="route('kasir')" 
                    :class="[
                        'flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-200',
                        route().current('kasir') 
                            ? 'bg-violet-500/10 text-violet-400 font-medium' 
                            : 'text-slate-400 hover:bg-slate-900 hover:text-white'
                    ]"    
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z" /></svg>
                    <span>Kasir</span>
                </Link>

                <!-- <Link 
                    v-show="$page.props.auth?.user?.role !== 'guest'"
                    href="#" 
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-200 text-slate-400 hover:bg-slate-900 hover:text-white"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z" /></svg>
                    <span>Laporan Analisis</span>
                </Link> -->

                <!-- <div v-show="$page.props.auth?.user?.role !== 'guest'" class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-3 pt-6 mb-2">Sistem</div>

                <Link 
                    v-show="$page.props.auth?.user?.role !== 'guest'"
                    href="#" 
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-200 text-slate-400 hover:bg-slate-900 hover:text-white"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    <span>Pengaturan</span>
                </Link> -->
            </nav>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow-sm h-16 flex items-center justify-between px-6 border-b border-gray-200 z-10">
                <button 
                    @click="isSidebarOpen = !isSidebarOpen" 
                    class="text-gray-500 focus:outline-none hover:text-gray-700 transition-colors p-1 rounded-lg hover:bg-gray-100"
                    :title="isSidebarOpen ? 'Sembunyikan sidebar' : 'Tampilkan sidebar'"
                >
                    <svg :class="['toggle-icon w-6 h-6', isSidebarOpen ? '' : 'rotated']" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <div class="flex items-center space-x-4">
                    <div v-if="$page.props.auth?.user" class="relative">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <span class="inline-flex rounded-md">
                                    <button
                                        type="button"
                                        class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none"
                                    >
                                        <div class="flex flex-col text-right mr-3">
                                            <span class="text-sm font-bold text-gray-800">{{ $page.props.auth.user.name }}</span>
                                            <span class="text-xs text-emerald-600 font-bold uppercase tracking-wider">{{ $page.props.auth.user.role }}</span>
                                        </div>
                                        <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
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
                    <div v-else>
                        <span class="text-sm text-gray-500">Guest</span>
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