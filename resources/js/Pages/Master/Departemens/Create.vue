<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
    CardDescription,
} from "@/components/ui/card";
import {
    IconArrowLeft,
    IconDeviceFloppy,
    IconLoader2,
    IconHierarchy2,
} from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

const form = useForm({
    departemen: "",
    urutan: 0,
});

const submit = () => {
    form.post(route("departemens.store"));
};
</script>

<template>
    <Head title="Tambah Departemen" />

    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1 md:pt-1">
        <div class="flex items-center gap-4">
            <Button
                variant="outline"
                size="icon"
                as-child
                class="rounded-full shadow-sm"
            >
                <Link :href="route('departemens.index')">
                    <IconArrowLeft class="size-4" />
                </Link>
            </Button>
            <div>
                <h2 class="text-3xl font-bold tracking-tight">
                    Tambah Departemen
                </h2>
                <p class="text-muted-foreground text-sm">
                    Input data departemen baru untuk alur monitoring.
                </p>
            </div>
        </div>

        <div class="max-w-2xl">
            <Card class="border-none shadow-lg">
                <CardHeader>
                    <div class="flex items-center gap-2 mb-1 text-primary">
                        <IconHierarchy2 class="size-5" />
                        <CardTitle>Struktur Departemen</CardTitle>
                    </div>
                    <CardDescription
                        >Tentukan nama departemen dan urutan tampilannya di
                        sistem.</CardDescription
                    >
                </CardHeader>

                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid gap-2">
                            <Label
                                for="departemen"
                                class="font-semibold text-primary"
                                >Nama Departemen</Label
                            >
                            <Input
                                id="departemen"
                                v-model="form.departemen"
                                placeholder="Contoh: OVEN, PACKING, QC..."
                                class="uppercase focus:ring-primary/50"
                                :class="{
                                    'border-destructive':
                                        form.errors.departemen,
                                }"
                            />
                            <p
                                v-if="form.errors.departemen"
                                class="text-sm text-destructive font-medium"
                            >
                                {{ form.errors.departemen }}
                            </p>
                        </div>

                        <div class="grid gap-2 max-w-[200px]">
                            <Label
                                for="urutan"
                                class="font-semibold text-primary"
                                >Nomor Urutan</Label
                            >
                            <Input
                                id="urutan"
                                type="number"
                                v-model="form.urutan"
                                placeholder="0"
                                :class="{
                                    'border-destructive': form.errors.urutan,
                                }"
                            />
                            <p
                                v-if="form.errors.urutan"
                                class="text-sm text-destructive font-medium"
                            >
                                {{ form.errors.urutan }}
                            </p>
                        </div>

                        <div class="flex justify-end pt-4 border-t">
                            <Button
                                type="submit"
                                :disabled="form.processing"
                                class="bg-primary hover:bg-primary/90 text-white shadow-md transition-all active:scale-95"
                            >
                                <IconLoader2
                                    v-if="form.processing"
                                    class="mr-2 size-4 animate-spin"
                                />
                                <IconDeviceFloppy v-else class="mr-2 size-4" />
                                Simpan Departemen
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
