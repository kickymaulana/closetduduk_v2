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
            nomor: string;
            keperluan: string;
            status: string;
            proses?: {
                proses?: string;
            };
            produks_count: number;
            terakhir_diperbaharui_jam: string;
            terakhir_diperbaharui: string;
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
            route("master.troli.index"), // Memastikan mengarah ke route yang tepat
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
    <Head title="Master Troli" />

    <div class="flex flex-col gap-4 p-4 md:p-8 pt-4">
        <Card class="border-none shadow-sm">
            <CardHeader
                class="flex flex-col md:flex-row items-start md:items-center justify-between space-y-4 md:space-y-0 pb-6"
            >
                <CardTitle class="text-xl font-bold flex items-center gap-2">
                    <IconShoppingCart class="size-6 text-primary" />
                    Master Data Troli
                </CardTitle>

                <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                    <div class="relative w-full md:w-80">
                        <IconSearch
                            class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground"
                        />
                        <Input
                            v-model="search"
                            placeholder="Cari nomor troli atau scan QR..."
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
                                <TableHead class="w-[200px]">Nomor</TableHead>
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
                                    class="h-32 text-center text-muted-foreground"
                                >
                                    Data troli tidak ditemukan.
                                </TableCell>
                            </TableRow>

                            <TableRow
                                v-for="troli in trolis.data"
                                :key="troli.id"
                                class="hover:bg-muted/30 transition-colors"
                            >
                                <TableCell>
                                    <div class="flex items-center gap-2">
                                        <button
                                            @click="
                                                copyToClipboard(
                                                    troli.nomor,
                                                    troli.id,
                                                )
                                            "
                                            class="inline-flex items-center justify-center size-8 rounded border bg-background hover:bg-muted transition-all shrink-0"
                                            :class="{
                                                'text-green-600 border-green-600':
                                                    copiedNomor === troli.id,
                                            }"
                                        >
                                            <IconCheck
                                                v-if="copiedNomor === troli.id"
                                                class="size-4"
                                            />
                                            <IconCopy v-else class="size-4" />
                                        </button>
                                        <span class="font-bold text-primary">
                                            {{ troli.nomor }}
                                        </span>
                                    </div>
                                </TableCell>
                                <TableCell>{{ troli.keperluan }}</TableCell>
                                <TableCell>
                                    <Badge
                                        variant="outline"
                                        class="bg-blue-50 text-blue-700 border-blue-200"
                                    >
                                        {{ troli.proses?.proses ?? "-" }}
                                    </Badge>
                                </TableCell>
                                <TableCell>
                                    <Badge
                                        class="bg-lime-500 text-black hover:bg-lime-600"
                                    >
                                        {{ troli.status }}
                                    </Badge>
                                </TableCell>
                                <TableCell>
                                    <span
                                        class="font-medium px-2 py-1 bg-muted rounded-md text-sm"
                                    >
                                        {{ troli.produks_count }} Item
                                    </span>
                                </TableCell>
                                <TableCell
                                    class="text-xs text-muted-foreground"
                                >
                                    <div class="flex flex-col">
                                        <span
                                            class="font-medium text-foreground"
                                            >{{
                                                troli.terakhir_diperbaharui_jam
                                            }}</span
                                        >
                                        <span>{{
                                            troli.terakhir_diperbaharui
                                        }}</span>
                                    </div>
                                </TableCell>
                                <TableCell class="text-right">
                                    <Button
                                        variant="ghost"
                                        size="icon"
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
                    <div class="text-sm text-muted-foreground">
                        Menampilkan {{ trolis.from }} - {{ trolis.to }} dari
                        {{ trolis.total }} troli
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
                            />
                        </template>
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
