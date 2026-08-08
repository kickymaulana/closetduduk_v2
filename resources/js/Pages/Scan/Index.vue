<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import {
    IconScan,
    IconShieldCheck,
    IconAlertTriangle,
    IconCircleX,
    IconClipboardCheck,
    IconClock,
} from "@tabler/icons-vue";

const props = defineProps<{
    sesiAktif: any;
}>();

const modes = [
    {
        title: "Scan Awal",
        desc: "Daftarkan produk baru (casting)",
        url: route("scan.awal"),
        icon: IconScan,
        color: "blue",
    },
    {
        title: "Scan Validasi",
        desc: "Produk OK",
        url: route("scan.validasi"),
        icon: IconShieldCheck,
        color: "emerald",
    },
    {
        title: "Scan In Proses",
        desc: "Ada cacat (toleransi)",
        url: route("scan.inproses"),
        icon: IconAlertTriangle,
        color: "orange",
    },
    {
        title: "Scan Buang",
        desc: "Produk dibuang",
        url: route("scan.buang"),
        icon: IconCircleX,
        color: "red",
    },
    {
        title: "Scan Checking",
        desc: "QC + kualitas & warna",
        url: route("scan.checking"),
        icon: IconClipboardCheck,
        color: "violet",
    },
];

defineOptions({ layout: AuthenticatedLayout });
</script>

<template>
    <Head title="Scan Produk" />

    <div class="flex flex-col items-center justify-center min-h-[80vh] p-4">
        <div class="w-full max-w-3xl mb-8 text-center">
            <h1 class="text-3xl font-bold">Scan Produk</h1>
            <p class="text-muted-foreground mt-2">Pilih jenis scan yang ingin dilakukan.</p>

            <div v-if="sesiAktif" class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-full bg-green-50 border border-green-200 text-green-700 text-sm font-semibold">
                <IconClock class="size-4" />
                Sesi Aktif:
                {{ sesiAktif.proses?.proses ?? "Proses tidak diketahui" }}
                ({{ sesiAktif.jenis }})
            </div>
            <div v-else class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-full bg-amber-50 border border-amber-200 text-amber-700 text-sm font-semibold">
                <IconClock class="size-4" />
                Belum ada sesi kerja aktif — aktifkan di menu Sesi Kerja terlebih dahulu.
            </div>
        </div>

        <div class="w-full max-w-3xl grid grid-cols-1 md:grid-cols-2 gap-4">
            <Link v-for="mode in modes" :key="mode.url" :href="mode.url">
                <Card class="cursor-pointer hover:shadow-xl transition-all hover:-translate-y-0.5">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2 text-lg">
                            <component :is="mode.icon" class="size-6" />
                            {{ mode.title }}
                        </CardTitle>
                        <CardDescription>{{ mode.desc }}</CardDescription>
                    </CardHeader>
                    <CardContent class="flex items-center gap-2 text-xs font-semibold">
                        <Button variant="ghost" class="p-0 h-auto font-bold" size="sm">Mulai →</Button>
                    </CardContent>
                </Card>
            </Link>
        </div>
    </div>
</template>
