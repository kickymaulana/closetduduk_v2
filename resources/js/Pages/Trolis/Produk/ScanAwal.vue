<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, Head, Link } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { toast } from "vue-sonner"; // Pakai sonner
import {
    IconScan,
    IconLoader2,
    IconArrowLeft,
    IconCheck,
    IconAlertCircle
} from "@tabler/icons-vue";

const props = defineProps<{
    troli: any;
}>();

const qrInput = ref<HTMLInputElement | null>(null);
const isFull = ref(false);

const form = useForm({
    qr: "",
});

onMounted(() => {
    qrInput.value?.focus();
});

const scan = () => {
    // Pastikan input tidak kosong sebelum kirim
    if (!form.qr) return;

    form.post(route('trolis.produk.scan_awal_store', props.troli.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Sonner Success
            toast.success("Berhasil!", {
                description: `Produk ${form.qr} berhasil ditambahkan ke troli.`,
                duration: 3000,
            });
            form.reset();
            qrInput.value?.focus();
        },
        onError: (errors) => {
            const message = errors.qr || errors.error || "Terjadi kesalahan sistem.";
            // Sonner Error
            toast.error("Gagal Scan", {
                description: message,
            });
            form.reset('qr');
            qrInput.value?.focus();
        }
    });
};

defineOptions({ layout: AuthenticatedLayout });
</script>

<template>
    <Head title="Scan Masuk Produk" />

    <div class="flex flex-col items-center justify-center min-h-[80vh] p-4 relative">

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
                        :disabled="isFull || form.processing"
                        type="text"
                        class="w-full text-center border-b-4 border-t-0 border-x-0 border-primary/30 focus:border-primary focus:ring-0 transition-all outline-none font-bold uppercase placeholder:text-muted/20"
                        style="background: transparent; font-size: 2.2rem; color: #1e3a8a; height: 80px;"
                        placeholder="SCAN DISINI"
                        @keyup.enter="scan"
                        autocomplete="off"
                    >

                    <div v-if="form.processing" class="flex items-center justify-center gap-2 text-primary font-medium">
                        <IconLoader2 class="animate-spin size-5" />
                        <span>Menyimpan...</span>
                    </div>
                </div>
            </CardContent>
        </Card>

        <div class="mt-8 flex flex-col items-center gap-2 text-xs text-muted-foreground italic">
            <p>Scanner mode active</p>
            <div class="flex gap-2">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-ping"></span>
                <span>Ready to receive data</span>
            </div>
        </div>
    </div>
</template>
