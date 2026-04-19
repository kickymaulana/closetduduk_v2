<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
    CardDescription,
} from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import {
    IconPackage,
    IconClock,
    IconChecklist,
    IconPlus,
    IconChartBar,
    IconArrowRight,
    IconTruck,
    IconCircleCheck,
} from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

const stats = [
    {
        label: "Total Sampel",
        value: "124",
        icon: IconPackage,
        color: "text-blue-600 dark:text-blue-400",
        bg: "bg-blue-50 dark:bg-blue-950/30",
    },
    {
        label: "Sedang Diproses",
        value: "12",
        icon: IconClock,
        color: "text-orange-600 dark:text-orange-400",
        bg: "bg-orange-50 dark:bg-orange-950/30",
    },
    {
        label: "Butuh Persetujuan",
        value: "5",
        icon: IconChecklist,
        color: "text-purple-600 dark:text-purple-400",
        bg: "bg-purple-50 dark:bg-purple-950/30",
    },
    {
        label: "Selesai Hari Ini",
        value: "8",
        icon: IconCircleCheck,
        color: "text-green-600 dark:text-green-400",
        bg: "bg-green-50 dark:bg-green-950/30",
    },
];

const recentSamples = [
    {
        code: "KDSPT29",
        customer: "ARTALEGA",
        status: "Proses",
        date: "12 Apr 2026",
    },
    {
        code: "KDSPT28",
        customer: "MARK DYNAMICS",
        status: "Selesai",
        date: "11 Apr 2026",
    },
    {
        code: "KDSPT27",
        customer: "GLOVE CORP",
        status: "Draft",
        date: "11 Apr 2026",
    },
    {
        code: "KDSPT26",
        customer: "SUNG SHIN",
        status: "Selesai",
        date: "10 Apr 2026",
    },
];
</script>

