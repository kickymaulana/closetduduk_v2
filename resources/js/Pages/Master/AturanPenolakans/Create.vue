<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
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
} from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

defineProps<{ cacats: any[]; departemens: any[] }>();

const form = useForm({
    master_cacat_id: "",
    dep_toleransi: "",
    dep_buang: "",
    dep_pemeriksa: "",
});
</script>

<template>
    <Head title="Tambah Aturan" />
    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">
        <div class="flex items-center gap-4">
            <Button variant="outline" size="icon" as-child class="rounded-full"
                ><Link :href="route('aturanpenolakans.index')"
                    ><IconArrowLeft class="size-4" /></Link
            ></Button>
            <h2 class="text-3xl font-bold tracking-tight">Buat Aturan Baru</h2>
        </div>
        <div class="max-w-3xl">
            <Card class="border-none shadow-lg">
                <CardHeader
                    ><CardTitle class="text-primary text-lg"
                        >Konfigurasi Alur Penolakan</CardTitle
                    ></CardHeader
                >
                <CardContent>
                    <form
                        @submit.prevent="
                            form.post(route('aturanpenolakans.store'))
                        "
                        class="space-y-6"
                    >
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="grid gap-2">
                                <Label>Pilih Jenis Cacat</Label>
                                <Select v-model="form.master_cacat_id">
                                    <SelectTrigger
                                        :class="{
                                            'border-destructive':
                                                form.errors.master_cacat_id,
                                        }"
                                        ><SelectValue placeholder="Pilih Cacat"
                                    /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="c in cacats"
                                            :key="c.id"
                                            :value="c.id.toString()"
                                            >{{ c.nama_cacat }}</SelectItem
                                        >
                                    </SelectContent>
                                </Select>
                                <p
                                    v-if="form.errors.master_cacat_id"
                                    class="text-xs text-destructive italic"
                                >
                                    {{ form.errors.master_cacat_id }}
                                </p>
                            </div>

                            <div class="grid gap-2">
                                <Label>Departemen Pemeriksa</Label>
                                <Select v-model="form.dep_pemeriksa">
                                    <SelectTrigger
                                        ><SelectValue
                                            placeholder="Pilih Departemen"
                                    /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="d in departemens"
                                            :key="d.id"
                                            :value="d.id.toString()"
                                            >{{ d.departemen }}</SelectItem
                                        >
                                    </SelectContent>
                                </Select>
                            </div>

                            <div class="grid gap-2">
                                <Label>Departemen Toleransi</Label>
                                <Select v-model="form.dep_toleransi">
                                    <SelectTrigger
                                        ><SelectValue
                                            placeholder="Pilih Departemen"
                                    /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="d in departemens"
                                            :key="d.id"
                                            :value="d.id.toString()"
                                            >{{ d.departemen }}</SelectItem
                                        >
                                    </SelectContent>
                                </Select>
                            </div>

                            <div class="grid gap-2">
                                <Label>Departemen Buang (Reject)</Label>
                                <Select v-model="form.dep_buang">
                                    <SelectTrigger
                                        ><SelectValue
                                            placeholder="Pilih Departemen"
                                    /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="d in departemens"
                                            :key="d.id"
                                            :value="d.id.toString()"
                                            >{{ d.departemen }}</SelectItem
                                        >
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>

                        <div class="pt-4 border-t">
                            <Button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full bg-primary h-11"
                            >
                                <IconLoader2
                                    v-if="form.processing"
                                    class="mr-2 animate-spin"
                                />
                                <IconDeviceFloppy v-else class="mr-2" /> Simpan
                                Konfigurasi
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
