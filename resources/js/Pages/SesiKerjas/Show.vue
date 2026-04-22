<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";
import {
    IconArrowLeft,
    IconEdit,
    IconClock,
    IconUser,
    IconCategory,
    IconCalendarTime,
    IconUsers,
    IconPackage,
    IconCheck,
    IconX,
    IconHistory,
    IconLoader
} from "@tabler/icons-vue";

const props = defineProps<{
    sesikerja: any;
    stats: {
        total_produk: number;
        total_ok: number;
        total_in_proses: number;
        total_reject: number;
    };
}>();

defineOptions({ layout: AuthenticatedLayout });

const formatDate = (dateString: string | null) => {
    if (!dateString) return "-";
    return new Date(dateString).toLocaleString("id-ID", {
        dateStyle: "medium",
        timeStyle: "short",
    });
};

const getDuration = () => {
    if (!props.sesikerja.jam_masuk || !props.sesikerja.jam_pulang) return null;
    const start = new Date(props.sesikerja.jam_masuk);
    const end = new Date(props.sesikerja.jam_pulang);
    const diffMs = end.getTime() - start.getTime();
    const diffHrs = Math.floor(diffMs / (1000 * 60 * 60));
    const diffMins = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
    return `${diffHrs} Jam ${diffMins} Menit`;
};
</script>

<template>
    <Head title="Detail Sesi Kerja" />

    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <Button variant="outline" size="icon" as-child class="rounded-full">
                    <Link :href="route('sesikerjas.index')">
                        <IconArrowLeft class="size-4" />
                    </Link>
                </Button>
                <div>
                    <h2 class="text-3xl font-bold tracking-tight">Detail Sesi</h2>
                    <p class="text-muted-foreground italic">Monitor pengerjaan unit lengkap</p>
                </div>
            </div>

            <Button as-child variant="default">
                <Link :href="route('sesikerjas.edit', sesikerja.id)">
                    <IconEdit class="mr-2 size-4" />
                    Edit Sesi
                </Link>
            </Button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <Card class="border-none shadow-md bg-blue-50/50 border-blue-100 text-blue-700">
                <CardContent class="p-6 flex items-center gap-4">
                    <IconPackage class="size-8 opacity-70" />
                    <div>
                        <p class="text-xs font-bold uppercase opacity-70">Total Produk</p>
                        <p class="text-3xl font-bold">{{ stats.total_produk }}</p>
                    </div>
                </CardContent>
            </Card>

            <Card class="border-none shadow-md bg-green-50/50 border-green-100 text-green-700">
                <CardContent class="p-6 flex items-center gap-4">
                    <IconCheck class="size-8 opacity-70" />
                    <div>
                        <p class="text-xs font-bold uppercase opacity-70">Kondisi OK</p>
                        <p class="text-3xl font-bold">{{ stats.total_ok }}</p>
                    </div>
                </CardContent>
            </Card>

            <Card class="border-none shadow-md bg-amber-50/50 border-amber-100 text-amber-700">
                <CardContent class="p-6 flex items-center gap-4">
                    <IconLoader class="size-8 opacity-70 animate-spin-slow" />
                    <div>
                        <p class="text-xs font-bold uppercase opacity-70">In Proses</p>
                        <p class="text-3xl font-bold">{{ stats.total_in_proses }}</p>
                    </div>
                </CardContent>
            </Card>

            <Card class="border-none shadow-md bg-red-50/50 border-red-100 text-red-700">
                <CardContent class="p-6 flex items-center gap-4">
                    <IconX class="size-8 opacity-70" />
                    <div>
                        <p class="text-xs font-bold uppercase opacity-70">Reject</p>
                        <p class="text-3xl font-bold">{{ stats.total_reject }}</p>
                    </div>
                </CardContent>
            </Card>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <Card class="md:col-span-2 border-none shadow-lg">
                <CardHeader>
                    <CardTitle class="flex items-center gap-2 text-primary text-lg">
                        <IconCalendarTime class="size-5" /> Log Waktu Kerja
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <p class="text-sm text-muted-foreground">Waktu Masuk</p>
                            <p class="text-lg font-semibold">{{ formatDate(sesikerja.jam_masuk) }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-sm text-muted-foreground">Waktu Pulang</p>
                            <p class="text-lg font-semibold">
                                {{ sesikerja.jam_pulang ? formatDate(sesikerja.jam_pulang) : "Sedang Berlangsung..." }}
                            </p>
                        </div>
                        <div v-if="sesikerja.jam_pulang" class="sm:col-span-2 mt-2 p-3 bg-primary/5 rounded-lg border-l-4 border-primary">
                            <p class="text-sm font-medium flex items-center gap-2">
                                <IconClock class="size-4" /> Durasi Kerja: {{ getDuration() }}
                            </p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <div class="flex flex-col gap-6">
                <Card class="border-none shadow-lg">
                    <CardContent class="p-6 space-y-4">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-muted-foreground flex items-center gap-2"><IconCategory class="size-4"/> Jenis</span>
                            <Badge variant="outline" class="font-bold font-mono">{{ sesikerja.jenis }}</Badge>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-muted-foreground flex items-center gap-2"><IconUser class="size-4"/> Leader</span>
                            <span class="font-bold">{{ sesikerja.leader?.name }}</span>
                        </div>
                    </CardContent>
                </Card>

                <Card class="border-none shadow-lg">
                    <CardHeader class="pb-2"><CardTitle class="text-base flex items-center gap-2"><IconUsers class="size-4"/> Anggota Tim</CardTitle></CardHeader>
                    <CardContent>
                        <div class="flex flex-wrap gap-1.5">
                            <Badge v-for="member in sesikerja.sesi_kerja_members" :key="member.id" variant="secondary" class="font-normal">
                                {{ member.user?.name }}
                            </Badge>
                            <p v-if="!sesikerja.sesi_kerja_members?.length" class="text-xs text-muted-foreground italic">Tidak ada anggota.</p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>

        <Card class="border-none shadow-lg">
            <CardHeader>
                <CardTitle class="text-lg flex items-center gap-2">
                    <IconHistory class="size-5 text-primary" /> Riwayat Scan Produk
                </CardTitle>
            </CardHeader>
            <CardContent>
                <div class="rounded-lg border overflow-hidden">
                    <Table>
                        <TableHeader>
                            <TableRow class="bg-muted/50">
                                <TableHead class="w-[180px]">Waktu Scan</TableHead>
                                <TableHead>QR Code</TableHead>
                                <TableHead>Departemen / Proses</TableHead>
                                <TableHead class="text-right">Kondisi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow
                                v-for="log in (sesikerja.pengerjaan_produks || []).filter(p => p.user_id === sesikerja.leader_id)"
                                :key="log.id"
                            >
                                <TableCell class="text-[11px] text-muted-foreground font-mono">
                                    {{ formatDate(log.created_at) }}
                                </TableCell>
                                <TableCell class="font-bold text-primary">{{ log.produk?.qrcode }}</TableCell>
                                <TableCell>
                                    <Badge variant="outline" class="font-normal">{{ log.proses?.nama || 'N/A' }}</Badge>
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
                            <TableRow v-if="!sesikerja.pengerjaan_produks?.length">
                                <TableCell colspan="4" class="text-center py-12 text-muted-foreground italic">Belum ada aktivitas scan pada sesi ini.</TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </CardContent>
        </Card>
    </div>
</template>

<style scoped>
.animate-spin-slow {
    animation: spin 3s linear infinite;
}
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
