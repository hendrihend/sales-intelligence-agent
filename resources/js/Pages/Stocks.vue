<script setup>
import AuthenticatedLayout from '@/Layouts/Sidebar.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Stocks" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Stock Overview</h2>
        </template>

       

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Aktivitas Terakhir</h3>
            <div class="border-t border-gray-100 pt-4 text-gray-500 text-sm">
                Data tidak ditemukan. Silakan tambahkan data baru untuk melihat aktivitas terakhir.
            </div>
        </div>
    </AuthenticatedLayout>
</template>