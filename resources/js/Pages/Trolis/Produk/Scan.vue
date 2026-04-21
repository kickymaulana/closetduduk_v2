<script setup lang="ts">
import { ref, onMounted } from "vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

// Shadcn Components
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { toast } from "vue-sonner";

// Icons
import {
    IconScan,
    IconLoader2,
    IconArrowLeft,
    IconCheck,
    IconShieldCheck
} from "@tabler/icons-vue";

const props = defineProps<{ troli: any }>();
const qrInput = ref<any>(null);
const form = useForm({ qr: "" });

onMounted(() => qrInput.value?.$el?.querySelector('input')?.focus());

const handleScan = () => {
    if (!form.qr) return;
    form.post(route('trolis.produk.scan_store', props.troli.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success("Terverifikasi!", { description: `Produk ${form.qr} sesuai.` });
            form.reset();
            qrInput.value?.$el?.querySelector('input')?.focus();
        },
        onError: (errors) => {
            toast.error("Gagal Validasi", { description: errors.qr });
            form.reset('qr');
        }
    });
};

defineOptions({ layout: AuthenticatedLayout });
</script>

<template>
    <Head title="Scan Validasi" />

    <div class="flex flex-col items-center justify-center min-h-[80vh] p-4 space-y-4">
        <div class="w-full max-w-md">
            <Button variant="ghost" as-child size="sm" class="group">
                <Link :href="route('trolis.produk.index', troli.id)">
                    <IconArrowLeft class="mr-2 size-4 transition-transform group-hover:-translate-x-1" />
                    Kembali
                </Link>
            </Button>
        </div>

        <Card class="w-full max-w-md border-2 shadow-xl border-blue-500/10">
            <CardHeader class="text-center pb-2">
                <CardTitle class="text-xl font-bold text-blue-700 flex items-center justify-center gap-2">
                    <IconShieldCheck class="size-5" /> Validasi Produk
                </CardTitle>
                <p class="text-xs font-mono text-muted-foreground bg-muted py-1 rounded">INV: {{ troli.invoice }}</p>
            </CardHeader>

            <CardContent class="space-y-6 py-6">
                <div class="flex justify-center">
                    <div class="relative p-6 bg-blue-50 rounded-full">
                        <IconScan :class="['size-16 text-blue-600', form.processing ? 'animate-pulse' : '']" />
                        <div v-if="form.recentlySuccessful" class="absolute -top-1 -right-1 bg-green-500 text-white p-1.5 rounded-full shadow-lg">
                            <IconCheck class="size-4" />
                        </div>
                    </div>
                </div>

                <div class="space-y-4 text-center">
                    <Input
                        ref="qrInput"
                        v-model="form.qr"
                        :disabled="form.processing"
                        placeholder="SCAN QR"
                        class="h-16 text-3xl text-center font-bold uppercase border-x-0 border-t-0 border-b-4 border-blue-200 focus-visible:ring-0 focus-visible:border-blue-600 rounded-none bg-transparent"
                        @keyup.enter="handleScan"
                    />

                    <div class="h-6">
                        <div v-if="form.processing" class="flex items-center justify-center gap-2 text-blue-600 animate-in fade-in">
                            <IconLoader2 class="animate-spin size-4" />
                            <span class="text-xs font-medium">Mengecek...</span>
                        </div>
                        <span v-else class="text-[10px] font-bold text-blue-400 uppercase tracking-widest">Mode: Verifikasi</span>
                    </div>
                </div>
            </CardContent>
        </Card>

        <div class="flex items-center gap-2 px-4 py-1.5 bg-white rounded-full border shadow-sm">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute h-full w-full rounded-full bg-green-400 opacity-75"></span>
                <span class="relative h-2 w-2 rounded-full bg-green-500"></span>
            </span>
            <span class="text-[10px] font-bold text-slate-500">SCANNER READY</span>
        </div>
    </div>
</template>
