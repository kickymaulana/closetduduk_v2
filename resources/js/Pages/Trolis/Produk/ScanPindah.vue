<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, Head, Link } from "@inertiajs/vue3";
import { ref, onMounted, watch } from "vue";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { toast } from "vue-sonner";
import {
    IconScan,
    IconLoader2,
    IconArrowLeft,
    IconArrowsExchange,
    IconClipboard,
    IconShoppingCart,
    IconHistory,
    IconAlertCircle
} from "@tabler/icons-vue";

const props = defineProps<{
    troli: any;
}>();

const qrInput = ref<HTMLInputElement | null>(null);
const tujuanInput = ref<HTMLInputElement | null>(null);
const scanHistory = ref<Array<{ code: string; time: string; success: boolean; target: string }>>([]);

const form = useForm({
    qr: "",
    nomor_tujuan: "", // Pastikan sama dengan di Controller
});

const focusInput = () => {
    setTimeout(() => {
        if (!form.nomor_tujuan) {
            tujuanInput.value?.focus();
        } else {
            qrInput.value?.focus();
        }
    }, 50);
};

onMounted(() => focusInput());

// Watcher untuk mengembalikan fokus kursor
watch(() => form.processing, (isProc) => { if (!isProc) focusInput(); });

/**
 * FUNGSI PASTE
 * Menempelkan isi clipboard ke input nomor tujuan
 */
const handlePaste = async () => {
    try {
        const text = await navigator.clipboard.readText();
        form.nomor_tujuan = text.trim().toUpperCase();
        toast.info("Nomor ditempel (Paste)");
        focusInput();
    } catch (err) {
        toast.error("Gagal membaca clipboard. Pastikan izin browser aktif.");
    }
};

const handleTransfer = () => {
    if (!form.nomor_tujuan) {
        toast.error("Isi Troli Tujuan!");
        tujuanInput.value?.focus();
        return;
    }
    if (!form.qr || form.processing) return;

    const currentQr = form.qr.toUpperCase();
    const currentTarget = form.nomor_tujuan;

    form.post(route('trolis.produk.scan_pindah_store', props.troli.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success("Berhasil Dipindahkan!");
            addToHistory(currentQr, true, currentTarget);
            form.qr = ""; // Hanya reset QR agar tujuan tetap sticky
        },
        onError: (errors) => {
            const errorMsg = errors.qr || errors.nomor_tujuan || "Gagal memproses data.";
            toast.error("Gagal", { description: errorMsg });
            addToHistory(currentQr, false, currentTarget);
            form.reset('qr');
            focusInput();
        }
    });
};

const addToHistory = (code: string, success: boolean, target: string) => {
    const timeString = new Date().toLocaleTimeString('id-ID');
    scanHistory.value.unshift({ code, time: timeString, success, target });
    if (scanHistory.value.length > 5) scanHistory.value.pop();
};

defineOptions({ layout: AuthenticatedLayout });
</script>

