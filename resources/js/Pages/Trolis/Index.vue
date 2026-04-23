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
} from "@tabler/icons-vue";
import { ref, watch } from "vue";
import { toast } from "vue-sonner";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    trolis: {
        data: Array<{
            id: number;
            invoice: string;
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
const copiedInvoice = ref<number | null>(null);

const copyToClipboard = async (text: string, id: number) => {
    try {
        await navigator.clipboard.writeText(text);
        copiedInvoice.value = id;

        toast.success("Invoice disalin!");

        setTimeout(() => {
            copiedInvoice.value = null;
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
</script>

<template>
    <Head title="Manajemen Troli" />

    <div class="flex flex-col gap-4 p-4 md:p-8 pt-4">
        <Card class="border-none shadow-sm">
            <CardHeader
                class="flex flex-col md:flex-row items-start md:items-center justify-between space-y-4 md:space-y-0 pb-6"
            >
                <CardTitle class="text-xl font-bold flex items-center gap-2">
                    <IconShoppingCart class="size-6 text-primary" />
                    Manajemen Troli
                </CardTitle>

                <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                    <div class="relative w-full md:w-64">
                        <IconSearch
                            class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground"
                        />
                        <Input
                            v-model="search"
                            placeholder="Cari invoice..."
                            class="pl-10 pr-10"
                        />
                        <button
                            v-if="search"
                            @click="clearSearch"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground"
                        >
                            <IconX class="size-4" />
                        </button>
                    </div>

                    <Button
                        variant="outline"
                        class="border-primary text-primary hover:bg-primary/10"
                        as-child
                    >
                        <Link :href="route('trolifisiks.index')">
                            <IconShoppingCart class="mr-2 size-4" />
                            Ambil Fisik
                        </Link>
                    </Button>

                    <Button class="bg-primary hover:bg-primary/90" as-child>
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
                            <TableRow class="bg-muted/50">
                                <TableHead>Invoice</TableHead>
                                <TableHead>Keperluan</TableHead>
                                <!-- <TableHead>Jenis</TableHead> -->
                                <!-- <TableHead>Tipe</TableHead> -->
                                <TableHead>Proses</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead>Total</TableHead>
                                <TableHead>Tanggal</TableHead>
                                <TableHead>Kapan</TableHead>
                                <TableHead class="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="trolis.data.length === 0">
                                <TableCell
                                    colspan="8"
                                    class="h-24 text-center text-muted-foreground"
                                >
                                    Data tidak ditemukan.
                                </TableCell>
                            </TableRow>

                            <TableRow
                                v-for="troli in trolis.data"
                                :key="troli.id"
                                class="hover:bg-muted/30 transition-colors"
                            >
                                <TableCell>
                                    <div class="flex items-center gap-3">
                                        <button
                                            @click="
                                                copyToClipboard(
                                                    troli.invoice,
                                                    troli.id,
                                                )
                                            "
                                            class="inline-flex items-center justify-center size-9 rounded-lg border-2 border-primary/20 bg-primary/5 active:bg-primary active:text-white transition-all shrink-0"
                                            :class="{
                                                'bg-green-500 border-green-600 text-white':
                                                    copiedInvoice === troli.id,
                                            }"
                                        >
                                            <IconCheck
                                                v-if="
                                                    copiedInvoice === troli.id
                                                "
                                                class="size-5"
                                            />
                                            <IconCopy v-else class="size-5" />
                                        </button>

                                        <span
                                            class="font-bold text-primary text-sm md:text-base leading-none"
                                        >
                                            {{ troli.invoice }}
                                        </span>
                                    </div>
                                </TableCell>
                                <TableCell>{{ troli.keperluan }}</TableCell>
                                <!-- <TableCell>{{ troli.jenis }}</TableCell> -->
                                <!-- <TableCell> -->
                                <!--     <Badge :variant="troli.is_output ? 'default' : 'outline'"> -->
                                <!--         {{ troli.is_output ? 'Output (Wadah)' : 'Sumber' }} -->
                                <!--     </Badge> -->
                                <!-- </TableCell> -->
                                <TableCell>
                                    <Badge class="bg-lime-500 text-black">{{
                                        troli.proses.proses ?? "-"
                                    }}</Badge>
                                </TableCell>
                                <TableCell>
                                    <Badge class="bg-lime-500 text-black">{{
                                        troli.status
                                    }}</Badge>
                                </TableCell>
                                <TableCell>
                                    <Badge class="bg-lime-500 text-black">{{
                                        troli.produks_count
                                    }}</Badge>
                                </TableCell>
                                <TableCell>{{ troli.tanggal_jam }} </TableCell>
                                <TableCell>{{ troli.create_time }}</TableCell>
                                <TableCell class="text-right">
                                    <Button
                                        variant="ghost"
                                        class="size-10"
                                        as-child
                                    >
                                        <Link
                                            :href="
                                                route(
                                                    'trolis.produk.index',
                                                    troli.id,
                                                )
                                            "
                                        >
                                            <IconEye
                                                class="size-5 text-primary"
                                            />
                                        </Link>
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
                    <div
                        class="text-sm text-muted-foreground order-2 md:order-1"
                    >
                        Menampilkan
                        <span class="font-medium text-foreground">{{
                            trolis.from
                        }}</span>
                        sampai
                        <span class="font-medium text-foreground">{{
                            trolis.to
                        }}</span>
                        dari
                        <span class="font-medium text-foreground">{{
                            trolis.total
                        }}</span>
                        data
                    </div>

                    <div
                        class="flex flex-wrap items-center justify-center gap-1 order-1 md:order-2"
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
                            />
                        </template>
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
