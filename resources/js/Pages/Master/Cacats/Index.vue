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
import {
    IconPlus,
    IconPencil,
    IconSearch,
    IconX,
    IconAlertTriangle,
} from "@tabler/icons-vue";
import { ref, watch } from "vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    cacats: {
        data: Array<{ id: number; nama_cacat: string; created_at: string }>;
        links: any[];
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
            route("cacats.index"),
            { search: value },
            { preserveState: true, replace: true },
        );
    }, 500);
});

const cleanLabel = (label: string) => {
    if (label.includes("Previous")) return "Sebelumnya";
    if (label.includes("Next")) return "Selanjutnya";
    return label;
};
</script>

<template>
    <Head title="Master Cacat" />
    <div class="flex flex-col gap-4 p-4 md:p-8 pt-4">
        <Card class="border-none shadow-sm">
            <CardHeader
                class="flex flex-col md:flex-row items-start md:items-center justify-between space-y-4 md:space-y-0 pb-6"
            >
                <CardTitle class="text-xl font-bold flex items-center gap-2">
                    <IconAlertTriangle class="size-6 text-primary" />
                    Daftar Jenis Cacat
                </CardTitle>
                <div class="flex items-center gap-2 w-full md:w-auto">
                    <div class="relative w-full md:w-72">
                        <IconSearch
                            class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground"
                        />
                        <Input
                            v-model="search"
                            placeholder="Cari jenis cacat..."
                            class="pl-10 pr-10"
                        />
                        <button
                            v-if="search"
                            @click="search = ''"
                            class="absolute right-3 top-1/2 -translate-y-1/2"
                        >
                            <IconX class="size-4" />
                        </button>
                    </div>
                    <Button as-child class="bg-primary hover:bg-primary/90">
                        <Link :href="route('cacats.create')"
                            ><IconPlus class="mr-2 size-4" />Tambah</Link
                        >
                    </Button>
                </div>
            </CardHeader>
            <CardContent>
                <div class="rounded-lg border">
                    <Table>
                        <TableHeader
                            ><TableRow class="bg-muted/50"
                                ><TableHead>Nama Cacat</TableHead
                                ><TableHead class="text-right"
                                    >Aksi</TableHead
                                ></TableRow
                            ></TableHeader
                        >
                        <TableBody>
                            <TableRow
                                v-for="item in cacats.data"
                                :key="item.id"
                            >
                                <TableCell class="font-medium uppercase">{{
                                    item.nama_cacat
                                }}</TableCell>
                                <TableCell class="text-right">
                                    <Button variant="ghost" size="icon" as-child
                                        ><Link
                                            :href="
                                                route('cacats.edit', item.id)
                                            "
                                            ><IconPencil class="size-4" /></Link
                                    ></Button>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
                <div class="flex items-center justify-between mt-6">
                    <p class="text-xs text-muted-foreground">
                        Total: {{ cacats.total }} data
                    </p>
                    <nav class="flex gap-1">
                        <template v-for="link in cacats.links">
                            <Button
                                v-if="link.url"
                                as-child
                                variant="outline"
                                size="sm"
                                :class="{
                                    'bg-primary text-white': link.active,
                                }"
                            >
                                <Link
                                    :href="link.url"
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
