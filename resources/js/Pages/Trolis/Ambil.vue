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
    IconShoppingCart,
    IconSearch,
    IconX,
    IconDownload,
    IconArrowLeft,
    IconCheck,
} from "@tabler/icons-vue";
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from "@/components/ui/alert-dialog";
import { ref, watch } from "vue";
import { toast } from "vue-sonner";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    trolis: {
        data: Array<{
            id: number;
            nomor: string;
            keperluan: string;
            jenis: string;
            status: string;
            is_output: boolean;
            proses?: {
                nama_proses: string;
                proses?: string;
            };
            produks_count: number;
            created_at: string;
        }>;
        links: Array<{
            url: string | null;
            label: string;
            active: boolean;
        }>;
        from: number;
        to: number;
        total: number;
    };
    filters: {
        search: string;
    };
}>();

const search = ref(props.filters.search || "");

// Logic Search dengan Debounce
let timeout: ReturnType<typeof setTimeout>;
watch(search, (value) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(
            route("trolis.ambil"),
            { search: value },
            { preserveState: true, replace: true },
        );
    }, 500);
});

const clearSearch = () => {
    search.value = "";
};

const cleanLabel = (label: string) => {
    if (label.includes("Previous")) return "Sebelumnya";
    if (label.includes("Next")) return "Selanjutnya";
    return label;
};

// State Konfirmasi Dialog
const selectedTroli = ref<any>(null);
const isDialogOpen = ref(false);

const confirmAmbil = (troli: any) => {
    selectedTroli.value = troli;
    isDialogOpen.value = true;
};

// Proses Ambil ke Backend
const handleAmbil = () => {
    if (selectedTroli.value) {
        router.post(
            route("trolis.ambilproses"),
            {
                id: selectedTroli.value.id,
            },
            {
                onSuccess: () => {
                    isDialogOpen.value = false;
                    selectedTroli.value = null;
                    toast.success("Troli berhasil diambil ke proses Anda.");
                },
                onError: (errors) => {
                    console.error(errors);
                    toast.error("Gagal mengambil troli.");
                },
            },
        );
    }
};
</script>

