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

const props = defineProps<{ aturan: any; cacats: any[]; proses: any[] }>();

const form = useForm({
    cacat_id: props.aturan.cacat_id.toString(),
    proses_toleransi: props.aturan.proses_toleransi.toString(),
    proses_buang: props.aturan.proses_buang.toString(),
    proses_pemeriksa: props.aturan.proses_pemeriksa.toString(),
});

const submit = () => {
    form.put(route('aturanpenolakans.update', props.aturan.id));
};
</script>

<template>
    <Head title="Edit Aturan" />
    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">
        <div class="flex items-center justify-between max-w-4xl">
            <div class="flex items-center gap-4">
                <Button
                    variant="outline"
                    size="icon"
                    as-child
                    class="rounded-full"
                >
                    <Link :href="route('aturanpenolakans.index')">
                        <IconArrowLeft class="size-4" />
                    </Link>
                </Button>
                <h2 class="text-3xl font-bold tracking-tight">Edit Aturan</h2>
            </div>
        </div>

        <div class="max-w-4xl">
            <Card class="border-none shadow-lg">
                <CardHeader class="flex flex-row items-center justify-between border-b">
                    <CardTitle class="text-primary">Update Konfigurasi</CardTitle>

                    <AlertDialog>
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button variant="ghost" size="icon">
                                    <IconDotsVertical class="size-4" />
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                                <AlertDialogTrigger as-child>
                                    <DropdownMenuItem class="text-destructive">
                                        <IconTrash class="mr-2 size-4" />
                                        Hapus Aturan
                                    </DropdownMenuItem>
                                </AlertDialogTrigger>
                            </DropdownMenuContent>
                        </DropdownMenu>

                        <AlertDialogContent>
                            <AlertDialogHeader>
                                <AlertDialogTitle>Hapus Aturan Ini?</AlertDialogTitle>
                                <AlertDialogDescription>
                                    Tindakan ini permanen dan tidak dapat dibatalkan.
                                </AlertDialogDescription>
                            </AlertDialogHeader>
                            <AlertDialogFooter>
                                <AlertDialogCancel>Batal</AlertDialogCancel>
                                <AlertDialogAction
                                    @click="router.delete(route('aturanpenolakans.destroy', props.aturan.id))"
                                    class="bg-destructive text-white hover:bg-destructive/90"
                                >
                                    Ya, Hapus
                                </AlertDialogAction>
                            </AlertDialogFooter>
                        </AlertDialogContent>
                    </AlertDialog>
                </CardHeader>

                <CardContent class="pt-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Grid Container -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                            <!-- Baris 1: Jenis Cacat (Full Width / Span 3) -->
                            <div class="grid gap-2 md:col-span-3">
                                <Label for="cacat">Jenis Cacat</Label>
                                <Select v-model="form.cacat_id">
                                    <SelectTrigger id="cacat">
                                        <SelectValue placeholder="Pilih Jenis Cacat" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="c in cacats"
                                            :key="c.id"
                                            :value="c.id.toString()"
                                        >
                                            {{ c.cacat }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>

                            <!-- Baris 2 Kolom 1: Departemen Toleransi -->
                            <div class="grid gap-2">
                                <Label for="toleransi">Departemen Toleransi</Label>
                                <Select v-model="form.proses_toleransi">
                                    <SelectTrigger id="toleransi">
                                        <SelectValue placeholder="Pilih Departemen" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="d in proses"
                                            :key="d.id"
                                            :value="d.id.toString()"
                                        >
                                            {{ d.proses }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>

                            <!-- Baris 2 Kolom 2: Departemen Buang -->
                            <div class="grid gap-2">
                                <Label for="buang">Departemen Buang</Label>
                                <Select v-model="form.proses_buang">
                                    <SelectTrigger id="buang">
                                        <SelectValue placeholder="Pilih Departemen" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="d in proses"
                                            :key="d.id"
                                            :value="d.id.toString()"
                                        >
                                            {{ d.proses }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>

                            <!-- Baris 2 Kolom 3: Departemen Pemeriksa -->
                            <div class="grid gap-2">
                                <Label for="pemeriksa">Departemen Pemeriksa</Label>
                                <Select v-model="form.proses_pemeriksa">
                                    <SelectTrigger id="pemeriksa">
                                        <SelectValue placeholder="Pilih Departemen" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="d in proses"
                                            :key="d.id"
                                            :value="d.id.toString()"
                                        >
                                            {{ d.proses }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>

                        <Button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full bg-primary h-11"
                        >
                            <IconDeviceFloppy v-if="!form.processing" class="mr-2 size-4" />
                            <span v-else class="mr-2 animate-spin">
                                <!-- Anda bisa menggunakan IconLoader2 di sini jika diimpor -->
                                ⏳
                            </span>
                            Update Aturan
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
