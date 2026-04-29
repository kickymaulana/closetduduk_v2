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
import { Checkbox } from "@/components/ui/checkbox";
import {
    IconPackage,
    IconSearch,
    IconX,
    IconArrowLeft,
    IconDotsVertical,
    IconTrash,
    IconTransfer,
    IconCheck,
    IconSettings,
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
import { ref, watch, computed } from "vue";
import { toast } from "vue-sonner";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    troli: any;
    produks: any;
    availableTrolis: Array<{ id: number; nomor: string }>;
    filters: any;
}>();

const search = ref(props.filters.search || "");
const selectedIds = ref<number[]>([]);
const targetTroliId = ref<string>("");

// Dialog States
const showMoveDialog = ref(false);
const showDeleteTroliDialog = ref(false);

// Logic Checkbox
const toggleAll = (checked: boolean) => {
    selectedIds.value = checked ? props.produks.data.map((p: any) => p.id) : [];
};

const toggleSelect = (id: number) => {
    const index = selectedIds.value.indexOf(id);
    if (index > -1) selectedIds.value.splice(index, 1);
    else selectedIds.value.push(id);
};

// Actions
const updateScanStatus = (status: string) => {
    if (selectedIds.value.length === 0)
        return toast.error("Pilih produk terlebih dahulu");
    router.post(
        route("master.troli.update_scan"),
        {
            ids: selectedIds.value,
            status: status,
        },
        {
            onSuccess: () => {
                toast.success("Status scan diperbarui");
                selectedIds.value = [];
            },
        },
    );
};

const moveProducts = () => {
    if (!targetTroliId.value) return toast.error("Pilih troli tujuan");
    router.post(
        route("master.troli.move_products"),
        {
            ids: selectedIds.value,
            troli_id: targetTroliId.value,
        },
        {
            onSuccess: () => {
                toast.success("Produk berhasil dipindahkan");
                showMoveDialog.value = false;
                selectedIds.value = [];
            },
        },
    );
};

const removeProducts = () => {
    if (selectedIds.value.length === 0) return;
    router.post(
        route("master.troli.remove_products"),
        {
            ids: selectedIds.value,
        },
        {
            onSuccess: () => {
                toast.success("Produk dikeluarkan dari troli");
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
            onSuccess: () => {
                toast.success("Troli berhasil dikosongkan/hapus");
                router.get(route("master.troli.index"));
            },
        },
    );
};

// Watcher Search
let timeout: any;
watch(search, (value) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(
            route("master.troli.produk", props.troli.id),
            { search: value },
            { preserveState: true },
        );
    }, 500);
});
</script>

