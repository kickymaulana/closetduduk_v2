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
    IconPackage, // Ikon untuk Produk
    IconSearch,
    IconX,
    IconQrcode, // Ikon Scan
    IconArrowLeft,
    IconSettings,
    IconDownload,
    IconTrash,
    IconDotsVertical,
    IconCheck,
    IconCircleCheck,
    IconTransfer,
    IconArrowBackUp,
    IconBasketX,
} from "@tabler/icons-vue";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from "@/components/ui/alert-dialog";
import { ref, watch } from "vue";
import { toast } from "vue-sonner";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    troli: {
        id: number;
        invoice: string;
        proses?: {
            id: number;
            proses: string; // Sesuaikan dengan nama kolom nama proses di tabel kamu
            urutan: number;
        };
    };
    produks: {
        data: Array<{
            id: number;
            qrcode: string;
            nama: string;
            jenis: string;
            status_akhir: string;
            sudah_scan: string;
            created_at: string;
        }>;
        links: any[];
        total: number;
    };
    filters: {
        search: string;
    };
}>();

const search = ref(props.filters.search || "");

let timeout: any;
watch(search, (value) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(
            route("trolis.produk.index", props.troli.id),
            { search: value },
            { preserveState: true, replace: true },
        );
    }, 500);
});

const clearSearch = () => { search.value = ""; };


const confirmSelesai = () => {
    router.post(route('trolis.selesaikan', props.troli.id), {}, {
        onSuccess: () => {
            toast.success("Berhasil!", {
                description: "Troli berhasil pindahkan ke proses selanjutnya.",
            });
        },


        onError: (errors) => {
            const message = errors.qr || errors.error || "Terjadi kesalahan sistem.";
            // Sonner Error
            toast.error("Gagal Scan", {
                description: message,
            });
        }

    });
};

const confirmHapus = () => {
    router.post(route('trolis.hapus_store', props.troli.id), {}, {
        onSuccess: () => {
            toast.success("Berhasil!", {
                description: "Troli berhasil dihapus.",
            });
        },
        onError: (errors) => {
            // Ini akan menangkap error "Troli masih berisi produk" dari controller
            const message = errors.error || "Gagal menghapus troli.";
            toast.error("Gagal", {
                description: message,
            });
        }
    });
};

</script>

