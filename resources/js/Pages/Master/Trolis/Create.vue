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
const form = useForm({ nomor: "" });
</script>

<template>
    <Head title="Tambah Cacat" />
    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">
        <div class="flex items-center gap-4">
            <Button variant="outline" size="icon" as-child class="rounded-full"
                ><Link :href="route('master.troli.index')"
                    ><IconArrowLeft class="size-4" /></Link
            ></Button>
            <h2 class="text-3xl font-bold tracking-tight">Tambah Troli</h2>
        </div>
        <div class="max-w-2xl">
            <Card class="border-none shadow-lg">
                <CardHeader
                    ><CardTitle class="text-primary"
                        >Master Data Troli</CardTitle
                    ></CardHeader
                >
                <CardContent>
                    <form
                        @submit.prevent="form.post(route('master.troli.store'))"
                        class="space-y-6"
                    >
                        <div class="grid gap-2">
                            <Label for="cacat">Nomor Troli</Label>
                            <Input
                                id="nomor"
                                v-model="form.nomor"
                                placeholder="Contoh: D203"
                                class=""
                            />
                            <p
                                v-if="form.errors.nomor"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.nomor }}
                            </p>
                        </div>
                        <Button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full bg-primary hover:bg-primary/90"
                        >
                            <IconLoader2
                                v-if="form.processing"
                                class="mr-2 animate-spin"
                            />
                            <IconDeviceFloppy v-else class="mr-2" /> Simpan Data
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