<template>
    <Head :title="'Produk Troli - ' + troli.nomor" />

    <div class="flex flex-col gap-4 p-4 md:p-8 pt-4">
        <div class="flex items-center">
            <Button variant="ghost" size="sm" as-child>
                <Link :href="route('master.troli.index')">
                    <IconArrowLeft class="size-4 mr-2" /> Kembali
                </Link>
            </Button>
        </div>

        <Card class="border-none shadow-sm">
            <CardHeader
                class="flex flex-col md:flex-row items-start md:items-center justify-between space-y-4 md:space-y-0 pb-6 border-b"
            >
                <CardTitle class="flex flex-col gap-1">
                    <div class="flex items-center gap-2 text-xl font-bold">
                        <IconPackage class="size-6 text-primary" />
                        Isi Troli: {{ troli.nomor }}
                    </div>
                    <div class="flex items-center gap-2">
                        <Badge variant="outline"
                            >Proses: {{ troli.proses?.proses }}</Badge
                        >
                        <span class="text-sm text-muted-foreground"
                            >Total: {{ produks.total }} Produk</span
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
                            placeholder="Cari produk..."
                            class="pl-10"
                        />
                    </div>

                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <Button variant="outline" size="icon">
                                <IconSettings class="size-4" />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end" class="w-56">
                            <DropdownMenuLabel
                                >Manajemen Troli</DropdownMenuLabel
                            >
                            <DropdownMenuSeparator />
                            <DropdownMenuItem
                                @click="showMoveDialog = true"
                                :disabled="selectedIds.length === 0"
                            >
                                <IconTransfer
                                    class="mr-2 size-4 text-blue-500"
                                />
                                Pindahkan ke Troli Lain
                            </DropdownMenuItem>
                            <DropdownMenuItem
                                @click="updateScanStatus('Sudah')"
                                :disabled="selectedIds.length === 0"
                            >
                                <IconCheck class="mr-2 size-4 text-green-500" />
                                Setel "Sudah Scan"
                            </DropdownMenuItem>
                            <DropdownMenuItem
                                @click="updateScanStatus('Belum')"
                                :disabled="selectedIds.length === 0"
                            >
                                <IconX class="mr-2 size-4 text-yellow-500" />
                                Setel "Belum Scan"
                            </DropdownMenuItem>
                            <DropdownMenuItem
                                @click="removeProducts"
                                :disabled="selectedIds.length === 0"
                                class="text-red-600"
                            >
                                <IconTrash class="mr-2 size-4" /> Hapus Produk
                                dari Troli
                            </DropdownMenuItem>
                            <DropdownMenuSeparator />
                            <DropdownMenuItem
                                @click="showDeleteTroliDialog = true"
                                class="text-red-600 font-bold"
                            >
                                <IconTrash class="mr-2 size-4" />
                                Hapus/Kosongkan Troli
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            </CardHeader>

            <CardContent class="pt-6">
                <div class="rounded-lg border overflow-hidden">
                    <Table>
                        <TableHeader>
                            <TableRow class="bg-muted/50">
                                <TableHead class="w-12">
                                    <Checkbox
                                        :checked="
                                            selectedIds.length ===
                                                produks.data.length &&
                                            produks.data.length > 0
                                        "
                                        @update:checked="toggleAll"
                                    />
                                </TableHead>
                                <TableHead>QR Code</TableHead>
                                <TableHead>Jenis</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead>Scan</TableHead>
                                <TableHead>Tgl Masuk</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow
                                v-for="item in produks.data"
                                :key="item.id"
                            >
                                <TableCell>
                                    <Checkbox
                                        :checked="selectedIds.includes(item.id)"
                                        @update:checked="
                                            () => toggleSelect(item.id)
                                        "
                                    />
                                </TableCell>
                                <TableCell class="font-mono font-bold">{{
                                    item.qrcode
                                }}</TableCell>
                                <TableCell>{{ item.jenis }}</TableCell>
                                <TableCell>
                                    <Badge
                                        :variant="
                                            item.status_akhir === 'OK'
                                                ? 'default'
                                                : 'destructive'
                                        "
                                        >{{ item.status_akhir }}</Badge
                                    >
                                </TableCell>
                                <TableCell>
                                    <Badge
                                        :class="
                                            item.sudah_scan === 'Sudah'
                                                ? 'bg-green-500'
                                                : 'bg-yellow-500'
                                        "
                                        >{{ item.sudah_scan }}</Badge
                                    >
                                </TableCell>
                                <TableCell class="text-xs">{{
                                    new Date(
                                        item.created_at,
                                    ).toLocaleDateString("id-ID")
                                }}</TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </CardContent>
        </Card>
    </div>

    <AlertDialog :open="showMoveDialog" @update:open="showMoveDialog = $event">
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle
                    >Pindahkan {{ selectedIds.length }} Produk</AlertDialogTitle
                >
                <AlertDialogDescription
                    >Pilih nomor troli tujuan untuk memindahkan produk yang
                    dipilih.</AlertDialogDescription
                >
            </AlertDialogHeader>
            <div class="py-4">
                <Select v-model="targetTroliId">
                    <SelectTrigger>
                        <SelectValue placeholder="Pilih Troli Tujuan" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="t in availableTrolis"
                            :key="t.id"
                            :value="t.id.toString()"
                        >
                            {{ t.nomor }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>
            <AlertDialogFooter>
                <AlertDialogCancel @click="showMoveDialog = false"
                    >Batal</AlertDialogCancel
                >
                <AlertDialogAction
                    @click="moveProducts"
                    class="bg-blue-600 hover:bg-blue-700"
                    >Pindahkan</AlertDialogAction
                >
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>

    <AlertDialog
        :open="showDeleteTroliDialog"
        @update:open="showDeleteTroliDialog = $event"
    >
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>Konfirmasi Hapus Troli</AlertDialogTitle>
                <AlertDialogDescription>
                    Menghapus troli akan melepaskan ikatan proses (set null).
                    Troli akan kembali tersedia untuk digunakan dari awal.
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel @click="showDeleteTroliDialog = false"
                    >Batal</AlertDialogCancel
                >
                <AlertDialogAction
                    @click="deleteTroli"
                    class="bg-red-600 hover:bg-red-700"
                    >Ya, Hapus</AlertDialogAction
                >
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