<template>
    <Head title="Ambil Troli - SISAMSUL" />

    <AlertDialog :open="isDialogOpen" @update:open="isDialogOpen = $event">
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>Konfirmasi Pengambilan</AlertDialogTitle>
                <AlertDialogDescription>
                    Apakah Anda yakin ingin mengambil troli dengan nomor
                    <span class="font-bold text-primary">{{
                        selectedTroli?.nomor
                    }}</span
                    >? Troli ini akan dipindahkan ke departemen Anda untuk
                    diproses lebih lanjut.
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel @click="selectedTroli = null"
                    >Batal</AlertDialogCancel
                >
                <AlertDialogAction
                    class="bg-primary hover:bg-primary/90"
                    @click="handleAmbil"
                >
                    Ya, Ambil Sekarang
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>

    <div class="flex flex-col gap-4 p-4 md:p-8 pt-4">
        <div class="flex items-center justify-between w-full">
            <Button
                variant="ghost"
                size="sm"
                as-child
                class="hover:bg-transparent p-0"
            >
                <Link
                    :href="route('trolis.index')"
                    class="flex items-center text-muted-foreground hover:text-primary transition-colors"
                >
                    <IconArrowLeft class="size-4 mr-2" />
                    Kembali ke Daftar Troli
                </Link>
            </Button>
        </div>

        <Card class="border-none shadow-sm">
            <CardHeader
                class="flex flex-col md:flex-row items-start md:items-center justify-between space-y-4 md:space-y-0 pb-6"
            >
                <CardTitle class="text-xl font-bold flex items-center gap-2">
                    <IconDownload class="size-6 text-primary" />
                    Ambil Troli (Siap Proses)
                </CardTitle>

                <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                    <div class="relative w-full md:w-80">
                        <IconSearch
                            class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground"
                        />
                        <Input
                            v-model="search"
                            placeholder="Scan QR produk atau cari nomor..."
                            class="pl-10 pr-10 border-primary/20 focus-visible:ring-primary"
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
                                <TableHead>Nomor</TableHead>
                                <TableHead>Keperluan</TableHead>
                                <TableHead>Jenis</TableHead>
                                <TableHead>Tipe</TableHead>
                                <TableHead>Dari Proses</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead>Total Isi</TableHead>
                                <TableHead class="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="trolis.data.length === 0">
                                <TableCell
                                    colspan="8"
                                    class="h-24 text-center text-muted-foreground"
                                >
                                    Tidak ada troli yang tersedia untuk diambil
                                    saat ini.
                                </TableCell>
                            </TableRow>

                            <TableRow
                                v-for="troli in trolis.data"
                                :key="troli.id"
                                class="hover:bg-muted/30 transition-colors"
                            >
                                <TableCell class="font-bold text-primary">{{
                                    troli.nomor
                                }}</TableCell>
                                <TableCell>{{ troli.keperluan }}</TableCell>
                                <TableCell>{{ troli.jenis }}</TableCell>
                                <TableCell>
                                    <Badge
                                        :variant="
                                            troli.is_output
                                                ? 'default'
                                                : 'outline'
                                        "
                                    >
                                        {{
                                            troli.is_output
                                                ? "Output"
                                                : "Sumber"
                                        }}
                                    </Badge>
                                </TableCell>
                                <TableCell>
                                    <Badge
                                        class="bg-lime-500 text-black font-medium"
                                    >
                                        {{
                                            troli.proses?.proses ??
                                            troli.proses?.nama_proses ??
                                            "-"
                                        }}
                                    </Badge>
                                </TableCell>
                                <TableCell>
                                    <Badge
                                        variant="outline"
                                        class="border-lime-500 text-lime-600"
                                    >
                                        {{ troli.status }}
                                    </Badge>
                                </TableCell>
                                <TableCell>
                                    <div class="flex items-center gap-1.5">
                                        <IconShoppingCart
                                            class="size-4 text-muted-foreground"
                                        />
                                        <span class="font-semibold">{{
                                            troli.produks_count
                                        }}</span>
                                    </div>
                                </TableCell>
                                <TableCell class="text-right">
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        class="text-orange-600 border-orange-200 hover:bg-orange-50 gap-2 shadow-sm"
                                        @click="confirmAmbil(troli)"
                                    >
                                        <IconDownload class="size-4" />
                                        Ambil
                                    </Button>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>

                <div
                    v-if="trolis.total > 0"
                    class="mt-6 flex flex-col md:flex-row items-center justify-between gap-4"
                >
                    <div class="text-sm text-muted-foreground">
                        Menampilkan
                        <span class="font-medium text-foreground">{{
                            trolis.from
                        }}</span>
                        -
                        <span class="font-medium text-foreground">{{
                            trolis.to
                        }}</span>
                        dari
                        <span class="font-medium text-foreground">{{
                            trolis.total
                        }}</span>
                        troli tersedia
                    </div>

                    <div
                        class="flex flex-wrap items-center justify-center gap-1"
                    >
                        <template v-for="(link, k) in trolis.links" :key="k">
                            <div
                                v-if="link.url === null"
                                class="px-3 py-1.5 text-xs border rounded-md text-muted-foreground opacity-50 cursor-not-allowed"
                                v-html="cleanLabel(link.label)"
                            />
                            <Link
                                v-else
                                :href="link.url"
                                class="px-3 py-1.5 text-xs border rounded-md transition-all hover:bg-primary hover:text-white"
                                :class="{
                                    'bg-primary text-white border-primary font-bold':
                                        link.active,
                                }"
                                v-html="cleanLabel(link.label)"
                                preserve-scroll
                                preserve-state
                            />
                        </template>
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
