<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import {
    IconArrowLeft,
    IconEdit,
    IconClock,
    IconUser,
    IconCategory,
    IconCalendarTime,
} from "@tabler/icons-vue";
import dayjs from "dayjs"; // Opsional: gunakan library date untuk format lebih cantik

const props = defineProps<{
    sesikerja: any;
}>();

defineOptions({ layout: AuthenticatedLayout });

// Fungsi sederhana untuk format tanggal jika tidak pakai dayjs
const formatDate = (dateString: string | null) => {
    if (!dateString) return "-";
    return new Date(dateString).toLocaleString("id-ID", {
        dateStyle: "medium",
        timeStyle: "short",
    });
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
                    <p class="text-muted-foreground">Informasi lengkap sesi pengerjaan</p>
                </div>
            </div>

            <Button as-child variant="default" class="w-fit">
                <Link :href="route('sesikerjas.edit', sesikerja.id)">
                    <IconEdit class="mr-2 size-4" />
                    Edit Sesi
                </Link>
            </Button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <Card class="md:col-span-2 border-none shadow-lg">
                <CardHeader>
                    <CardTitle class="flex items-center gap-2 text-primary">
                        <IconCalendarTime class="size-5" />
                        Log Waktu
                    </CardTitle>
                </CardHeader>
                <CardContent class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                    <div class="space-y-1">
                        <p class="text-sm text-muted-foreground">Waktu Masuk</p>
                        <p class="text-lg font-semibold italic">
                            {{ formatDate(sesikerja.jam_masuk) }}
                        </p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-sm text-muted-foreground">Waktu Pulang / Selesai</p>
                        <p class="text-lg font-semibold italic">
                            {{ sesikerja.jam_pulang ? formatDate(sesikerja.jam_pulang) : "Masih Berlangsung" }}
                        </p>
                    </div>
                </CardContent>
            </Card>

            <Card class="border-none shadow-lg">
                <CardHeader>
                    <CardTitle class="text-lg">Atribut</CardTitle>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-sm text-muted-foreground">
                            <IconCategory class="size-4" />
                            Jenis
                        </div>
                        <Badge variant="outline" class="font-bold">
                            {{ sesikerja.jenis }}
                        </Badge>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-sm text-muted-foreground">
                            <IconUser class="size-4" />
                            Leader
                        </div>
                        <span class="font-medium text-sm">
                            {{ sesikerja.leader?.name || 'N/A' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-sm text-muted-foreground">
                            <IconClock class="size-4" />
                            Status
                        </div>
                        <Badge
                            :variant="sesikerja.jam_pulang ? 'secondary' : 'default'"
                            class="animate-pulse"
                            v-if="!sesikerja.jam_pulang"
                        >
                            Aktif
                        </Badge>
                        <Badge variant="outline" v-else>Selesai</Badge>
                    </div>
                </CardContent>
            </Card>
        </div>

        </div>
</template>
