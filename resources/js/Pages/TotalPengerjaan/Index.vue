<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover";
import { Calendar } from "@/components/ui/calendar";
import {
    IconSearch,
    IconUsers,
    IconX,
    IconTrophy,
    IconCalendar,
    IconFilterOff
} from "@tabler/icons-vue";
import { ref, watch } from "vue";
import { DateFormatter, getLocalTimeZone, parseDate } from '@internationalized/date';

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    rekap: {
        data: Array<any>;
        links: Array<any>;
        from: number;
        to: number;
        total: number;
    };
    filters: {
        search?: string;
        date_start?: string;
        date_end?: string;
    };
}>();

// Formatter untuk tampilan tanggal di button (Bahasa Indonesia)
const df = new DateFormatter('id-ID', { dateStyle: 'medium' });

// State untuk filter
const search = ref(props.filters.search || "");
const dateStart = ref(props.filters.date_start ? parseDate(props.filters.date_start) : undefined);
const dateEnd = ref(props.filters.date_end ? parseDate(props.filters.date_end) : undefined);

let timeout: any;

// Fungsi utama untuk kirim data ke Laravel
const updateFilters = () => {
    router.get(
        route("total.pengerjaan.user"),
        {
            search: search.value,
            date_start: dateStart.value?.toString(),
            date_end: dateEnd.value?.toString()
        },
        {
            preserveState: true,
            replace: true,
            only: ['rekap', 'filters'] // Agar lebih cepat, hanya update data table
        }
    );
};

// Watcher untuk search (dengan debounce)
watch(search, (value) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        updateFilters();
    }, 500);
});

// Watcher untuk tanggal (langsung update saat dipilih)
watch([dateStart, dateEnd], () => {
    updateFilters();
});

const clearSearch = () => {
    search.value = "";
};

const resetFilters = () => {
    search.value = "";
    dateStart.value = undefined;
    dateEnd.value = undefined;
};

const cleanLabel = (label: string) => {
    if (label.includes("Previous")) return "Sebelumnya";
    if (label.includes("Next")) return "Selanjutnya";
    return label;
};
</script>

<template>
    <Head title="Rekap Produk Per User" />

    <div class="flex flex-col gap-4 p-4 md:p-8 pt-4">
        <Card class="border-none shadow-sm">
            <CardHeader
                class="flex flex-col lg:flex-row items-start lg:items-center justify-between space-y-4 lg:space-y-0 pb-6"
            >
                <CardTitle class="text-xl font-bold flex items-center gap-2">
                    <IconUsers class="size-6 text-primary" />
                    Pencapaian Kerja Per Personel
                </CardTitle>

                <div class="flex flex-col md:flex-row items-center gap-3 w-full lg:w-auto">
                    <div class="flex items-center gap-2 w-full md:w-auto">
                        <Popover>
                            <PopoverTrigger as-child>
                                <Button variant="outline" class="w-full md:w-[160px] justify-start text-left font-normal h-10">
                                    <IconCalendar class="mr-2 size-4 text-muted-foreground" />
                                    {{ dateStart ? df.format(dateStart.toDate(getLocalTimeZone())) : "Mulai" }}
                                </Button>
                            </PopoverTrigger>
                            <PopoverContent class="w-auto p-0" align="start">
                                <Calendar v-model="dateStart" />
                            </PopoverContent>
                        </Popover>

                        <span class="text-muted-foreground text-xs font-bold">s/d</span>

                        <Popover>
                            <PopoverTrigger as-child>
                                <Button variant="outline" class="w-full md:w-[160px] justify-start text-left font-normal h-10">
                                    <IconCalendar class="mr-2 size-4 text-muted-foreground" />
                                    {{ dateEnd ? df.format(dateEnd.toDate(getLocalTimeZone())) : "Selesai" }}
                                </Button>
                            </PopoverTrigger>
                            <PopoverContent class="w-auto p-0" align="end">
                                <Calendar v-model="dateEnd" />
                            </PopoverContent>
                        </Popover>
                    </div>

                    <div class="flex items-center gap-2 w-full md:w-auto">
                        <div class="relative w-full md:w-64">
                            <IconSearch
                                class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground"
                            />
                            <Input
                                v-model="search"
                                placeholder="Cari nama..."
                                class="pl-10 pr-10 h-10"
                            />
                            <button
                                v-if="search"
                                @click="clearSearch"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground"
                            >
                                <IconX class="size-4" />
                            </button>
                        </div>

                        <Button
                            v-if="search || dateStart || dateEnd"
                            variant="ghost"
                            size="icon"
                            @click="resetFilters"
                            title="Reset Filter"
                            class="text-red-500 hover:text-red-600 hover:bg-red-50"
                        >
                            <IconFilterOff class="size-5" />
                        </Button>
                    </div>
                </div>
            </CardHeader>

            <CardContent>
                <div class="rounded-lg border overflow-hidden">
                    <Table>
                        <TableHeader>
                            <TableRow class="bg-muted/50">
                                <TableHead class="w-[80px] text-center text-xs uppercase font-bold tracking-wider">Rank</TableHead>
                                <TableHead class="text-xs uppercase font-bold tracking-wider">Nama Personel</TableHead>
                                <TableHead class="text-center text-xs uppercase font-bold tracking-wider">Total Output</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow
                                v-for="(item, index) in rekap.data"
                                :key="item.user.id"
                                class="hover:bg-muted/30 transition-colors"
                            >
                                <TableCell class="text-center font-bold text-muted-foreground">
                                    {{ rekap.from + index }}
                                </TableCell>
                                <TableCell>
                                    <div class="flex items-center gap-3">
                                        <div class="size-9 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs border border-primary/20">
                                            {{ item.user.name.substring(0, 2).toUpperCase() }}
                                        </div>
                                        <span class="font-semibold text-slate-700 tracking-tight">{{ item.user.name }}</span>
                                    </div>
                                </TableCell>
                                <TableCell class="text-center">
                                    <Badge class="bg-blue-50 text-blue-700 border-blue-200 hover:bg-blue-100 px-4 py-1">
                                        <IconTrophy class="size-3.5 mr-1.5" />
                                        {{ item.total_pengerjaan }} Produk
                                    </Badge>
                                </TableCell>
                            </TableRow>

                            <TableRow v-if="rekap.data.length === 0">
                                <TableCell colspan="3" class="text-center py-20">
                                    <div class="flex flex-col items-center gap-2 text-muted-foreground">
                                        <IconFilterOff class="size-10 opacity-20" />
                                        <p>Data tidak ditemukan untuk periode ini.</p>
                                    </div>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>

                <div class="flex flex-col md:flex-row items-center justify-between gap-4 mt-6">
                    <p class="text-xs text-muted-foreground font-medium">
                        Menampilkan {{ rekap.from }} - {{ rekap.to }} dari {{ rekap.total }} personel
                    </p>
                    <nav class="flex items-center gap-1">
                        <template v-for="(link, k) in rekap.links" :key="k">
                            <Button
                                v-if="link.url === null"
                                variant="outline" size="sm" disabled
                                class="opacity-50 text-xs px-3 h-8"
                                v-html="cleanLabel(link.label)"
                            />
                            <Button
                                v-else
                                as-child variant="outline" size="sm"
                                class="text-xs px-3 h-8"
                                :class="{ 'bg-primary text-white border-primary': link.active }"
                            >
                                <Link :href="link.url" v-html="cleanLabel(link.label)" />
                            </Button>
                        </template>
                    </nav>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
