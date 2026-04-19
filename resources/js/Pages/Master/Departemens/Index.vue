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
import { Badge } from "@/components/ui/badge"; // Tambahkan Badge
import {
    IconPlus,
    IconPencil,
    IconSearch,
    IconX,
    IconHierarchy2,
} from "@tabler/icons-vue";
import { ref, watch } from "vue";

// 1. Definisikan Persistent Layout
defineOptions({ layout: AuthenticatedLayout });

// 2. Definisi Props
const props = defineProps<{
    departemens: {
        data: Array<{
            id: number;
            urutan: number;
            departemen: string;
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

// 3. Logika Pencarian
const search = ref(props.filters.search || "");

let timeout: ReturnType<typeof setTimeout>;
watch(search, (value) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(
            route("departemens.index"),
            { search: value },
            { preserveState: true, replace: true },
        );
    }, 500);
});

const clearSearch = () => {
    search.value = "";
};

// 4. Helper Formatter
const cleanLabel = (label: string) => {
    if (label.includes("Previous")) return "Sebelumnya";
    if (label.includes("Next")) return "Selanjutnya";
    return label;
};
</script>

<template>
    <Head title="Departemen" />

    <div class="flex flex-col gap-4 p-4 md:p-8 pt-4">
        <Card class="border-none shadow-sm">
            <CardHeader
                class="flex flex-col md:flex-row items-start md:items-center justify-between space-y-4 md:space-y-0 pb-6"
            >
                <CardTitle class="text-xl font-bold flex items-center gap-2">
                    <IconHierarchy2 class="size-6 text-primary" />
                    Departemen
                </CardTitle>

                <div class="flex items-center gap-2 w-full md:w-auto">
                    <div class="relative w-full md:w-72">
                        <IconSearch
                            class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground"
                        />
                        <Input
                            v-model="search"
                            placeholder="Cari departemen..."
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

                    <Button
                        as-child
                        class="bg-primary hover:bg-primary/90 shadow-md transition-all active:scale-95"
                    >
                        <Link :href="route('departemens.create')">
                            <IconPlus class="mr-2 size-4" />
                            <span class="hidden sm:inline"
                                >Tambah Departemen</span
                            >
                        </Link>
                    </Button>
                </div>
            </CardHeader>

            <CardContent>
                <div class="rounded-lg border overflow-hidden">
                    <Table>
                        <TableHeader>
                            <TableRow class="bg-muted/50">
                                <TableHead>Nama Departemen</TableHead>
                                <TableHead class="hidden md:table-cell"
                                    >Dibuat Pada</TableHead
                                >
                                <TableHead class="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="departemens.data.length === 0">
                                <TableCell
                                    colspan="4"
                                    class="h-24 text-center text-muted-foreground italic"
                                >
                                    Data departemen tidak ditemukan.
                                </TableCell>
                            </TableRow>

                            <TableRow
                                v-for="item in departemens.data"
                                :key="item.id"
                                class="hover:bg-muted/30 transition-colors"
                            >
                                <TableCell
                                    class="font-bold text-primary uppercase tracking-wide"
                                >
                                    {{ item.departemen }}
                                </TableCell>
                                <TableCell
                                    class="hidden md:table-cell text-muted-foreground text-sm"
                                >
                                    {{
                                        new Date(
                                            item.created_at,
                                        ).toLocaleDateString("id-ID", {
                                            day: "2-digit",
                                            month: "short",
                                            year: "numeric",
                                        })
                                    }}
                                </TableCell>

                                <TableCell class="text-right">
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        class="size-8 hover:text-primary transition-colors"
                                        as-child
                                    >
                                        <Link
                                            :href="
                                                route(
                                                    'departemens.edit',
                                                    item.id,
                                                )
                                            "
                                        >
                                            <IconPencil class="size-4" />
                                        </Link>
                                    </Button>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>

                <div
                    class="flex flex-col md:flex-row items-center justify-between gap-4 mt-6"
                >
                    <p class="text-xs text-muted-foreground italic">
                        Menampilkan {{ departemens.from ?? 0 }} -
                        {{ departemens.to ?? 0 }} dari
                        {{ departemens.total }} data
                    </p>

                    <nav class="flex items-center gap-1">
                        <template
                            v-for="(link, k) in departemens.links"
                            :key="k"
                        >
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
