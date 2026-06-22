<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex bg-slate-50 font-sans antialiased">
        <!-- Left Pane: Branding / Graphic -->
        <div class="hidden lg:flex lg:w-1/2 relative bg-slate-950 overflow-hidden items-center justify-center">
            <!-- Decorative Background Elements -->
            <div class="absolute top-0 left-0 w-full h-full opacity-20">
                <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full bg-emerald-500 blur-3xl"></div>
                <div class="absolute top-1/2 right-0 w-96 h-96 rounded-full bg-teal-500 blur-3xl translate-x-1/2 -translate-y-1/2"></div>
                <div class="absolute -bottom-24 -left-24 w-96 h-96 rounded-full bg-blue-500 blur-3xl"></div>
            </div>
            
            <div class="relative z-10 text-center px-12">
                <div class="flex items-center justify-center w-fit mx-auto mb-8 mt-4">
                    <img src="/images/logo.png" alt="SwaPartID" class="h-32 transform scale-150 object-contain" />
                </div>
                <!-- <h1 class="text-4xl font-bold text-white mb-6 leading-tight">Sales Intelligence<br>Agent System</h1> -->
                <p class="text-slate-400 text-lg max-w-md mx-auto leading-relaxed">
                    Kelola stok, pantau transaksi kasir, dan dapatkan insight bisnis otomatis melalui kecerdasan buatan.
                </p>
                
                <!-- Mockup/Illustration placeholder -->
                <div class="mt-12 w-full max-w-sm mx-auto bg-slate-900/50 backdrop-blur-sm rounded-2xl border border-slate-800 p-6 shadow-2xl relative">
                    <div class="absolute -inset-1 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-2xl blur opacity-20"></div>
                    <div class="relative flex items-center justify-between border-b border-slate-800 pb-4 mb-4">
                        <div class="w-24 h-4 bg-slate-800 rounded-full"></div>
                        <div class="w-12 h-4 bg-emerald-500/20 rounded-full"></div>
                    </div>
                    <div class="space-y-3">
                        <div class="w-full h-3 bg-slate-800 rounded-full"></div>
                        <div class="w-5/6 h-3 bg-slate-800 rounded-full"></div>
                        <div class="w-4/6 h-3 bg-slate-800 rounded-full"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Pane: Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12 lg:p-24 bg-white relative">
            <div class="w-full max-w-md">
                <slot />
            </div>
        </div>
    </div>
</template>
