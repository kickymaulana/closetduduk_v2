<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { computed } from "vue";
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import {
    IconPackage,
    IconClock,
    IconAlertTriangle,
    IconPlus,
    IconChartBar,
    IconArrowRight,
    IconTruck,
    IconHistory,
} from "@tabler/icons-vue";

// Menerima data dari Controller
const props = defineProps<{
    statsData: any;
    recentActivities: any;
    topCacats: any;
}>();

defineOptions({ layout: AuthenticatedLayout });

const displayStats = computed(() => [
    {
        label: "Total Unit Produk",
        value: props.statsData.total_produk,
        icon: IconPackage,
        color: "text-blue-600 dark:text-blue-400",
        bg: "bg-blue-50 dark:bg-blue-950/30",
    },
    {
        label: "Pengerjaan Hari Ini",
        value: props.statsData.proses_aktif,
        icon: IconClock,
        color: "text-orange-600 dark:text-orange-400",
        bg: "bg-orange-50 dark:bg-orange-950/30",
    },
    {
        label: "Temuan Cacat (Today)",
        value: props.statsData.total_cacat_hari_ini,
        icon: IconAlertTriangle,
        color: "text-red-600 dark:text-red-400",
        bg: "bg-red-50 dark:bg-red-950/30",
    },
    {
        label: "Total Troli",
        value: props.statsData.troli_berjalan,
        icon: IconTruck,
        color: "text-green-600 dark:text-green-400",
        bg: "bg-green-50 dark:bg-green-950/30",
    },
]);
</script>

<template>
    <Head title="Dashboard SIMANSUR" />

    <div class="flex flex-col gap-6 p-4 md:p-8 pt-4 transition-colors duration-500">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-slate-100 uppercase italic">
                    Dashboard SIMANSUR
                </h1>
                <p class="text-[10px] text-muted-foreground font-bold uppercase tracking-[0.2em]">
                    SIstem Monitoring Alur Nilai Scan Unit Reject Closet Duduk
                </p>
            </div>
            <div class="flex gap-2">
                <Button variant="outline" size="sm" class="font-bold uppercase text-[10px] h-9 dark:border-slate-800">
                    <IconChartBar class="mr-2 size-4" /> Laporan Produksi
                </Button>
                <Button size="sm" class="font-black uppercase text-[10px] h-9 shadow-lg shadow-primary/20">
                    <IconPlus class="mr-2 size-4" /> Scan Produk
                </Button>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <Card v-for="stat in displayStats" :key="stat.label"
                class="border-none shadow-sm overflow-hidden dark:bg-slate-900/50 dark:ring-1 dark:ring-slate-800">
                <CardContent class="p-6 flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-black uppercase text-muted-foreground tracking-widest mb-1">
                            {{ stat.label }}
                        </p>
                        <h3 class="text-3xl font-black italic tracking-tighter dark:text-slate-100">
                            {{ stat.value }}
                        </h3>
                    </div>
                    <div :class="[stat.bg, stat.color]" class="size-12 rounded-2xl flex items-center justify-center">
                        <component :is="stat.icon" class="size-6" />
                    </div>
                </CardContent>
            </Card>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <Card class="lg:col-span-2 border-none shadow-sm dark:bg-slate-900/50 dark:ring-1 dark:ring-slate-800">
                <CardHeader class="pb-2">
                    <CardTitle class="text-xs font-black uppercase tracking-widest flex items-center gap-2 dark:text-slate-200">
                        <IconHistory class="size-4 text-primary" /> Aktivitas Produksi Terkini
                    </CardTitle>
                </CardHeader>
                <CardContent class="space-y-3">
                    <div v-for="activity in recentActivities" :key="activity.id"
                        class="flex items-center justify-between p-3 rounded-xl bg-muted/30 dark:bg-slate-950/40 border border-transparent hover:border-primary/20 transition-all cursor-pointer group">
                        <div class="flex items-center gap-4">
                            <div class="size-10 rounded-lg bg-white dark:bg-slate-800 shadow-sm flex items-center justify-center font-black text-[10px] text-primary border dark:border-slate-700 italic">
                                UNIT
                            </div>
                            <div>
                                <p class="text-xs font-black uppercase tracking-tight dark:text-slate-100">
                                    {{ activity.qrcode }}
                                </p>
                                <p class="text-[9px] text-muted-foreground font-bold uppercase">
                                    {{ activity.proses_nama }} • {{ activity.operator }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-[9px] font-bold text-muted-foreground uppercase">{{ activity.waktu }}</span>
                            <IconArrowRight class="size-4 text-muted-foreground group-hover:text-primary transition-transform group-hover:translate-x-1" />
                        </div>
                    </div>
                    <div v-if="recentActivities.length === 0" class="text-center py-4 text-xs text-muted-foreground uppercase font-bold">
                        Belum ada aktivitas hari ini
                    </div>
                </CardContent>
            </Card>

            <div class="space-y-4">
                <Card class="border-none shadow-sm bg-primary text-primary-foreground dark:bg-indigo-900 overflow-hidden relative group">
                    <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform duration-500">
                        <IconAlertTriangle class="size-32 text-white" />
                    </div>
                    <CardHeader class="pb-2">
                        <CardTitle class="text-xs font-black uppercase tracking-widest italic">Peringatan QC</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-2 relative z-10">
                            <div v-for="item in topCacats" :key="item.id" class="flex justify-between items-center bg-white/10 p-2 rounded-lg">
                                <span class="text-[10px] font-bold uppercase tracking-tight">{{ item.cacat.cacat }}</span>
                                <Badge variant="secondary" class="text-[10px] font-black">{{ item.total }}x</Badge>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card class="border-none shadow-sm dark:bg-slate-900/50 dark:ring-1 dark:ring-slate-800">
                    <CardHeader class="pb-2">
                        <CardTitle class="text-[9px] font-black uppercase tracking-widest text-muted-foreground italic">Info Sistem</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="flex gap-3">
                            <div class="size-1.5 rounded-full bg-green-500 mt-1 shrink-0"></div>
                            <p class="text-[10px] font-bold leading-snug dark:text-slate-300 uppercase">
                                Server: <span class="text-primary dark:text-indigo-400">Online</span>
                            </p>
                        </div>
                        <div class="flex gap-3">
                            <div class="size-1.5 rounded-full bg-blue-500 mt-1 shrink-0"></div>
                            <p class="text-[10px] font-bold leading-snug dark:text-slate-300 uppercase">
                                Database: <span class="text-primary dark:text-indigo-400">MariaDB 11.2</span>
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
