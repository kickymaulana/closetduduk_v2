<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref, watch, computed } from "vue";
import { toast } from "vue-sonner";
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
    IconPackage,
    IconSearch,
    IconX,
    IconArrowLeft,
    IconTrash,
    IconTransfer,
    IconCheck,
    IconSettings,
    IconRefresh,
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
} from "@/components/ui/alert-dialog";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    troli: any;
    produks: { data: any[]; total: number };
    availableTrolis: any[];
    allProses: any[];
    filters: { search: string };
}>();

const search = ref(props.filters.search || "");
const selectedIds = ref<number[]>([]);
const targetTroliId = ref<string>("");
const selectedProsesId = ref<string>(props.troli.proses_id?.toString() || "");

const showMoveDialog = ref(false);
const showDeleteTroliDialog = ref(false);
const showChangeProsesDialog = ref(false);

/**
 * LOGIKA SELEKSI (HTML CHECKBOX)
 */
const isAllSelected = computed(() => {
    return (
        props.produks.data.length > 0 &&
        selectedIds.value.length === props.produks.data.length
    );
});

const toggleAll = (event: Event) => {
    const isChecked = (event.target as HTMLInputElement).checked;
    if (isChecked) {
        selectedIds.value = props.produks.data.map((p) => p.id);
    } else {
        selectedIds.value = [];
    }
};

/**
 * ACTIONS
 */
const changeProses = () => {
    router.post(
        route("master.troli.update_proses", props.troli.id),
        { proses_id: selectedProsesId.value },
        {
            onSuccess: () => {
                toast.success("Proses diperbarui");
                showChangeProsesDialog.value = false;
            },
        },
    );
};

const updateScanStatus = (sudah_scan: "Sudah" | "Belum") => {
    if (selectedIds.value.length === 0)
        return toast.error("Pilih produk terlebih dahulu!");

    router.post(
        route("master.troli.update_scan", props.troli.id),
        {
            ids: selectedIds.value,
            sudah_scan: sudah_scan,
        },
        {
            onSuccess: () => {
                toast.success(`Berhasil mengubah status ke: ${sudah_scan}`);
                selectedIds.value = []; // Reset pilihan setelah berhasil
            },
        },
    );
};

const moveProducts = () => {
    if (!targetTroliId.value) return toast.error("Pilih troli tujuan!");

    router.post(
        route("master.troli.move_products", props.troli.id),
        {
            ids: selectedIds.value,
            troli_id: targetTroliId.value, // Sesuaikan dengan request->validate di controller
        },
        {
            onSuccess: () => {
                toast.success("Produk berhasil dipindahkan");
                showMoveDialog.value = false;
                selectedIds.value = [];
                targetTroliId.value = "";
                searchTroli.value = ""; // Reset pencarian
            },
        },
    );
};

const removeProducts = () => {
    router.post(
        route("master.troli.remove_products", props.troli.id),
        { ids: selectedIds.value },
        {
            onSuccess: () => {
                toast.success("Produk dikeluarkan");
                selectedIds.value = [];
            },
        },
    );
};

const deleteTroli = () => {
    router.post(
        route("master.troli.hapus_troli", props.troli.id),
        {},
        {
            onSuccess: (page) => {
                const flash = (page.props as any).flash;
                if (flash?.error) return toast.error(flash.error);
                toast.success("Troli direset");
                router.get(route("master.troli.index"));
            },
        },
    );
};

watch(search, (val) => {
    router.get(
        route("master.troli.produk", props.troli.id),
        { search: val },
        { preserveState: true },
    );
});

const searchTroli = ref(""); // State untuk input pencarian troli

// Computed untuk memfilter daftar troli berdasarkan input user
const filteredTrolis = computed(() => {
    return props.availableTrolis.filter((t) =>
        t.nomor.toLowerCase().includes(searchTroli.value.toLowerCase()),
    );
});
</script>

