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
    IconPlus,
    IconPencil,
    IconSearch,
    IconX,
    IconGavel,
} from "@tabler/icons-vue";
import { ref, watch } from "vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    aturanPenolakans: any;
    filters: { search: string };
}>();

const search = ref(props.filters.search || "");
let timeout: any;
watch(search, (value) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(
            route("aturanpenolakans.index"),
            { search: value },
            { preserveState: true, replace: true },
        );
    }, 500);
});
</script>

<template>
    <Head title="Aturan Penolakan" />
    <div class="flex flex-col gap-4 p-4 md:p-8 pt-4">
        <Card class="border-none shadow-sm">
            <CardHeader
                class="flex flex-col md:flex-row items-start md:items-center justify-between space-y-4 md:space-y-0 pb-6"
            >
                <CardTitle class="text-xl font-bold flex items-center gap-2">
                    <IconGavel class="size-6 text-primary" />
                    Aturan Penolakan Cacat
                </CardTitle>
                <div class="flex items-center gap-2 w-full md:w-auto">
                    <div class="relative w-full md:w-72">
                        <IconSearch
                            class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground"
                        />
                        <Input
                            v-model="search"
                            placeholder="Cari nama cacat..."
                            class="pl-10"
                        />
                    </div>
                    <Button as-child class="bg-primary hover:bg-primary/90">
                        <Link :href="route('aturanpenolakans.create')"
                            ><IconPlus class="mr-2 size-4" />Tambah</Link
                        >
                    </Button>
                </div>
            </CardHeader>
            <CardContent>
                <div class="rounded-lg border overflow-hidden">
                    <Table>
                        <TableHeader
                            ><TableRow class="bg-muted/50">
                                <TableHead>Jenis Cacat</TableHead>
                                <TableHead>Dep. Toleransi</TableHead>
                                <TableHead>Dep. Buang</TableHead>
                                <TableHead>Dep. Pemeriksa</TableHead>
                                <TableHead class="text-right">Aksi</TableHead>
                            </TableRow></TableHeader
                        >
                        <TableBody>
                            <TableRow
                                v-for="item in aturanPenolakans.data"
                                :key="item.id"
                            >
                                <TableCell class="font-bold text-primary">{{
                                    item.cacat.cacat
                                }}</TableCell>
                                <TableCell
                                    ><Badge variant="outline">{{
                                        item.proses_toleransi.proses
                                    }}</Badge></TableCell
                                >
                                <TableCell
                                    ><Badge variant="secondary">{{
                                        item.proses_buang.proses
                                    }}</Badge></TableCell
                                >
                                <TableCell
                                    ><Badge
                                        class="bg-lime-500 text-black hover:bg-lime-600"
                                        >{{
                                            item.proses_pemeriksa.proses
                                        }}</Badge
                                    ></TableCell
                                >
                                <TableCell class="text-right">
                                    <Button variant="ghost" size="icon" as-child
                                        ><Link
                                            :href="
                                                route(
                                                    'aturanpenolakans.edit',
                                                    item.id,
                                                )
                                            "
                                            ><IconPencil class="size-4" /></Link
                                    ></Button>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
