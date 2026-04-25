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
    IconClock,
} from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

// Sesuaikan field form dengan kolom di tabel 'shift'
const form = useForm({
    shift: "",
});

const submit = () => {
    form.post(route("shifts.store"), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Tambah Shift" />
    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">
        <div class="flex items-center gap-4">
            <Button variant="outline" size="icon" as-child class="rounded-full">
                <Link :href="route('shifts.index')">
                    <IconArrowLeft class="size-4" />
                </Link>
            </Button>
            <h2 class="text-3xl font-bold tracking-tight">
                Tambah Shift Kerja
            </h2>
        </div>

        <div class="max-w-2xl">
            <Card class="border-none shadow-lg">
                <CardHeader>
                    <CardTitle class="text-primary flex items-center gap-2">
                        <IconClock class="size-5" />
                        Master Data Shift
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid gap-2">
                            <Label for="shift">Nama Shift</Label>
                            <Input
                                id="shift"
                                v-model="form.shift"
                                placeholder="Contoh: SHIFT 1, SHIFT PAGI, atau NON-SHIFT"
                                class="uppercase"
                                :class="{
                                    'border-destructive': form.errors.shift,
                                }"
                                autofocus
                            />
                            <p
                                v-if="form.errors.shift"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.shift }}
                            </p>
                        </div>

                        <Button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full bg-primary hover:bg-primary/90 transition-all active:scale-[0.98]"
                        >
                            <IconLoader2
                                v-if="form.processing"
                                class="mr-2 animate-spin"
                            />
                            <IconDeviceFloppy v-else class="mr-2" />
                            Simpan Data Shift
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
