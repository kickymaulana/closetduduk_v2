<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, Head, Link } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { toast } from "vue-sonner";
import {
    IconScan,
    IconLoader2,
    IconArrowLeft,
    IconCheck,
    IconShieldCheck
} from "@tabler/icons-vue";

const props = defineProps<{
    troli: any;
}>();

const qrInput = ref<any>(null);

const form = useForm({
    qr: "",
});

// Fungsi untuk memastikan input selalu fokus
const focusInput = () => {
    // Shadcn Input adalah wrapper, kita butuh elemen input di dalamnya
    qrInput.value?.$el?.querySelector('input')?.focus();
};

onMounted(() => {
    focusInput();
});

const handleScan = () => {
    if (!form.qr) return;

    form.post(route('trolis.produk.scan_store', props.troli.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success("Terverifikasi!", {
                description: `Produk ${form.qr} berhasil divalidasi untuk tim.`,
                duration: 2000,
            });
            form.reset();
            focusInput();
        },
        onError: (errors) => {
            // Menangkap error 'qr' (produk tidak ada) atau 'error' (sesi tidak ada)
            const message = errors.qr || errors.error || "Terjadi kesalahan validasi.";
            toast.error("Gagal Validasi", {
                description: message,
            });
            form.reset('qr');
            focusInput();
        }
    });
};

defineOptions({ layout: AuthenticatedLayout });
</script>

<template>
    <Head title="Scan Validasi Produk" />

    <div class="flex flex-col items-center justify-center min-h-[80vh] p-4 relative">

        <div class="w-full max-w-4xl grid grid-cols-3 gap-2 mb-6">
            <Button as-child variant="default" class="bg-blue-600 hover:bg-blue-700 shadow-lg border-b-4 border-blue-800">
                <Link :href="route('trolis.produk.scan', troli.id)">MODE OK</Link>
            </Button>
            <Button as-child variant="outline" class="text-orange-600 border-orange-200 hover:bg-orange-50">
                <Link :href="route('trolis.produk.scan_inproses', troli.id)">IN PROSES</Link>
            </Button>
            <Button as-child variant="outline" class="text-red-600 border-red-200 hover:bg-red-50">
                <Link :href="route('trolis.produk.scan_buang', troli.id)">BUANG</Link>
            </Button>
        </div>

        <div class="w-full max-w-md mb-4">
            <Button variant="ghost" as-child class="group text-muted-foreground hover:text-blue-600">
                <Link :href="route('trolis.produk.index', troli.id)">
                    <IconArrowLeft class="mr-2 size-4 transition-transform group-hover:-translate-x-1" />
                    Batal & Kembali
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
                <p class="text-muted-foreground font-mono text-sm tracking-widest bg-muted py-1 rounded-md mt-2">
                    INV: {{ troli.invoice }}
                </p>
            </CardHeader>

            <CardContent class="space-y-8 py-8">
                <div class="flex justify-center">
                    <div class="relative">
                        <div class="p-6 bg-blue-50 rounded-full">
                            <IconScan :class="['size-20 text-blue-600 transition-all', form.processing ? 'animate-pulse scale-110' : '']" />
                        </div>
                        <div v-if="form.recentlySuccessful" class="absolute -top-2 -right-2 bg-green-500 text-white p-2 rounded-full shadow-lg animate-in zoom-in">
                            <IconCheck class="size-5" />
                        </div>
                    </div>
                </div>

                <div class="space-y-4 text-center">
                    <Input
                        ref="qrInput"
                        v-model="form.qr"
                        :disabled="form.processing"
                        type="text"
                        class="w-full text-center border-b-4 border-t-0 border-x-0 border-blue-200 focus-visible:ring-0 focus-visible:border-blue-600 transition-all outline-none font-bold uppercase placeholder:text-slate-300 rounded-none bg-transparent"
                        style="font-size: 2.2rem; color: #1e40af; height: 80px;"
                        placeholder="SCAN DISINI"
                        @keyup.enter="handleScan"
                        autocomplete="off"
                    />

                    <div class="h-6">
                        <div v-if="form.processing" class="flex items-center justify-center gap-2 text-blue-600 font-medium">
                            <IconLoader2 class="animate-spin size-5" />
                            <span>Mengecek Data...</span>
                        </div>
                        <div v-else class="flex items-center justify-center gap-2">
                            <span class="text-[10px] font-black text-blue-400 uppercase tracking-[0.2em]">Mode: Verifikasi Tim</span>
                        </div>
                    </div>
                </div>
            </CardContent>
        </Card>

        <div class="mt-8 flex flex-col items-center gap-2 text-xs text-muted-foreground italic">
            <div class="flex items-center gap-2 px-3 py-1 bg-white border rounded-full shadow-sm">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative h-2 w-2 rounded-full bg-green-500"></span>
                </span>
                <span class="font-bold text-slate-500 uppercase tracking-tighter">Scanner Active</span>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Hilangkan outline default browser untuk input focus */
input:focus {
    outline: none !important;
    box-shadow: none !important;
}
</style>
