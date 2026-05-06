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
    IconEye,
    IconDownload,
    IconCopy,
    IconCheck,
    IconClock,
    IconInfoCircle,
} from "@tabler/icons-vue";
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from "@/components/ui/alert-dialog";
import { ref, watch, onMounted } from "vue";
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
                id: number;
                proses: string;
            };
            produks_count: number;
            terakhir_diperbaharui: string;
            terakhir_diperbaharui_jam: string;
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
    // Menerima data sesi aktif dari controller
    sesiAktif?: {
        id: number;
        jenis: string;
        proses?: {
            proses: string;
        };
    };
}>();


const search = ref(props.filters.search || "");
const copiedNomor = ref<number | null>(null);

const copyToClipboard = async (text: string, id: number) => {
    try {
        await navigator.clipboard.writeText(text);
        copiedNomor.value = id;
        toast.success("Nomor disalin!");
        setTimeout(() => {
            copiedNomor.value = null;
        }, 2000);
    } catch (err) {
        toast.error("Gagal menyalin");
    }
};

let timeout: ReturnType<typeof setTimeout>;
watch(search, (value) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(
            route("trolis.index"),
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


const isDialogOpen = ref(false);

onMounted(() => {
    // Jika sesiAktif null atau undefined, buka dialog otomatis
    if (!props.sesiAktif) {
        isDialogOpen.value = true;
    }
});
</script>

<template>
    <Head title="Manajemen Troli" />

    <div class="flex flex-col gap-4 p-4 md:p-8 pt-4">

        <div
            v-if="sesiAktif"
            class="bg-primary/10 border border-primary/20 p-4 rounded-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-3 shadow-sm"
        >
            <div class="flex items-center gap-3">
                <div class="bg-primary p-2 rounded-lg">
                    <IconClock class="size-5 text-white" />
                </div>
                <div>
                    <p class="text-xs text-primary font-bold uppercase tracking-wider">Sesi Kerja Aktif</p>
                    <h3 class="text-sm font-semibold">
                        {{ sesiAktif.jenis }} — <span class="text-primary">{{ sesiAktif.proses?.proses }}</span>
                    </h3>
                </div>
            </div>
            <Button variant="outline" size="sm" as-child class="text-xs h-8 border-primary/30 hover:bg-primary/5">
                <Link :href="route('sesikerjas.index')">Ganti Sesi</Link>
            </Button>
        </div>

        <div v-else class="bg-amber-50 border border-amber-200 p-4 rounded-xl flex items-center gap-3 text-amber-800 shadow-sm">
            <IconInfoCircle class="size-5" />
            <p class="text-sm font-medium">Anda belum mengaktifkan sesi kerja. Silakan pilih sesi kerja terlebih dahulu.</p>
        </div>

        <Card class="border-none shadow-sm">
            <CardHeader
                class="flex flex-col md:flex-row items-start md:items-center justify-between space-y-4 md:space-y-0 pb-6"
            >
                <CardTitle class="text-xl font-bold flex items-center gap-2 text-foreground">
                    <IconShoppingCart class="size-6 text-primary" />
                    Manajemen Troli
                </CardTitle>

                <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                    <div class="relative w-full md:w-80">
                        <IconSearch
                            class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground"
                        />
                        <Input
                            v-model="search"
                            placeholder="Cari nomor atau scan produk..."
                            class="pl-10 pr-10 border-primary/20 focus-visible:ring-primary h-10"
                        />
                        <button
                            v-if="search"
                            @click="clearSearch"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                        >
                            <IconX class="size-4" />
                        </button>
                    </div>

                    <Button
                        variant="outline"
                        class="border-primary text-primary hover:bg-primary/10 h-10 shadow-sm"
                        as-child
                    >
                        <Link :href="route('trolis.trolikosong')">
                            <IconShoppingCart class="mr-2 size-4" />
                            Troli Kosong
                        </Link>
                    </Button>

                    <Button class="bg-primary hover:bg-primary/90 h-10 shadow-md" as-child>
                        <Link :href="route('trolis.ambil')">
                            <IconDownload class="mr-2 size-4" />
                            Ambil Troli
                        </Link>
                    </Button>
                </div>
            </CardHeader>

            <CardContent>
                <div class="rounded-lg border overflow-hidden">
                    <Table>
                        <TableHeader>
                            <TableRow class="bg-muted/50 hover:bg-muted/50">
                                <TableHead class="w-[180px]">Nomor Troli</TableHead>
                                <TableHead>Keperluan</TableHead>
                                <TableHead>Proses</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead>Total Produk</TableHead>
                                <TableHead>Update Terakhir</TableHead>
                                <TableHead class="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="trolis.data.length === 0">
                                <TableCell
                                    colspan="7"
                                    class="h-32 text-center text-muted-foreground italic font-medium"
                                >
                                    Tidak ada troli yang tersedia untuk proses ini.
                                </TableCell>
                            </TableRow>

                            <TableRow
                                v-for="troli in trolis.data"
                                :key="troli.id"
                                class="hover:bg-muted/30 transition-colors group"
                            >
                                <TableCell>
                                    <div class="flex items-center gap-3">
                                        <button
                                            @click="copyToClipboard(troli.nomor, troli.id)"
                                            class="inline-flex items-center justify-center size-9 rounded-lg border-2 border-primary/20 bg-primary/5 active:bg-primary active:text-white transition-all shrink-0 hover:scale-105"
                                            :class="{ 'bg-green-500 border-green-600 text-white': copiedNomor === troli.id }"
                                        >
                                            <IconCheck v-if="copiedNomor === troli.id" class="size-5" />
                                            <IconCopy v-else class="size-5 text-primary group-hover:text-primary/80" />
                                        </button>
                                        <span class="font-bold text-primary text-base tracking-tight">
                                            {{ troli.nomor }}
                                        </span>
                                    </div>
                                </TableCell>
                                <TableCell class="font-medium text-foreground/80">{{ troli.keperluan }}</TableCell>
                                <TableCell>
                                    <Badge variant="outline" class="bg-blue-50 text-blue-700 border-blue-200">
                                        {{ troli.proses?.proses ?? "-" }}
                                    </Badge>
                                </TableCell>
                                <TableCell>
                                    <Badge variant="secondary" class="bg-lime-100 text-lime-800 border-lime-200">
                                        {{ troli.status }}
                                    </Badge>
                                </TableCell>
                                <TableCell>
                                    <Badge class="bg-primary/10 text-primary border-primary/20 font-mono">
                                        {{ troli.produks_count }} Produk
                                    </Badge>
                                </TableCell>
                                <TableCell class="text-xs text-muted-foreground leading-relaxed">
                                    <p class="font-medium text-foreground/70">{{ troli.terakhir_diperbaharui_jam }}</p>
                                    <p class="italic text-[10px]">{{ troli.terakhir_diperbaharui }}</p>
                                </TableCell>
                                <TableCell class="text-right">
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        class="hover:bg-primary/10 hover:text-primary transition-colors"
                                        as-child
                                    >
                                        <Link :href="route('trolis.produk.index', troli.id)">
                                            <IconEye class="size-5" />
                                        </Link>
                                    </Button>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>

                <div
                    v-if="trolis.total > 0"
                    class="mt-8 flex flex-col md:flex-row items-center justify-between gap-4 border-t pt-6"
                >
                    <div class="text-xs text-muted-foreground italic font-medium">
                        Menampilkan {{ trolis.from }} - {{ trolis.to }} dari {{ trolis.total }} troli
                    </div>

                    <div class="flex flex-wrap items-center justify-center gap-1">
                        <template v-for="(link, k) in trolis.links" :key="k">
                            <div
                                v-if="link.url === null"
                                class="px-3 py-1.5 text-[10px] uppercase font-bold border rounded-md text-muted-foreground opacity-50 cursor-not-allowed bg-muted/20"
                                v-html="cleanLabel(link.label)"
                            />
                            <Link
                                v-else
                                :href="link.url"
                                class="px-3 py-1.5 text-[10px] uppercase font-bold border rounded-md transition-all hover:bg-primary hover:text-white"
                                :class="{ 'bg-primary text-white border-primary': link.active }"
                                v-html="cleanLabel(link.label)"
                                preserve-scroll
                                preserve-state
                            />
                        </template>
                    </div>
                </div>
            </CardContent>
        </Card>

        <AlertDialog :open="isDialogOpen" @update:open="isDialogOpen = $event">
            <AlertDialogContent class="max-w-[400px]">
                <AlertDialogHeader>
                    <div class="flex justify-center mb-2">
                        <div class="p-3 bg-amber-100 rounded-full">
                            <IconClock class="size-8 text-amber-600 animate-pulse" />
                        </div>
                    </div>
                    <AlertDialogTitle class="text-center text-xl">Sesi Belum Aktif!</AlertDialogTitle>
                    <AlertDialogDescription class="text-center">
                        Maaf, Anda harus mengaktifkan **Sesi Kerja** terlebih dahulu sebelum dapat mengelola troli atau melakukan scan produk.
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter class="flex-col sm:flex-col gap-2">
                    <Button class="w-full bg-primary" as-child>
                        <Link :href="route('sesikerjas.index')">
                            Pilih Sesi Sekarang
                        </Link>
                    </Button>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </div>
</template>
