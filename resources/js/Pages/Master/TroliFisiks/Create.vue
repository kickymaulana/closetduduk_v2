<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import {
    IconArrowLeft,
    IconDeviceFloppy,
    IconLoader2,
} from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

// Inisialisasi form dengan field 'nomor'
const form = useForm({
    nomor: "",
    status: "",
});

const submit = () => {
    form.post(route("trolifisiks.store"), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Tambah Troli Fisik" />

    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">
        <div class="flex items-center gap-4">
            <Button
                variant="outline"
                size="icon"
                as-child
                class="rounded-full shadow-sm"
            >
                <Link :href="route('trolifisiks.index')">
                    <IconArrowLeft class="size-4" />
                </Link>
            </Button>
            <h2 class="text-3xl font-bold tracking-tight">
                Tambah Troli Fisik
            </h2>
        </div>

        <div class="max-w-2xl">
            <Card class="border-none shadow-lg">
                <CardHeader>
                    <CardTitle class="text-primary"
                        >Master Data Troli</CardTitle
                    >
                </CardHeader>

                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid gap-2">
                            <Label for="nomor">Nomor Troli</Label>
                            <Input
                                id="nomor"
                                v-model="form.nomor"
                                placeholder="Contoh: 001"
                                class="uppercase"
                                :class="{
                                    'border-destructive': form.errors.nomor,
                                }"
                            />

                            <p
                                v-if="form.errors.nomor"
                                class="text-sm text-destructive font-medium"
                            >
                                {{ form.errors.nomor }}
                            </p>
                        </div>

                        <div class="grid gap-2">
                            <Label for="status">Status Penggunaan</Label>
                            <select
                                id="status"
                                v-model="form.status"
                                class="flex h-11 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring shadow-sm"
                            >
                                <option value="Tidak">Tidak</option>
                                <option value="Digunakan">Digunakan</option>
                            </select>
                            <p
                                v-if="form.errors.status"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.status }}
                            </p>
                        </div>

                        <Button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full bg-primary hover:bg-primary/90 shadow-md transition-all active:scale-95"
                        >
                            <IconLoader2
                                v-if="form.processing"
                                class="mr-2 animate-spin size-4"
                            />
                            <IconDeviceFloppy v-else class="mr-2 size-4" />
                            Simpan Data Troli
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
