<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, Head, Link } from "@inertiajs/vue3";
import { ref, onMounted, nextTick, watch } from "vue";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { toast } from "vue-sonner";
import {
    IconScan,
    IconLoader2,
    IconArrowLeft,
    IconCheck,
} from "@tabler/icons-vue";

const props = defineProps<{
    troli: any;
}>();

const qrInput = ref<HTMLInputElement | null>(null);

const form = useForm({
    qr: "",
});

/**
 * FUNGSI FOKUS UTAMA
 * Menggunakan setTimeout 50ms untuk memastikan transisi DOM
 * Inertia selesai sebelum meminta fokus kembali.
 */
const forceFocus = () => {
    setTimeout(() => {
        if (qrInput.value) {
            qrInput.value.focus();
        }
    }, 50);
};

// 1. Fokus saat halaman pertama kali terbuka
onMounted(() => {
    forceFocus();
});

/**
 * 2. WATCHDOG: Pantau status 'processing'
 * Ketika form selesai (processing berubah dari true ke false),
 * sistem otomatis memicu fokus kembali. Ini jauh lebih stabil
 * daripada menaruhnya di onSuccess.
 */
watch(() => form.processing, (isProcessing) => {
    if (!isProcessing) {
        forceFocus();
    }
});

const scan = () => {
    if (!form.qr || form.processing) return;

    form.post(route('trolis.produk.scan_awal_store', props.troli.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success("Berhasil!", {
                description: `Produk ${form.qr} berhasil dicatat.`,
                duration: 2000,
            });
            form.reset();
            // Fokus otomatis ditangani oleh 'watch' di atas
        },
        onError: (errors) => {
            const message = errors.qr || errors.error || "Gagal menyimpan data.";
            toast.error("Gagal Scan", { description: message });
            form.reset('qr');
            forceFocus();
        }
    });
};

defineOptions({ layout: AuthenticatedLayout });
</script>

<template>
    <Head title="Scan Masuk Produk" />

    <div class="flex flex-col items-center justify-center min-h-[80vh] p-4 relative" @click="forceFocus">

        <div class="w-full max-w-md mb-4">
            <Button variant="ghost" as-child class="group text-muted-foreground hover:text-primary">
                <Link :href="route('trolis.produk.index', troli.id)">
                    <IconArrowLeft class="mr-2 size-4 transition-transform group-hover:-translate-x-1" />
                    Batal & Kembali
                </Link>
            </Button>
        </div>

        <Card class="w-full max-w-md border-2 border-primary/20 shadow-xl overflow-hidden">
            <div class="h-2 bg-primary w-full"></div>
            <CardHeader class="text-center">
                <CardTitle class="text-2xl font-bold text-primary flex items-center justify-center gap-2">
                    <IconScan class="size-6" />
                    Scan Produk
                </CardTitle>
                <p class="text-muted-foreground font-mono text-sm tracking-widest bg-muted py-1 rounded-md mt-2">
                    INV: {{ troli.invoice }}
                </p>
            </CardHeader>

            <CardContent class="space-y-8 py-8">
                <div class="flex justify-center">
                    <div class="relative">
                        <div class="p-6 bg-primary/10 rounded-full">
                            <IconScan :class="['size-20 text-primary transition-all', form.processing ? 'animate-pulse scale-110' : '']" />
                        </div>
                        <div v-if="form.recentlySuccessful" class="absolute -top-2 -right-2 bg-green-500 text-white p-2 rounded-full shadow-lg animate-in zoom-in">
                            <IconCheck class="size-5" />
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <input
                        ref="qrInput"
                        v-model="form.qr"
                        :disabled="form.processing"
                        type="text"
                        maxlength="10"
                        class="w-full text-center border-b-4 border-t-0 border-x-0 border-primary/30 focus:border-primary focus:ring-0 transition-all outline-none font-bold placeholder:text-muted/20 uppercase"
                        style="background: transparent; font-size: 2.2rem; color: #1e3a8a; height: 80px;"
                        placeholder="SCAN DISINI"
                        @keyup.enter="scan"
                        @input="form.qr = form.qr.toUpperCase()"
                        @blur="forceFocus"
                        autocomplete="off"
                    >

                    <div v-if="form.processing" class="flex items-center justify-center gap-2 text-primary font-medium">
                        <IconLoader2 class="animate-spin size-5" />
                        <span>Menyimpan...</span>
                    </div>

                    <p v-if="form.errors.qr" class="text-sm text-red-600 text-center font-semibold animate-pulse">
                        {{ form.errors.qr }}
                    </p>
                </div>
            </CardContent>
        </Card>

        <div class="mt-8 flex flex-col items-center gap-2 text-xs text-muted-foreground italic">
            <p>Scanner Mode Active (Autofocus Locked)</p>
            <div class="flex gap-2 text-green-600 font-medium">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-ping"></span>
                <span>Siap menerima input QR</span>
            </div>
        </div>
    </div>
</template>
