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
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Badge } from "@/components/ui/badge";
import {
    IconArrowLeft,
    IconSearch,
    IconX,
    IconHistory,
} from "@tabler/icons-vue";
import { ref, watch } from "vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    sesikerja: any;
    riwayat: {
        data: Array<{
            id: number;
            created_at: string;
            status_kondisi: string;
            produk?: { qrcode: string };
            proses?: { proses: string };
        }>;
        links: any[];
        from: number;
        to: number;
        total: number;
    };
    filters: { search: string };
}>();

const search = ref(props.filters.search || "");
let timeout: any;

watch(search, (value) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(
            route("sesikerjas.riwayat_scan", props.sesikerja.id),
            { search: value },
            { preserveState: true, replace: true },
        );
    }, 500);
});

const clearSearch = () => {
    search.value = "";
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleString("id-ID", {
        dateStyle: "medium",
        timeStyle: "short",
    });
};

const cleanLabel = (label: string) => {
    if (label.includes("Previous")) return "Sebelumnya";
    if (label.includes("Next")) return "Selanjutnya";
    return label;
};
</script>

<template>
    <Head title="Riwayat Scan Lengkap" />

    <div class="flex flex-col gap-4 p-4 md:p-8 pt-4">
        <div class="flex items-center gap-4 mb-2">
            <Button variant="outline" size="icon" as-child class="rounded-full">
                <Link :href="route('sesikerjas.show', sesikerja.id)">
                    <IconArrowLeft class="size-4" />
                </Link>
            </Button>
            <div>
                <h2 class="text-2xl font-bold tracking-tight">Riwayat Scan Lengkap</h2>
                <p class="text-sm text-muted-foreground">Sesi: {{ sesikerja.jenis }} - Leader: {{ sesikerja.leader?.name }}</p>
            </div>
        </div>

        <Card class="border-none shadow-sm">
            <CardHeader
                class="flex flex-col md:flex-row items-start md:items-center justify-between space-y-4 md:space-y-0 pb-6"
            >
                <CardTitle class="text-xl font-bold flex items-center gap-2">
                    <IconHistory class="size-6 text-primary" />
                    Log Aktivitas Produksi
                </CardTitle>

                <div class="flex items-center gap-2 w-full md:w-auto">
                    <div class="relative w-full md:w-80">
                        <IconSearch
                            class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground"
                        />
                        <Input
                            v-model="search"
                            placeholder="Cari QR Code Produk..."
                            class="pl-10 pr-10"
                        />
                        <button
                            v-if="search"
                            @click="clearSearch"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                        >
                            <IconX class="size-4" />
                        </button>
                    </div>
                </div>
            </CardHeader>

            <CardContent>
                <div class="rounded-lg border overflow-hidden">
                    <Table>
                        <TableHeader>
                            <TableRow class="bg-muted/50">
                                <TableHead class="w-[200px]">Waktu Scan</TableHead>
                                <TableHead>QR Code</TableHead>
                                <TableHead>Proses / Departemen</TableHead>
                                <TableHead class="text-right">Status Kondisi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="riwayat.data.length === 0">
                                <TableCell
                                    colspan="4"
                                    class="h-24 text-center text-muted-foreground italic"
                                >
                                    Data riwayat scan tidak ditemukan.
                                </TableCell>
                            </TableRow>

                            <TableRow
                                v-for="log in riwayat.data"
                                :key="log.id"
                                class="hover:bg-muted/30 transition-colors"
                            >
                                <TableCell class="text-sm font-mono text-muted-foreground">
                                    {{ formatDate(log.created_at) }}
                                </TableCell>
                                <TableCell class="font-bold text-primary tracking-wider">
                                    {{ log.produk?.qrcode || 'N/A' }}
                                </TableCell>
                                <TableCell>
                                    <Badge variant="outline" class="font-normal uppercase text-[10px]">
                                        {{ log.proses?.proses || 'N/A' }}
                                    </Badge>
                                </TableCell>
                                <TableCell class="text-right">
                                    <Badge
                                        :variant="log.status_kondisi === 'OK' ? 'default' : (log.status_kondisi === 'Buang' ? 'destructive' : 'secondary')"
                                        :class="log.status_kondisi === 'In Proses' ? 'bg-amber-500 text-white hover:bg-amber-600 border-none' : ''"
                                    >
                                        {{ log.status_kondisi }}
                                    </Badge>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>

                <div class="flex flex-col md:flex-row items-center justify-between gap-4 mt-6">
                    <p class="text-xs text-muted-foreground italic font-medium">
                        Menampilkan {{ riwayat.from ?? 0 }} -
                        {{ riwayat.to ?? 0 }} dari {{ riwayat.total }} aktivitas scan
                    </p>

                    <nav class="flex items-center gap-1">
                        <template v-for="(link, k) in riwayat.links" :key="k">
                            <Button
                                v-if="link.url === null"
                                variant="outline"
                                size="sm"
                                disabled
                                class="opacity-50 text-xs px-3 h-8"
                                v-html="cleanLabel(link.label)"
                            />
                            <Button
                                v-else
                                as-child
                                variant="outline"
                                size="sm"
                                class="text-xs px-3 h-8 transition-all"
                                :class="{
                                    'bg-primary text-primary-foreground hover:bg-primary/90 shadow-sm':
                                        link.active,
                                }"
                            >
                                <Link
                                    :href="link.url"
                                    v-html="cleanLabel(link.label)"
                                />
                            </Button>
                        </template>
                    </nav>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
