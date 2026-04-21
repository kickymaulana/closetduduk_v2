<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, Head, Link } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { toast } from "vue-sonner";
import {
    IconScan,
    IconLoader2,
    IconArrowLeft,
    IconCheck,
    IconShieldCheck,
} from "@tabler/icons-vue";

const props = defineProps<{
    troli: any;
}>();

const qrInput = ref<HTMLInputElement | null>(null);

const form = useForm({
    qr: "",
});

onMounted(() => {
    qrInput.value?.focus();
});

const handleScan = () => {
    if (!form.qr) return;

    // Mengarah ke method scan_store yang hanya mengupdate status 'sudah_scan'
    form.post(route('trolis.produk.scan_store', props.troli.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success("Terverifikasi!", {
                description: `Produk ${form.qr} sesuai dengan isi troli.`,
                duration: 2000,
            });
            form.reset();
            qrInput.value?.focus();
        },
        onError: (errors) => {
            toast.error("Gagal Validasi", {
                description: errors.qr || "Produk tidak terdaftar di troli ini.",
            });
            form.reset('qr');
            qrInput.value?.focus();
        }
    });
};

defineOptions({ layout: AuthenticatedLayout });
</script>

<template>
    <Head title="Scan Validasi Produk" />

    <div class="flex flex-col items-center justify-center min-h-[80vh] p-4">

        <div class="w-full max-w-md mb-4">
            <Button variant="ghost" as-child class="group">
                <Link :href="route('trolis.produk.index', troli.id)">
                    <IconArrowLeft class="mr-2 size-4 transition-transform group-hover:-translate-x-1" />
                    Kembali ke Isi Troli
                </Link>
            </Button>
        </div>

        <Card class="w-full max-w-md border-2 border-blue-500/20 shadow-xl overflow-hidden">
            <div class="h-2 bg-blue-600 w-full"></div>

            <CardHeader class="text-center">
                <CardTitle class="text-2xl font-bold text-blue-700 flex items-center justify-center gap-2">
                    <IconShieldCheck class="size-6" />
                    Validasi Produk
                </CardTitle>
                <div class="flex flex-col gap-1 mt-2">
                    <p class="text-muted-foreground font-mono text-sm bg-muted py-1 rounded-md">
                        INV: {{ troli.invoice }}
                    </p>
                </div>
            </CardHeader>

            <CardContent class="space-y-8 py-8">
                <div class="flex justify-center">
                    <div class="relative">
                        <div class="p-6 bg-blue-50 rounded-full">
                            <IconScan :class="[
                                'size-20 text-blue-600 transition-all',
                                form.processing ? 'animate-pulse scale-110' : ''
                            ]" />
                        </div>
                        <div v-if="form.recentlySuccessful" class="absolute -top-2 -right-2 bg-green-500 text-white p-2 rounded-full shadow-lg animate-in zoom-in">
                            <IconCheck class="size-5" />
                        </div>
                    </div>
                </div>

                <div class="space-y-4 text-center">
                    <input
                        ref="qrInput"
                        v-model="form.qr"
                        :disabled="form.processing"
                        type="text"
                        class="w-full text-center border-b-4 border-t-0 border-x-0 border-blue-200 focus:border-blue-600 focus:ring-0 transition-all outline-none font-bold uppercase placeholder:text-slate-300"
                        style="background: transparent; font-size: 2.5rem; color: #1e3a8a; height: 80px;"
                        placeholder="SCAN QR"
                        @keyup.enter="handleScan"
                        autocomplete="off"
                    >

                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-semibold text-blue-500 uppercase tracking-widest">
                            Mode: Verifikasi Barang
                        </span>
                        <div v-if="form.processing" class="flex items-center justify-center gap-2 text-blue-600">
                            <IconLoader2 class="animate-spin size-4" />
                            <span class="text-sm font-medium">Mengecek data...</span>
                        </div>
                    </div>
                </div>
            </CardContent>
        </Card>

        <div class="mt-8 flex items-center gap-3 px-4 py-2 bg-white rounded-full shadow-sm border border-slate-100">
            <span class="relative flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
            </span>
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-tighter">Scanner Ready</span>
        </div>
    </div>
</template>
