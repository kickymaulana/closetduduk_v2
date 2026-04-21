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
    IconShoppingCartCopy,
    IconSearch,
    IconX,
    IconHandClick, // Ikon untuk aksi ambil
        IconArrowLeft,

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
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Label } from "@/components/ui/label";
import { ref, watch } from "vue";


defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    troliFisiks: {
        data: Array<{
            id: number;
            nomor: string;
            status: 'Tidak' | 'Digunakan';
            created_at: string;
        }>;
        links: any[];
        from: number;
        to: number;
        total: number;
    };
    prosesList: Array<{ id: number, proses: string }>;
    filters: {
        search: string;
    };
}>();

const search = ref(props.filters.search || "");

let timeout: ReturnType<typeof setTimeout>;
watch(search, (value) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(
            route("trolifisiks.index"),
            { search: value },
            { preserveState: true, replace: true },
        );
    }, 500);
});

const clearSearch = () => { search.value = ""; };

// Fungsi untuk aksi ambil troli
const ambilTroli = (id: number) => {
    if (confirm('Apakah Anda yakin ingin mengambil troli ini?')) {
        // Ganti dengan route action yang sesuai nantinya
        console.log("Mengambil troli ID:", id);
    }
};

const cleanLabel = (label: string) => {
    if (label.includes("Previous")) return "Sebelumnya";
    if (label.includes("Next")) return "Selanjutnya";
    return label;
};

const isDialogOpen = ref(false);
const selectedTroliId = ref<number | null>(null);
const selectedProsesId = ref<string>("");
const selectedKeperluan = ref<string>("");

const confirmAmbil = (id: number) => {
    selectedTroliId.value = id;
    // Jika hanya ada 1 proses, otomatis pilihkan agar user tidak repot
    if (props.prosesList.length === 1) {
        selectedProsesId.value = props.prosesList[0].id.toString();
    }
    isDialogOpen.value = true;
};

const executeAmbil = () => {
    if (selectedTroliId.value) {
        router.post(route("trolifisiks.ambil"),
            {
                id: selectedTroliId.value,
                proses_id: selectedProsesId.value,
                keperluan: selectedKeperluan.value,
            },
            {
                onSuccess: () => {
                    isDialogOpen.value = false;
                    selectedTroliId.value = null;
                    selectedProsesId.value = "";
                }
            }
        );
    } else {
        alert("Silahkan pilih proses terlebih dahulu!");
    }
};
</script>

