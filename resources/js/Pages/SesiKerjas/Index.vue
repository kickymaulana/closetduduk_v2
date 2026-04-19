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
import { IconPlus, IconPencil, IconSearch, IconClock } from "@tabler/icons-vue";
import { ref, watch } from "vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    sesikerjas: any;
    filters: { search: string };
}>();

const search = ref(props.filters.search || "");
let timeout: any;

watch(search, (value) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(
            route("sesikerjas.index"),
            { search: value },
            { preserveState: true, replace: true },
        );
    }, 500);
});
</script>

<template>
    <Head title="Sesi Kerja" />
    <div class="flex flex-col gap-4 p-4 md:p-8 pt-4">
        <Card class="border-none shadow-sm">
            <CardHeader
                class="flex flex-col md:flex-row items-start md:items-center justify-between space-y-4 md:space-y-0 pb-6"
            >
                <CardTitle class="text-xl font-bold flex items-center gap-2">
                    <IconClock class="size-6 text-primary" />
                    Daftar Sesi Kerja
                </CardTitle>
                <div class="flex items-center gap-2 w-full md:w-auto">
                    <div class="relative w-full md:w-72">
                        <IconSearch
                            class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground"
                        />
                        <Input
                            v-model="search"
                            placeholder="Cari leader atau jenis..."
                            class="pl-10"
                        />
                    </div>
                    <Button as-child class="bg-primary hover:bg-primary/90">
                        <Link :href="route('sesikerjas.create')">
                            <IconPlus class="mr-2 size-4" />Tambah
                        </Link>
                    </Button>
                </div>
            </CardHeader>
            <CardContent>
                <div class="rounded-lg border overflow-hidden">
                    <Table>
                        <TableHeader>
                            <TableRow class="bg-muted/50">
                                <TableHead>Leader</TableHead>
                                <TableHead>Jenis</TableHead>
                                <TableHead>Jam Masuk</TableHead>
                                <TableHead>Jam Pulang</TableHead>
                                <TableHead class="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow
                                v-for="item in sesikerjas.data"
                                :key="item.id"
                            >
                                <TableCell class="font-medium">
                                    {{ item.leader?.name }}
                                </TableCell>
                                <TableCell>
                                    <Badge
                                        :variant="
                                            item.jenis === 'Body'
                                                ? 'default'
                                                : 'secondary'
                                        "
                                    >
                                        {{ item.jenis }}
                                    </Badge>
                                </TableCell>
                                <TableCell>{{
                                    item.jam_masuk || "-"
                                }}</TableCell>
                                <TableCell>{{
                                    item.jam_pulang || "-"
                                }}</TableCell>
                                <TableCell class="text-right">
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        as-child
                                    >
                                        <Link>
                                            <IconPencil class="size-4" />
                                        </Link>
                                    </Button>
                                </TableCell>
                            </TableRow>
                            <TableRow v-if="sesikerjas.data.length === 0">
                                <TableCell
                                    colspan="5"
                                    class="text-center py-10 text-muted-foreground"
                                >
                                    Data tidak ditemukan.
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