<template>
    <Head :title="'Produk Troli - ' + troli.invoice" />

    <div class="flex flex-col gap-4 p-4 md:p-8 pt-4">

        <div class="flex items-center justify-between w-full">
            <Button variant="ghost" size="sm" as-child>
                <Link :href="route('trolis.index')">
                    <IconArrowLeft class="size-4 mr-2" />
                    Kembali ke Daftar Troli
                </Link>
            </Button>

            <DropdownMenu>
            <DropdownMenuTrigger as-child>
                <Button variant="outline" size="icon" class="size-8">
                <IconDotsVertical class="size-4" />
                </Button>
            </DropdownMenuTrigger>

            <DropdownMenuContent align="end" class="w-48">
                <DropdownMenuLabel>Opsi Troli</DropdownMenuLabel>
                <DropdownMenuSeparator />

                <AlertDialog>
                <AlertDialogTrigger as-child>
                    <DropdownMenuItem @select.prevent>
                    <IconCircleCheck class="mr-2 size-4 text-green-500" />
                    <span>Selesaikan Troli</span>
                    </DropdownMenuItem>
                </AlertDialogTrigger>

                <AlertDialogContent>
                    <AlertDialogHeader>
                    <AlertDialogTitle>Selesaikan Proses Sekarang?</AlertDialogTitle>
                    <AlertDialogDescription>
                        Troli ini akan dipindahkan ke tahap berikutnya.
                        Semua status produk di dalamnya akan direset menjadi <b>Belum Scan</b> untuk tahap baru.
                    </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                    <AlertDialogCancel>Batal</AlertDialogCancel>
                    <AlertDialogAction @click="confirmSelesai" class="bg-green-600 hover:bg-green-700">
                        Ya, Selesaikan
                    </AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
                </AlertDialog>
                <DropdownMenuItem as-child>
                <Link :href="route('trolis.kembalikan', props.troli.id)">
                    <IconArrowBackUp class="mr-2 size-4 text-green-500" />
                    <span>Kembalikan Troli</span>
                </Link>
                </DropdownMenuItem>

                <DropdownMenuItem as-child>
                <Link :href="route('trolis.produk.scan_pindah', props.troli.id)">
                    <IconTransfer class="mr-2 size-4 text-green-500" />
                    <span>Pindahkan Produk</span>
                </Link>
                </DropdownMenuItem>
                <DropdownMenuItem as-child>
                <Link :href="route('trolis.produk.scan_hapus', props.troli.id)">
                    <IconBasketX class="mr-2 size-4 text-red-500" />
                    <span>Hapus Produk</span>
                </Link>
                </DropdownMenuItem>


                <DropdownMenuSeparator />
                <AlertDialog>
                    <AlertDialogTrigger as-child>
                        <DropdownMenuItem @select.prevent class="text-red-600 focus:text-red-600">
                            <IconTrash class="mr-2 size-4" />
                            <span>Hapus Troli</span>
                        </DropdownMenuItem>
                    </AlertDialogTrigger>
                    <AlertDialogContent>
                        <AlertDialogHeader>
                            <AlertDialogTitle>Hapus Troli {{ troli.invoice }}?</AlertDialogTitle>
                            <AlertDialogDescription>
                                Tindakan ini tidak dapat dibatalkan. Troli hanya bisa dihapus jika <b>tidak ada produk</b> di dalamnya.
                                Status fisik troli di sistem akan diubah menjadi <b>"Tidak"</b> (Tersedia).
                            </AlertDialogDescription>
                        </AlertDialogHeader>
                        <AlertDialogFooter>
                            <AlertDialogCancel>Batal</AlertDialogCancel>
                            <AlertDialogAction @click="confirmHapus" class="bg-red-600 hover:bg-red-700">
                                Ya, Hapus Troli
                            </AlertDialogAction>
                        </AlertDialogFooter>
                    </AlertDialogContent>
                </AlertDialog>
            </DropdownMenuContent>
            </DropdownMenu>


        </div>

        <Card class="border-none shadow-sm">
            <CardHeader class="flex flex-col md:flex-row items-start md:items-center justify-between space-y-4 md:space-y-0 pb-6">
                <CardTitle class="text-xl font-bold flex flex-col gap-1">
                    <div class="flex items-center gap-2">
                        <IconPackage class="size-6 text-primary" />
                        Isi Troli: {{ troli.invoice }}
                    </div>
                    <span class="text-sm font-normal text-muted-foreground">Total Produk: {{ produks.total }} |
                        <Badge>
                        Proses: {{ troli.proses?.proses }}
                        </Badge>
                    </span>
                </CardTitle>

                <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                    <div class="relative w-full md:w-64">
                        <IconSearch class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
                        <Input v-model="search" placeholder="Cari QR Code / Nama..." class="pl-10 pr-10" />
                        <button v-if="search" @click="clearSearch" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground">
                            <IconX class="size-4" />
                        </button>
                    </div>


                    <Button
                        v-if="props.troli.proses?.proses === 'Casting'"
                        as-child
                        class="bg-primary hover:bg-primary/90 shadow-md transition-all active:scale-95"
                    >
                        <Link :href="route('trolis.produk.scan_awal', props.troli.id)">
                            <IconQrcode class="mr-2 size-4" />
                            <span class="hidden sm:inline">Scan</span>
                        </Link>
                    </Button>

                    <Button
                        v-else
                        as-child
                        class="bg-primary hover:bg-primary/90 shadow-md transition-all active:scale-95"
                    >
                        <Link :href="route('trolis.produk.scan', props.troli.id)">
                            <IconQrcode class="mr-2 size-4" />
                            <span class="hidden sm:inline">Scan Validasi</span>
                        </Link>
                    </Button>


                </div>
            </CardHeader>

            <CardContent>
                <div class="rounded-lg border overflow-hidden">
                    <Table>
                        <TableHeader>
                            <TableRow class="bg-muted/50">
                                <TableHead>QR Code</TableHead>
                                <TableHead>Jenis</TableHead>
                                <TableHead>Status Akhir</TableHead>
                                <TableHead>Scan</TableHead>
                                <TableHead>Tgl Masuk</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="produks.data.length === 0">
                                <TableCell colspan="6" class="h-24 text-center text-muted-foreground">
                                    Belum ada produk di dalam troli ini.
                                </TableCell>
                            </TableRow>

                            <TableRow v-for="item in produks.data" :key="item.id" class="hover:bg-muted/30 transition-colors">
                                <TableCell class="font-mono font-bold">{{ item.qrcode }}</TableCell>
                                <TableCell>{{ item.jenis }}</TableCell>
                                <TableCell>
                                    <Badge :variant="item.status_akhir === 'OK' ? 'default' : 'destructive'">
                                        {{ item.status_akhir }}
                                    </Badge>
                                </TableCell>
                                <TableCell>
                                    <Badge :class="item.sudah_scan === 'Sudah' ? 'bg-green-500' : 'bg-yellow-500'">
                                        {{ item.sudah_scan }}
                                    </Badge>
                                </TableCell>
                                <TableCell class="text-muted-foreground text-sm">
                                    {{ new Date(item.created_at).toLocaleDateString('id-ID') }}
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