<template>
    <Head title="Daftar Troli Fisik" />

    <div class="flex flex-col gap-4 p-4 md:p-8 pt-4">

                <div class="flex items-center justify-between w-full">
                <Button variant="ghost" size="sm" as-child>
                    <Link :href="route('trolis.index')">
                        <IconArrowLeft class="size-4 mr-2" />
                        Kembali ke Daftar Troli
                    </Link>
                </Button>
        </div>

        <Card class="border-none shadow-sm">
            <CardHeader class="flex flex-col md:flex-row items-start md:items-center justify-between space-y-4 md:space-y-0 pb-6">
                <CardTitle class="text-xl font-bold flex items-center gap-2">
                    <IconShoppingCartCopy class="size-6 text-primary" />
                    Daftar Troli Fisik
                </CardTitle>

                <div class="flex items-center gap-2 w-full md:w-auto">
                    <div class="relative w-full md:w-72">
                        <IconSearch class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
                        <Input v-model="search" placeholder="Cari nomor troli..." class="pl-10 pr-10" />
                        <button v-if="search" @click="clearSearch" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground">
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
                                <TableHead class="w-[100px]">ID</TableHead>
                                <TableHead>Nomor Troli</TableHead>
                                <TableHead>Status Ketersediaan</TableHead>
                                <TableHead class="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="troliFisiks.data.length === 0">
                                <TableCell colspan="4" class="h-24 text-center text-muted-foreground">Troli tidak ditemukan.</TableCell>
                            </TableRow>

                            <TableRow v-for="item in troliFisiks.data" :key="item.id" class="hover:bg-muted/30 transition-colors">
                                <TableCell class="text-muted-foreground">#{{ item.id }}</TableCell>
                                <TableCell class="font-bold text-lg">{{ item.nomor }}</TableCell>
                                <TableCell>
                                    <Badge :variant="item.status === 'Tidak' ? 'outline' : 'destructive'"
                                           :class="item.status === 'Tidak' ? 'border-lime-500 text-lime-600' : ''">
                                        {{ item.status === 'Tidak' ? 'Tersedia' : 'Sedang Digunakan' }}
                                    </Badge>
                                </TableCell>
                                <TableCell class="text-right">
                                    <Button
                                        size="sm"
                                        :disabled="item.status === 'Digunakan'"
                                        @click="confirmAmbil(item.id)"
                                        class="bg-primary hover:bg-primary/90"
                                    >
                                        <IconHandClick class="size-4 mr-2" />
                                        Ambil Troli
                                    </Button>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>


                <AlertDialog :open="isDialogOpen" @update:open="isDialogOpen = $event">
                    <AlertDialogContent>
                        <AlertDialogHeader>
                            <AlertDialogTitle>Konfirmasi Pengambilan</AlertDialogTitle>
                            <AlertDialogDescription>
                                Pilih proses kerja untuk troli ini. Status troli fisik akan berubah menjadi "Digunakan".
                            </AlertDialogDescription>
                        </AlertDialogHeader>

                        <div class="grid grid-cols-2 gap-4 py-4">
                            <div class="grid gap-2">
                                <Label for="proses">Proses Tujuan</Label>
                                <Select v-model="selectedProsesId">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Pilih Proses..." />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="p in prosesList"
                                            :key="p.id"
                                            :value="p.id.toString()"
                                        >
                                            {{ p.proses }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <p v-if="prosesList.length === 0" class="text-xs text-destructive">
                                    Departemen Anda belum memiliki data proses.
                                </p>
                            </div>

                            <div class="grid gap-2">
                                <Label for="keperluan">Keperluan</Label>
                                <Select v-model="selectedKeperluan">
                                    <SelectTrigger id="keperluan">
                                        <SelectValue placeholder="Pilih Keperluan..." />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="OK">OK</SelectItem>
                                        <SelectItem value="In Proses">In Proses</SelectItem>
                                        <SelectItem value="Scan">Scan</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>

                        <AlertDialogFooter>
                            <AlertDialogCancel @click="isDialogOpen = false">Batal</AlertDialogCancel>
                            <AlertDialogAction
                                @click="executeAmbil"
                                :disabled="!selectedProsesId"
                                class="bg-primary text-white hover:bg-primary/90"
                            >
                                Konfirmasi & Ambil
                            </AlertDialogAction>
                        </AlertDialogFooter>
                    </AlertDialogContent>
                </AlertDialog>



                <div class="flex flex-col md:flex-row items-center justify-between gap-4 mt-6">
                    <p class="text-xs text-muted-foreground italic">
                        Menampilkan {{ troliFisiks.from ?? 0 }} - {{ troliFisiks.to ?? 0 }} dari {{ troliFisiks.total }} troli
                    </p>
                    <nav class="flex items-center gap-1">
                        <template v-for="(link, k) in troliFisiks.links" :key="k">
                            <Button v-if="link.url === null" variant="outline" size="sm" disabled class="opacity-50 text-xs px-3 h-8" v-html="cleanLabel(link.label)" />
                            <Button v-else as-child variant="outline" size="sm" class="text-xs px-3 h-8" :class="{ 'bg-primary text-primary-foreground': link.active }">
                                <Link :href="link.url" v-html="cleanLabel(link.label)" />
                            </Button>
                        </template>
                    </nav>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
