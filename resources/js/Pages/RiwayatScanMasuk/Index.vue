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
    IconSearch,
    IconHistory,
    IconBox,
    IconUser,
    IconChevronRight,
    IconX,
    IconCalendarTime,
    IconEye,
} from "@tabler/icons-vue";
import { ref, watch } from "vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    riwayat: {
        data: Array<any>;
        links: Array<any>;
        from: number;
        to: number;
        total: number;
    };
    filters: { search: string };
}>();

const search = ref(props.filters.search || "");
let timeout: any;

// Watcher untuk pencarian dengan debounce 500ms
watch(search, (value) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(
            route("riwayat.scan.masuk"),
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

// Helper warna badge status
const getStatusVariant = (status: string) => {
    switch (status) {
        case "OK":
            return "bg-green-500 hover:bg-green-600";
        case "In Proses":
            return "bg-blue-500 hover:bg-blue-600";
        case "Buang":
            return "bg-red-500 hover:bg-red-600";
        default:
            return "bg-slate-500";
    }
};
</script>

<template>
    <Head title="Riwayat Scan Masuk" />

    <div class="flex flex-col gap-4 p-4 md:p-8 pt-4">
        <Card class="border-none shadow-sm">
            <CardHeader
                class="flex flex-col md:flex-row items-start md:items-center justify-between space-y-4 md:space-y-0 pb-6"
            >
                <CardTitle class="text-xl font-bold flex items-center gap-2">
                    <IconHistory class="size-6 text-primary" />
                    Riwayat Scan Masuk
                </CardTitle>

                <div class="flex items-center gap-2 w-full md:w-auto">
                    <div class="relative w-full md:w-80">
                        <IconSearch
                            class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground"
                        />
                        <Input
                            v-model="search"
                            placeholder="Cari Invoice atau Operator..."
                            class="pl-10 pr-10"
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
                                <TableHead class="w-[200px]"
                                    >Waktu Scan</TableHead
                                >
                                <TableHead>Produk (Invoice)</TableHead>
                                <TableHead>Proses</TableHead>
                                <TableHead>Operator</TableHead>
                                <TableHead>Kondisi</TableHead>
                                <TableHead class="text-right">Detail</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow
                                v-for="item in riwayat.data"
                                :key="item.id"
                                class="hover:bg-muted/30 transition-colors"
                            >
                                <TableCell>
                                    <div class="flex flex-col gap-0.5">
                                        <div
                                            class="flex items-center gap-1.5 text-sm font-medium"
                                        >
                                            <IconCalendarTime
                                                class="size-3.5 text-slate-400"
                                            />
                                            {{ item.waktu_scan }}
                                        </div>
                                        <span
                                            class="text-[10px] text-muted-foreground italic ml-5"
                                        >
                                            {{ item.waktu_relatif }}
                                        </span>
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <div class="flex items-center gap-2">
                                        <IconBox
                                            class="size-4 text-slate-400"
                                        />
                                        <span
                                            class="font-mono font-bold text-primary tracking-wider"
                                        >
                                            {{ item.produk?.qrcode || "N/A" }}
                                        </span>
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <Badge
                                        variant="outline"
                                        class="font-medium px-2 py-0.5 border-primary/20 text-primary bg-primary/5"
                                    >
                                        {{ item.proses?.proses || "-" }}
                                    </Badge>
                                </TableCell>
                                <TableCell>
                                    <div
                                        class="flex items-center gap-2 text-sm"
                                    >
                                        <div
                                            class="size-6 rounded-full bg-slate-200 flex items-center justify-center text-[10px] font-bold"
                                        >
                                            {{
                                                item.user?.name
                                                    ?.substring(0, 2)
                                                    .toUpperCase()
                                            }}
                                        </div>
                                        {{ item.user?.name }}
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <Badge
                                        :class="
                                            getStatusVariant(
                                                item.status_kondisi,
                                            )
                                        "
                                        class="text-white border-none shadow-sm"
                                    >
                                        {{ item.status_kondisi }}
                                    </Badge>
                                </TableCell>
                                <TableCell class="text-right">
                                    <Button variant="ghost" class="size-10" as-child>
                                        <Link :href="route('produk.show', item.id)">
                                            <IconEye class="size-5 text-primary" />
                                        </Link>
                                    </Button>
                                </TableCell>
                            </TableRow>

                            <TableRow v-if="riwayat.data.length === 0">
                                <TableCell
                                    colspan="6"
                                    class="text-center py-12 text-muted-foreground italic"
                                >
                                    Belum ada riwayat scan hari ini.
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>

                <div
                    class="flex flex-col md:flex-row items-center justify-between gap-4 mt-6"
                >
                    <p class="text-xs text-muted-foreground italic font-medium">
                        Menampilkan {{ riwayat.from ?? 0 }} -
                        {{ riwayat.to ?? 0 }} dari {{ riwayat.total }} data scan
                    </p>

                    <nav class="flex items-center gap-1">
                        <template v-for="(link, k) in riwayat.links" :key="k">
                            <Button
                                v-if="link.url === null"
                                variant="outline"
                                size="sm"
                                disabled
                                class="opacity-50 text-xs px-3 h-8"
                                v-html="cleanLabel(link.label)"
                            />
                            <Button
                                v-else
                                as-child
                                variant="outline"
                                size="sm"
                                class="text-xs px-3 h-8 transition-all"
                                :class="{
                                    'bg-primary text-primary-foreground hover:bg-primary/90 shadow-sm':
                                        link.active,
                                }"
                            >
                                <Link
                                    :href="link.url"
                                    preserve-state
                                    v-html="cleanLabel(link.label)"
                                />
                            </Button>
                        </template>
                    </nav>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