<template>
    <Head :title="'Produk Troli ' + troli.nomor" />

    <div class="flex flex-col gap-4 p-4 md:p-8 pt-4">
        <div class="flex items-center">
            <Button variant="ghost" size="sm" as-child>
                <Link :href="route('master.troli.index')"
                    ><IconArrowLeft class="size-4 mr-2" /> Kembali</Link
                >
            </Button>
        </div>

        <Card class="border-none shadow-sm">
            <CardHeader
                class="flex flex-col md:flex-row items-center justify-between pb-6 border-b space-y-4 md:space-y-0"
            >
                <CardTitle class="flex flex-col gap-1">
                    <div
                        class="flex items-center gap-2 text-xl font-bold text-primary"
                    >
                        <IconPackage class="size-6" /> {{ troli.nomor }}
                    </div>
                    <div class="flex items-center gap-2">
                        <Badge
                            variant="outline"
                            class="bg-blue-50 text-blue-700"
                            >{{ troli.proses?.proses ?? "Belum Set" }}</Badge
                        >
                        <span class="text-sm text-muted-foreground"
                            >{{ produks.total }} Produk</span
                        >
                    </div>
                </CardTitle>

                <div class="flex items-center gap-2 w-full md:w-auto">
                    <div class="relative flex-1 md:w-64">
                        <IconSearch
                            class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground"
                        />
                        <Input
                            v-model="search"
                            placeholder="Cari..."
                            class="pl-10"
                        />
                    </div>
                    <DropdownMenu>
                        <DropdownMenuTrigger as-child
                            ><Button variant="outline" size="icon"
                                ><IconSettings class="size-4" /></Button
                        ></DropdownMenuTrigger>
                        <DropdownMenuContent align="end" class="w-64">
                            <DropdownMenuLabel>Aksi Troli</DropdownMenuLabel>
                            <DropdownMenuItem
                                @click="showChangeProsesDialog = true"
                                ><IconRefresh class="mr-2 size-4" /> Ganti
                                Proses</DropdownMenuItem
                            >
                            <DropdownMenuItem
                                @click="showDeleteTroliDialog = true"
                                class="text-red-600"
                                ><IconTrash class="mr-2 size-4" /> Reset
                                Troli</DropdownMenuItem
                            >
                            <DropdownMenuSeparator />
                            <DropdownMenuItem
                                @click="showMoveDialog = true"
                                :disabled="selectedIds.length === 0"
                                ><IconTransfer class="mr-2 size-4" />
                                Pindahkan</DropdownMenuItem
                            >

                            <DropdownMenuItem
                                @click="updateScanStatus('Sudah')"
                                :disabled="selectedIds.length === 0"
                            >
                                <IconCheck class="mr-2 size-4 text-green-500" />
                                Setel Sudah Scan
                            </DropdownMenuItem>

                            <DropdownMenuItem
                                @click="updateScanStatus('Belum')"
                                :disabled="selectedIds.length === 0"
                            >
                                <IconX class="mr-2 size-4 text-yellow-600" />
                                Setel Belum Scan
                            </DropdownMenuItem>

                            <DropdownMenuItem
                                @click="removeProducts"
                                :disabled="selectedIds.length === 0"
                                class="text-red-600"
                                ><IconTrash class="mr-2 size-4" />
                                Keluarkan</DropdownMenuItem
                            >
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            </CardHeader>

            <CardContent class="pt-6">
                <div class="rounded-lg border bg-background">
                    <Table>
                        <TableHeader>
                            <TableRow class="bg-muted/50">
                                <TableHead class="w-12 text-center">
                                    <input
                                        type="checkbox"
                                        class="size-4 rounded border-gray-300"
                                        :checked="isAllSelected"
                                        @change="toggleAll"
                                    />
                                </TableHead>
                                <TableHead>QR Code</TableHead>
                                <TableHead>Jenis</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead>Scan</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow
                                v-for="item in produks.data"
                                :key="item.id"
                                :class="{
                                    'bg-primary/5': selectedIds.includes(
                                        item.id,
                                    ),
                                }"
                            >
                                <TableCell class="text-center">
                                    <input
                                        type="checkbox"
                                        class="size-4 rounded border-gray-300"
                                        :value="item.id"
                                        v-model="selectedIds"
                                    />
                                </TableCell>
                                <TableCell class="font-mono font-bold">{{
                                    item.qrcode
                                }}</TableCell>
                                <TableCell>{{ item.jenis }}</TableCell>
                                <TableCell
                                    ><Badge
                                        :variant="
                                            item.status_akhir === 'OK'
                                                ? 'default'
                                                : 'destructive'
                                        "
                                        >{{ item.status_akhir }}</Badge
                                    ></TableCell
                                >
                                <TableCell
                                    ><Badge
                                        :class="
                                            item.sudah_scan === 'Sudah'
                                                ? 'bg-green-500 text-white'
                                                : 'bg-yellow-500 text-white'
                                        "
                                        >{{ item.sudah_scan }}</Badge
                                    ></TableCell
                                >
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </CardContent>
        </Card>
    </div>

    <AlertDialog
        :open="showChangeProsesDialog"
        @update:open="showChangeProsesDialog = $event"
    >
        <AlertDialogContent>
            <AlertDialogHeader
                ><AlertDialogTitle
                    >Ganti Proses</AlertDialogTitle
                ></AlertDialogHeader
            >
            <div class="py-4">
                <Select v-model="selectedProsesId">
                    <SelectTrigger
                        ><SelectValue placeholder="Pilih Proses"
                    /></SelectTrigger>
                    <SelectContent
                        ><SelectItem
                            v-for="p in allProses"
                            :key="p.id"
                            :value="p.id.toString()"
                            >{{ p.proses }}</SelectItem
                        ></SelectContent
                    >
                </Select>
            </div>
            <AlertDialogFooter
                ><AlertDialogCancel>Batal</AlertDialogCancel
                ><AlertDialogAction @click="changeProses"
                    >Simpan</AlertDialogAction
                ></AlertDialogFooter
            >
        </AlertDialogContent>
    </AlertDialog>

    <AlertDialog :open="showMoveDialog" @update:open="showMoveDialog = $event">
        <AlertDialogContent class="max-w-md">
            <AlertDialogHeader>
                <AlertDialogTitle>Pindahkan ke Troli Lain</AlertDialogTitle>
                <AlertDialogDescription>
                    Pindahkan {{ selectedIds.length }} produk terpilih.
                </AlertDialogDescription>
            </AlertDialogHeader>

            <div class="py-4 space-y-4">
                <div class="relative">
                    <IconSearch
                        class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground"
                    />
                    <Input
                        v-model="searchTroli"
                        placeholder="Ketik nomor troli..."
                        class="pl-10"
                    />
                </div>

                <Select v-model="targetTroliId">
                    <SelectTrigger>
                        <SelectValue placeholder="Pilih Nomor Troli" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="t in filteredTrolis"
                            :key="t.id"
                            :value="t.id.toString()"
                        >
                            {{ t.nomor }}
                        </SelectItem>
                        <div
                            v-if="filteredTrolis.length === 0"
                            class="p-2 text-center text-sm text-muted-foreground"
                        >
                            Troli tidak ditemukan
                        </div>
                    </SelectContent>
                </Select>
            </div>

            <AlertDialogFooter>
                <AlertDialogCancel @click="searchTroli = ''"
                    >Batal</AlertDialogCancel
                >
                <AlertDialogAction
                    @click="moveProducts"
                    :disabled="!targetTroliId"
                    class="bg-primary"
                >
                    Konfirmasi Pindah
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>

    <AlertDialog
        :open="showDeleteTroliDialog"
        @update:open="showDeleteTroliDialog = $event"
    >
        <AlertDialogContent>
            <AlertDialogHeader
                ><AlertDialogTitle
                    >Reset Troli?</AlertDialogTitle
                ></AlertDialogHeader
            >
            <AlertDialogFooter
                ><AlertDialogCancel>Batal</AlertDialogCancel
                ><AlertDialogAction @click="deleteTroli" class="bg-red-600"
                    >Ya, Reset</AlertDialogAction
                ></AlertDialogFooter
            >
        </AlertDialogContent>
    </AlertDialog>
</template>
