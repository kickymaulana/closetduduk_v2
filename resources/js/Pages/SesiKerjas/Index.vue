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
import { IconPlus, IconPencil, IconSearch, IconClock, IconCircleCheck } from "@tabler/icons-vue";
import { ref, watch } from "vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    sesikerjas: any;
    filters: { search: string };
    sesi_kerja_id: number | null;
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

const toggleSesi = (id: number) => {
    if (props.sesi_kerja_id === id) {
        // Jika sedang aktif, maka jalankan fungsi nonaktif
        router.delete(route('sesikerjas.nonaktif', id));
    } else {
        // Jika tidak aktif, jalankan fungsi aktifkan
        router.post(route('sesikerjas.aktifkan', id));
    }
};
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
                                <TableHead>Anggota</TableHead>
                                <TableHead>Jenis</TableHead>
                                <TableHead>Jam Masuk</TableHead>
                                <TableHead>Jam Pulang</TableHead>
                                <TableHead>Pengerjaan</TableHead>
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
    <div class="flex flex-wrap gap-1 max-w-[200px]">
        <Badge
            v-for="member in item.sesi_kerja_members"
            :key="member.id"
            variant="outline"
            class="text-[10px] px-2 py-0 bg-muted/50"
        >
            {{ member.user.name }}
        </Badge>
        <span v-if="item.sesi_kerja_members.length === 0" class="text-xs text-muted-foreground italic">
            Tanpa Anggota
        </span>
    </div>
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


                                <TableCell>
    <div class="flex items-center gap-2">
        <Badge variant="outline" class="font-mono text-sm border-primary/50 text-primary">
            {{ item.total_produk }}
        </Badge>
        <span class="text-xs text-muted-foreground uppercase italic font-medium">Produk</span>
    </div>
</TableCell>


                                <TableCell class="text-right">
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        as-child
                                    >
                                        <Link :href="route('sesikerjas.show', item.id)">
                                            <IconPencil class="size-4 text-primary" />
                                        </Link>
                                    </Button>
                                    <Button
                                        @click="toggleSesi(item.id)"
                                        :variant="sesi_kerja_id === item.id ? 'destructive' : 'outline'"
                                        size="sm"
                                    >
                                        <IconCircleCheck v-if="sesi_kerja_id === item.id" class="mr-1 size-4" />
                                        {{ sesi_kerja_id === item.id ? 'Nonaktifkan' : 'Aktifkan' }}
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
