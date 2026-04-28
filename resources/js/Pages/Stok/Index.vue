<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import {
    IconBox,
    IconBuildingFactory,
    IconLayoutDashboard,
    IconArrowRight
} from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    stok: Array<{
        id: number;
        proses: string;
        urutan: number;
        total_produk: number;
        departemen: { id: number, departemen: string };
    }>;
}>();

// Warna tema berdasarkan ID Departemen
const themes: { [key: number]: string } = {
    1: 'bg-blue-500',      // Casting
    2: 'bg-emerald-500',   // Solar
    3: 'bg-amber-500',     // Spray
    5: 'bg-orange-500',    // Oven Susun
    6: 'bg-rose-500',      // Oven Bongkar
    7: 'bg-teal-500',      // Checking
    23: 'bg-pink-500',     // Rework
    24: 'bg-cyan-500',     // QC
    0: 'bg-slate-500'      // Default
};

const getTheme = (id: number) => themes[id] || themes[id % 8] || themes[0];
</script>

<template>
    <Head title="Stok Produksi" />

    <div class="p-4 md:p-8 max-w-4xl mx-auto space-y-4">

        <div class="flex items-center justify-between pb-4 border-b">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-slate-900 dark:bg-primary rounded-lg text-white">
                    <IconLayoutDashboard class="size-6" />
                </div>
                <h1 class="text-xl font-black uppercase tracking-tight text-slate-900 dark:text-white">
                    Ringkasan Stok
                </h1>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-bold text-muted-foreground uppercase">Total Produk Aktif</p>
                <p class="text-2xl font-black text-primary leading-none">
                    {{ stok.reduce((acc, i) => acc + i.total_produk, 0).toLocaleString() }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-2">
            <div
                v-for="item in stok"
                :key="item.id"
                class="group relative bg-white dark:bg-slate-950 border rounded-xl overflow-hidden hover:shadow-md transition-all"
            >
                <div class="flex items-center p-3 pr-6 gap-4">

                    <div
                        class="flex shrink-0 items-center justify-center size-10 rounded-full text-white font-black text-sm shadow-sm"
                        :class="getTheme(item.departemen.id)"
                    >
                        {{ item.urutan }}
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-1.5 opacity-60">
                            <IconBuildingFactory class="size-3" />
                            <span class="text-[10px] font-bold uppercase tracking-wider">{{ item.departemen.departemen }}</span>
                        </div>
                        <h3 class="font-bold text-base uppercase text-slate-800 dark:text-slate-200">
                            {{ item.proses }}
                        </h3>
                    </div>

                    <IconArrowRight class="size-4 text-slate-200 dark:text-slate-800 hidden md:block" />

                    <div class="flex items-center gap-3 bg-slate-50 dark:bg-slate-900 px-4 py-2 rounded-lg border ml-auto">
                        <div class="text-right">
                            <span class="text-xl font-black text-slate-900 dark:text-white">
                                {{ item.total_produk || 0 }}
                            </span>
                            <span class="text-[10px] font-bold text-muted-foreground ml-1 uppercase tracking-tighter">Unit</span>
                        </div>
                        <IconBox class="size-5 text-primary opacity-80" />
                    </div>
                </div>

                <div class="absolute left-0 top-0 h-full w-1" :class="getTheme(item.departemen.id)"></div>
            </div>
        </div>

        <p class="text-center text-[10px] font-bold text-muted-foreground uppercase tracking-[0.3em] pt-6 opacity-40">
            Mark Dynamics Indonesia
        </p>
    </div>
</template>