<template>
    <Head title="Pindah Isi Troli" />

    <div class="max-w-5xl mx-auto p-4 md:p-6 space-y-6" @click="focusInput">

        <div class="flex items-center justify-between border-b pb-4">
            <div class="flex items-center gap-3">
                <Button variant="outline" size="icon" as-child>
                    <Link :href="route('trolis.produk.index', troli.id)">
                        <IconArrowLeft class="size-5" />
                    </Link>
                </Button>
                <div>
                    <h1 class="text-xl font-bold text-slate-800">Transfer Antar Troli</h1>
                    <div class="flex items-center gap-2">
                        <Badge variant="destructive">ASAL</Badge>
                        <span class="font-mono font-bold text-slate-600">{{ troli.nomor }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <Card class="lg:col-span-5 border-2 border-slate-200 shadow-sm">
                <CardHeader class="bg-slate-50 py-4">
                    <CardTitle class="text-sm font-bold flex items-center gap-2 text-slate-600">
                        <IconShoppingCart class="size-4" /> NOMOR TUJUAN
                    </CardTitle>
                </CardHeader>
                <CardContent class="p-6 space-y-4">
                    <div class="relative group">
                        <input
                            ref="tujuanInput"
                            v-model="form.nomor_tujuan"
                            type="text"
                            class="w-full text-center border-b-4 border-t-0 border-x-0 border-slate-200 focus:border-blue-600 focus:ring-0 font-mono font-bold py-4 text-2xl uppercase transition-all outline-none bg-transparent block"
                            placeholder="INPUT / PASTE"
                            @input="form.nomor_tujuan = form.nomor_tujuan.toUpperCase()"
                        />
                        <button
                            @click.stop="handlePaste"
                            class="absolute right-0 top-1/2 -translate-y-1/2 bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700 active:scale-95 transition-all shadow-md"
                            title="Klik untuk Paste dari Clipboard"
                        >
                            <IconClipboard class="size-5" />
                        </button>
                    </div>

                    <div v-if="form.errors.nomor_tujuan" class="p-2 bg-red-50 border border-red-200 rounded-lg flex items-center gap-2 text-red-600 animate-in fade-in zoom-in">
                        <IconAlertCircle class="size-4 shrink-0" />
                        <span class="text-[11px] font-bold uppercase leading-tight">{{ form.errors.nomor_tujuan }}</span>
                    </div>

                    <p class="text-[10px] text-slate-400 text-center font-bold uppercase tracking-widest leading-relaxed">
                        Silakan salin nomor dari Index, lalu klik tombol biru untuk menempel.
                    </p>
                </CardContent>
            </Card>

            <Card class="lg:col-span-7 border-2 border-blue-600 shadow-xl overflow-hidden">
                <CardContent class="p-0">
                    <div class="py-12 flex flex-col items-center justify-center space-y-6 relative">
                        <div v-if="!form.nomor_tujuan" class="absolute inset-0 z-30 bg-white/50 backdrop-blur-[1px] flex items-center justify-center">
                            <Badge variant="secondary" class="shadow-sm">Isi Nomor Tujuan Dahulu</Badge>
                        </div>

                        <div :class="['p-6 rounded-full transition-all duration-500', form.processing ? 'bg-blue-600 text-white scale-110' : 'bg-blue-50 text-blue-600']">
                            <IconScan size="64" :class="form.processing ? 'animate-pulse' : ''" />
                        </div>

                        <div class="w-full max-w-xs space-y-2 text-center">
                            <span class="text-[11px] font-black text-blue-600 uppercase tracking-[0.3em]">Scan Produk</span>
                            <input
                                ref="qrInput"
                                v-model="form.qr"
                                :disabled="!form.nomor_tujuan || form.processing"
                                type="text"
                                maxlength="10"
                                class="w-full text-center border-b-4 border-t-0 border-x-0 border-slate-100 focus:border-blue-600 focus:ring-0 outline-none text-4xl font-black py-2 bg-transparent uppercase"
                                placeholder="......"
                                @keyup.enter="handleTransfer"
                                @input="form.qr = form.qr.toUpperCase()"
                                autocomplete="off"
                            />

                            <p v-if="form.errors.qr" class="text-xs font-bold text-red-600 mt-2 animate-bounce">
                                {{ form.errors.qr }}
                            </p>
                        </div>
                    </div>

                    <div class="bg-slate-50 border-t p-3 flex justify-between items-center">
                        <div class="flex items-center gap-2 text-slate-500">
                            <IconHistory class="size-4" />
                            <span class="text-[10px] font-bold uppercase">Riwayat Pindah</span>
                        </div>
                    </div>
                    <div class="divide-y border-t bg-white max-h-40 overflow-y-auto">
                        <div v-for="(log, i) in scanHistory" :key="i" class="p-3 flex justify-between items-center text-xs animate-in slide-in-from-top-2">
                            <div class="flex items-center gap-2">
                                <span class="font-mono font-bold">{{ log.code }}</span>
                                <IconArrowsExchange class="size-3 text-slate-300" />
                                <span class="font-mono text-blue-600 font-bold">{{ log.target }}</span>
                            </div>
                            <Badge :variant="log.success ? 'outline' : 'destructive'" class="text-[9px] h-4">
                                {{ log.success ? 'BERHASIL' : 'GAGAL' }}
                            </Badge>
                        </div>
                        <div v-if="scanHistory.length === 0" class="p-6 text-center text-slate-300 italic text-xs">
                            Belum ada riwayat perpindahan.
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>

<style scoped>
input:focus {
    outline: none !important;
    box-shadow: none !important;
}
.overflow-y-auto::-webkit-scrollbar {
    width: 4px;
}
.overflow-y-auto::-webkit-scrollbar-thumb {
    background-color: #e2e8f0;
    border-radius: 20px;
}
</style>
