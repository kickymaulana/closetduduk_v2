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
import { Input } from "@/components/ui/input";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import {
    IconSearch,
    IconUsers,
    IconUser,
    IconX,
    IconTrophy,
} from "@tabler/icons-vue";
import { ref, watch } from "vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    rekap: {
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

watch(search, (value) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(
            route("total.pengerjaan.user"),
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
    <Head title="Rekap Produk Per User" />

    <div class="flex flex-col gap-4 p-4 md:p-8 pt-4">
        <Card class="border-none shadow-sm">
            <CardHeader
                class="flex flex-col md:flex-row items-start md:items-center justify-between space-y-4 md:space-y-0 pb-6"
            >
                <CardTitle class="text-xl font-bold flex items-center gap-2">
                    <IconUsers class="size-6 text-primary" />
                    Pencapaian Kerja Per Personel
                </CardTitle>

                <div class="flex items-center gap-2 w-full md:w-auto">
                    <div class="relative w-full md:w-80">
                        <IconSearch
                            class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground"
                        />
                        <Input
                            v-model="search"
                            placeholder="Cari nama personel..."
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
                </div>
            </CardHeader>

            <CardContent>
                <div class="rounded-lg border overflow-hidden">
                    <Table>
                        <TableHeader>
                            <TableRow class="bg-muted/50">
                                <TableHead class="w-[50px] text-center">Rank</TableHead>
                                <TableHead>Nama Personel</TableHead>
                                <TableHead class="text-center">Total Output</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow
                                v-for="(item, index) in rekap.data"
                                :key="item.user.id"
                                class="hover:bg-muted/30 transition-colors"
                            >
                                <TableCell class="text-center font-bold text-muted-foreground">
                                    {{ rekap.from + index }}
                                </TableCell>
                                <TableCell>
                                    <div class="flex items-center gap-3">
                                        <div class="size-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs">
                                            {{ item.user.name.substring(0, 2).toUpperCase() }}
                                        </div>
                                        <span class="font-medium text-slate-700">{{ item.user.name }}</span>
                                    </div>
                                </TableCell>
                                <TableCell class="text-center">
                                    <Badge class="bg-blue-50 text-blue-700 border-blue-200 hover:bg-blue-100 px-3">
                                        <IconTrophy class="size-3 mr-1" />
                                        {{ item.total_pengerjaan }} Produk
                                    </Badge>
                                </TableCell>
                            </TableRow>

                            <TableRow v-if="rekap.data.length === 0">
                                <TableCell colspan="4" class="text-center py-12 text-muted-foreground">
                                    Data personel tidak ditemukan.
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>

                <div class="flex flex-col md:flex-row items-center justify-between gap-4 mt-6">
                    <p class="text-xs text-muted-foreground">
                        Menampilkan {{ rekap.total }} personel terdaftar
                    </p>
                    <nav class="flex items-center gap-1">
                        <template v-for="(link, k) in rekap.links" :key="k">
                            <Button
                                v-if="link.url === null"
                                variant="outline" size="sm" disabled
                                class="opacity-50 text-xs px-3 h-8"
                                v-html="cleanLabel(link.label)"
                            />
                            <Button
                                v-else
                                as-child variant="outline" size="sm"
                                class="text-xs px-3 h-8"
                                :class="{ 'bg-primary text-white': link.active }"
                            >
                                <Link :href="link.url" v-html="cleanLabel(link.label)" />
                            </Button>
                        </template>
                    </nav>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