<template>
    <Head title="Dashboard SISAMCUS" />

    <div
        class="flex flex-col gap-6 p-4 md:p-8 pt-4 transition-colors duration-500"
    >
        <div
            class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4"
        >
            <div>
                <h1
                    class="text-2xl font-black tracking-tight text-slate-900 dark:text-slate-100 uppercase italic"
                >
                    Dashboard SISAMCUS
                </h1>
                <p
                    class="text-[10px] text-muted-foreground font-bold uppercase tracking-[0.2em]"
                >
                    PT Mark Dynamics Indonesia Tbk
                </p>
            </div>
            <div class="flex gap-2">
                <Button
                    variant="outline"
                    size="sm"
                    class="font-bold uppercase text-[10px] h-9 dark:border-slate-800"
                >
                    <IconChartBar class="mr-2 size-4" /> Laporan
                </Button>
                <Button
                    size="sm"
                    class="font-black uppercase text-[10px] h-9 shadow-lg shadow-primary/20"
                >
                    <IconPlus class="mr-2 size-4" /> Sampel Baru
                </Button>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <Card
                v-for="stat in stats"
                :key="stat.label"
                class="border-none shadow-sm overflow-hidden dark:bg-slate-900/50 dark:ring-1 dark:ring-slate-800"
            >
                <CardContent class="p-6 flex items-center justify-between">
                    <div>
                        <p
                            class="text-[9px] font-black uppercase text-muted-foreground tracking-widest mb-1"
                        >
                            {{ stat.label }}
                        </p>
                        <h3
                            class="text-3xl font-black italic tracking-tighter dark:text-slate-100"
                        >
                            {{ stat.value }}
                        </h3>
                    </div>
                    <div
                        :class="[stat.bg, stat.color]"
                        class="size-12 rounded-2xl flex items-center justify-center transition-colors"
                    >
                        <component :is="stat.icon" class="size-6" />
                    </div>
                </CardContent>
            </Card>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <Card
                class="lg:col-span-2 border-none shadow-sm dark:bg-slate-900/50 dark:ring-1 dark:ring-slate-800"
            >
                <CardHeader class="pb-2">
                    <CardTitle
                        class="text-xs font-black uppercase tracking-widest flex items-center gap-2 dark:text-slate-200"
                    >
                        <IconPackage class="size-4 text-primary" /> Aktivitas
                        Sampel Terbaru
                    </CardTitle>
                </CardHeader>
                <CardContent class="space-y-3">
                    <div
                        v-for="sample in recentSamples"
                        :key="sample.code"
                        class="flex items-center justify-between p-3 rounded-xl bg-muted/30 dark:bg-slate-950/40 border border-transparent hover:border-primary/20 hover:bg-white dark:hover:bg-slate-900 transition-all cursor-pointer group"
                    >
                        <div class="flex items-center gap-4">
                            <div
                                class="size-10 rounded-lg bg-white dark:bg-slate-800 shadow-sm flex items-center justify-center font-black text-[10px] text-primary border dark:border-slate-700 italic group-hover:bg-primary group-hover:text-white transition-colors"
                            >
                                SMP
                            </div>
                            <div>
                                <p
                                    class="text-xs font-black uppercase tracking-tight dark:text-slate-100"
                                >
                                    {{ sample.code }}
                                </p>
                                <p
                                    class="text-[9px] text-muted-foreground font-bold uppercase"
                                >
                                    {{ sample.customer }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <Badge
                                :variant="
                                    sample.status === 'Selesai'
                                        ? 'default'
                                        : 'secondary'
                                "
                                class="text-[8px] font-black uppercase px-2 py-0"
                            >
                                {{ sample.status }}
                            </Badge>
                            <IconArrowRight
                                class="size-4 text-muted-foreground group-hover:text-primary transition-transform group-hover:translate-x-1"
                            />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <div class="space-y-4">
                <Card
                    class="border-none shadow-sm bg-primary text-primary-foreground dark:bg-indigo-900 overflow-hidden relative group"
                >
                    <div
                        class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform duration-500"
                    >
                        <IconTruck class="size-32 text-white" />
                    </div>
                    <CardHeader class="pb-2">
                        <CardTitle
                            class="text-xs font-black uppercase tracking-widest italic"
                            >Tugas Produksi</CardTitle
                        >
                    </CardHeader>
                    <CardContent>
                        <p class="text-2xl font-black mb-4 tracking-tighter">
                            12
                            <span
                                class="text-[10px] font-bold opacity-70 italic uppercase ml-1"
                                >Tugas Aktif</span
                            >
                        </p>
                        <Button
                            variant="secondary"
                            size="sm"
                            class="w-full font-black uppercase text-[9px] h-8 dark:bg-slate-100 dark:text-slate-900"
                        >
                            Buka Antrean <IconArrowRight class="ml-2 size-3" />
                        </Button>
                    </CardContent>
                </Card>

                <Card
                    class="border-none shadow-sm dark:bg-slate-900/50 dark:ring-1 dark:ring-slate-800"
                >
                    <CardHeader class="pb-2">
                        <CardTitle
                            class="text-[9px] font-black uppercase tracking-widest text-muted-foreground italic"
                            >Update Terakhir</CardTitle
                        >
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="flex gap-3">
                            <div
                                class="size-1.5 rounded-full bg-orange-500 mt-1 shrink-0"
                            ></div>
                            <p
                                class="text-[10px] font-bold leading-snug dark:text-slate-300"
                            >
                                Menunggu Paraf Manager:
                                <span
                                    class="text-primary dark:text-indigo-400 uppercase underline italic"
                                    >KDSPT29</span
                                >
                            </p>
                        </div>
                        <div class="flex gap-3">
                            <div
                                class="size-1.5 rounded-full bg-blue-500 mt-1 shrink-0"
                            ></div>
                            <p
                                class="text-[10px] font-bold leading-snug dark:text-slate-300"
                            >
                                Selesai Produksi:
                                <span
                                    class="text-primary dark:text-indigo-400 uppercase underline italic"
                                    >KDSPT28</span
                                >
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
