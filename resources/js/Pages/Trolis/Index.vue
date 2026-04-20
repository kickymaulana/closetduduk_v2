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
    IconPlus,
    IconBuildingBridge // Ikon untuk Dept
} from "@tabler/icons-vue";
import { ref, watch } from "vue";

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
                nama_proses: string; // Sesuaikan dengan nama kolom di tabel 'proses'
            };
            created_at: string;
        }>;
        links: any[];
        from: number;
        to: number;
        total: number;
    };
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
            route("trolis.index"),
            { search: value },
            { preserveState: true, replace: true },
        );
    }, 500);
});

const clearSearch = () => { search.value = ""; };

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
            <CardHeader class="flex flex-col md:flex-row items-start md:items-center justify-between space-y-4 md:space-y-0 pb-6">
                <CardTitle class="text-xl font-bold flex items-center gap-2">
                    <IconShoppingCart class="size-6 text-primary" />
                    Manajemen Troli
                </CardTitle>

                <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                    <div class="relative w-full md:w-64">
                        <IconSearch class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
                        <Input v-model="search" placeholder="Cari invoice..." class="pl-10 pr-10" />
                        <button v-if="search" @click="clearSearch" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground">
                            <IconX class="size-4" />
                        </button>
                    </div>

                    <Button variant="outline" class="border-primary text-primary hover:bg-primary/10">
                        <Link :href="route('trolifisiks.index')" class="flex items-center justify-center">
                            <IconShoppingCart class="mr-2 size-4" />
                            Ambil Fisik
                        </Link>
                    </Button>


                    <Button class="bg-primary hover:bg-primary/90">
                        <IconBuildingBridge class="mr-2 size-4" />
                        Ambil Departemen
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
                                <TableHead>Jenis</TableHead>
                                <TableHead>Tipe</TableHead>
                                <TableHead>Proses</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead class="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="trolis.data.length === 0">
                                <TableCell colspan="7" class="h-24 text-center text-muted-foreground">Data tidak ditemukan.</TableCell>
                            </TableRow>

                            <TableRow v-for="troli in trolis.data" :key="troli.id" class="hover:bg-muted/30 transition-colors">
                                <TableCell class="font-bold text-primary">{{ troli.invoice }}</TableCell>
                                <TableCell>{{ troli.keperluan }}</TableCell>
                                <TableCell>{{ troli.jenis }}</TableCell>
                                <TableCell>
                                    <Badge :variant="troli.is_output ? 'default' : 'outline'">
                                        {{ troli.is_output ? 'Output (Wadah)' : 'Sumber' }}
                                    </Badge>
                                </TableCell>
                                <TableCell>{{ troli.proses?.nama_proses ?? '-' }}</TableCell>
                                <TableCell>
                                    <Badge class="bg-lime-500 text-black">{{ troli.status }}</Badge>
                                </TableCell>
                                <TableCell class="text-right">
                                    <Button variant="ghost" size="icon" as-child>
                                        <Link :href="route('trolis.index', troli.id)"> <IconEye class="size-4 text-primary" />
                                        </Link>
                                    </Button>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
                </CardContent>
        </Card>
    </div>
</template>
