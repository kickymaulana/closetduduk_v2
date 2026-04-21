<script setup lang="ts">
import { Head, Link, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

// Shadcn Components (Yang sudah ada saja)
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";

// Icons
import {
    IconArrowLeft,
    IconArrowBackUp,
    IconLoader2,
    IconHistory,
    IconCircleCheck,
    IconChevronRight
} from "@tabler/icons-vue";

interface Proses {
    id: number;
    nama_proses: string;
}

const props = defineProps<{
    troli: any,
    prosesTujuan: Proses[]
}>();

const form = useForm({
    proses_id: null as number | null,
});

const selectProses = (id: number) => {
    form.proses_id = id;
};

const submit = () => {
    if (!form.proses_id) return;
    form.post(route('trolis.kembalikan_store', props.troli.id));
};

defineOptions({ layout: AuthenticatedLayout });
</script>

<template>
    <Head title="Kembalikan Troli" />

    <div class="flex flex-col items-center justify-center min-h-[80vh] p-4 space-y-4">
        <div class="w-full max-w-md">
            <Button variant="ghost" as-child size="sm" class="group">
                <Link :href="route('trolis.index')">
                    <IconArrowLeft class="mr-2 size-4 transition-transform group-hover:-translate-x-1" />
                    Batal
                </Link>
            </Button>
        </div>

        <Card class="w-full max-w-md border-2 shadow-xl border-amber-500/10">
            <CardHeader class="text-center pb-2">
                <CardTitle class="text-xl font-bold text-amber-700 flex items-center justify-center gap-2">
                    <IconHistory class="size-5" /> Pilih Proses Tujuan
                </CardTitle>
                <p class="text-xs font-mono text-muted-foreground bg-muted py-1 rounded">INV: {{ troli.invoice }}</p>
            </CardHeader>

            <CardContent class="space-y-6 py-6">
                <div class="space-y-3">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block text-center">
                        Mundur Ke Tahap:
                    </span>

                    <div class="grid gap-2">
                        <button
                            v-for="proses in prosesTujuan"
                            :key="proses.id"
                            type="button"
                            @click="selectProses(proses.id)"
                            class="flex items-center justify-between w-full p-4 rounded-xl border-2 transition-all text-left"
                            :class="form.proses_id === proses.id
                                ? 'border-amber-500 bg-amber-50 shadow-md ring-1 ring-amber-500'
                                : 'border-slate-100 bg-white hover:border-slate-300 hover:bg-slate-50'"
                        >
                            <div class="flex items-center gap-3">
                                <div :class="['p-2 rounded-full', form.proses_id === proses.id ? 'bg-amber-500 text-white' : 'bg-slate-100 text-slate-400']">
                                    <IconHistory size="16" />
                                </div>
                                <span :class="['font-bold', form.proses_id === proses.id ? 'text-amber-700' : 'text-slate-600']">
                                    {{ proses.proses }}
                                </span>
                            </div>
                            <IconCircleCheck v-if="form.proses_id === proses.id" class="text-amber-600 size-6" />
                            <IconChevronRight v-else class="text-slate-300 size-5" />
                        </button>

                        <div v-if="prosesTujuan.length === 0" class="text-center py-10 border-2 border-dashed rounded-xl">
                            <p class="text-sm text-slate-400">Tidak ada proses sebelumnya.</p>
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <Button
                        @click="submit"
                        class="w-full h-14 text-lg font-bold bg-amber-600 hover:bg-amber-700 shadow-lg shadow-amber-200"
                        :disabled="form.processing || !form.proses_id"
                    >
                        <IconLoader2 v-if="form.processing" class="mr-2 size-5 animate-spin" />
                        <IconArrowBackUp v-else class="mr-2 size-5" />
                        KEMBALIKAN SEKARANG
                    </Button>
                </div>
            </CardContent>
        </Card>

        <div class="flex items-center gap-2 px-4 py-1.5 bg-white rounded-full border shadow-sm">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Manual Rollback Mode</span>
        </div>
    </div>
</template>
