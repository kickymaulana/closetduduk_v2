<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Label } from "@/components/ui/label";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import {
    IconArrowLeft,
    IconDeviceFloppy,
    IconLoader2,
    IconDotsVertical,
    IconTrash,
} from "@tabler/icons-vue";
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from "@/components/ui/alert-dialog";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{ aturan: any; cacats: any[]; departemens: any[] }>();

const form = useForm({
    master_cacat_id: props.aturan.master_cacat_id.toString(),
    dep_toleransi: props.aturan.dep_toleransi.toString(),
    dep_buang: props.aturan.dep_buang.toString(),
    dep_pemeriksa: props.aturan.dep_pemeriksa.toString(),
});
</script>

<template>
    <Head title="Edit Aturan" />
    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">
        <div class="flex items-center justify-between max-w-3xl">
            <div class="flex items-center gap-4">
                <Button
                    variant="outline"
                    size="icon"
                    as-child
                    class="rounded-full"
                    ><Link :href="route('aturanpenolakans.index')"
                        ><IconArrowLeft class="size-4" /></Link
                ></Button>
                <h2 class="text-3xl font-bold tracking-tight">Edit Aturan</h2>
            </div>
        </div>
        <div class="max-w-3xl">
            <Card class="border-none shadow-lg">
                <CardHeader
                    class="flex flex-row items-center justify-between border-b bg-muted/20"
                >
                    <CardTitle class="text-primary"
                        >Update Konfigurasi</CardTitle
                    >
                    <AlertDialog>
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child
                                ><Button variant="ghost" size="icon"
                                    ><IconDotsVertical class="size-4" /></Button
                            ></DropdownMenuTrigger>
                            <DropdownMenuContent align="end"
                                ><AlertDialogTrigger as-child
                                    ><DropdownMenuItem class="text-destructive"
                                        ><IconTrash class="mr-2 size-4" />Hapus
                                        Aturan</DropdownMenuItem
                                    ></AlertDialogTrigger
                                ></DropdownMenuContent
                            >
                        </DropdownMenu>
                        <AlertDialogContent>
                            <AlertDialogHeader
                                ><AlertDialogTitle
                                    >Hapus Aturan Ini?</AlertDialogTitle
                                ><AlertDialogDescription
                                    >Tindakan ini
                                    permanen.</AlertDialogDescription
                                ></AlertDialogHeader
                            >
                            <AlertDialogFooter
                                ><AlertDialogCancel>Batal</AlertDialogCancel
                                ><AlertDialogAction
                                    @click="
                                        router.delete(
                                            route(
                                                'aturanpenolakans.destroy',
                                                props.aturan.id,
                                            ),
                                        )
                                    "
                                    class="bg-destructive text-white"
                                    >Ya, Hapus</AlertDialogAction
                                ></AlertDialogFooter
                            >
                        </AlertDialogContent>
                    </AlertDialog>
                </CardHeader>
                <CardContent class="pt-6">
                    <form
                        @submit.prevent="
                            form.put(
                                route(
                                    'aturanpenolakans.update',
                                    props.aturan.id,
                                ),
                            )
                        "
                        class="space-y-6"
                    >
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="grid gap-2">
                                <Label>Jenis Cacat</Label>
                                <Select v-model="form.master_cacat_id">
                                    <SelectTrigger
                                        ><SelectValue
                                    /></SelectTrigger>
                                    <SelectContent
                                        ><SelectItem
                                            v-for="c in cacats"
                                            :key="c.id"
                                            :value="c.id.toString()"
                                            >{{ c.nama_cacat }}</SelectItem
                                        ></SelectContent
                                    >
                                </Select>
                            </div>
                            <div class="grid gap-2">
                                <Label>Departemen Pemeriksa</Label>
                                <Select v-model="form.dep_pemeriksa">
                                    <SelectTrigger
                                        ><SelectValue
                                    /></SelectTrigger>
                                    <SelectContent
                                        ><SelectItem
                                            v-for="d in departemens"
                                            :key="d.id"
                                            :value="d.id.toString()"
                                            >{{ d.departemen }}</SelectItem
                                        ></SelectContent
                                    >
                                </Select>
                            </div>
                            <div class="grid gap-2">
                                <Label>Departemen Toleransi</Label>
                                <Select v-model="form.dep_toleransi">
                                    <SelectTrigger
                                        ><SelectValue
                                    /></SelectTrigger>
                                    <SelectContent
                                        ><SelectItem
                                            v-for="d in departemens"
                                            :key="d.id"
                                            :value="d.id.toString()"
                                            >{{ d.departemen }}</SelectItem
                                        ></SelectContent
                                    >
                                </Select>
                            </div>
                            <div class="grid gap-2">
                                <Label>Departemen Buang</Label>
                                <Select v-model="form.dep_buang">
                                    <SelectTrigger
                                        ><SelectValue
                                    /></SelectTrigger>
                                    <SelectContent
                                        ><SelectItem
                                            v-for="d in departemens"
                                            :key="d.id"
                                            :value="d.id.toString()"
                                            >{{ d.departemen }}</SelectItem
                                        ></SelectContent
                                    >
                                </Select>
                            </div>
                        </div>
                        <Button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full bg-primary h-11"
                            ><IconDeviceFloppy class="mr-2 size-4" />Update
                            Aturan</Button
                        >
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
