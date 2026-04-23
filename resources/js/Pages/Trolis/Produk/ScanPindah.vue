<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, Head, Link } from "@inertiajs/vue3";
import { ref, onMounted, nextTick, watch } from "vue";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { toast } from "vue-sonner";
import {
    IconScan,
    IconLoader2,
    IconArrowLeft,
    IconArrowsExchange,
    IconShoppingCart,
    IconLayoutGrid,
    IconCircleCheck,
    IconBoxSeam,
    IconHistory,
    IconAlertCircle
} from "@tabler/icons-vue";

// Props yang diterima dari Controller
const props = defineProps<{
    troli: any;        // Troli ASAL
    daftarTroli: any[]; // Daftar troli selain asal
}>();

const qrInput = ref<HTMLInputElement | null>(null);
const scanHistory = ref<Array<{ code: string; time: string; success: boolean }>>([]);

const form = useForm({
    qr: "",
    troli_tujuan_id: "", // Dipilih secara manual oleh user di sidebar
});

/**
 * FUNGSI FOKUS UTAMA
 */
const focusInput = () => {
    setTimeout(() => {
        if (qrInput.value) {
            qrInput.value.focus();
        }
    }, 50);
};

onMounted(() => {
    focusInput();
});

/**
 * WATCHDOG FOKUS
 * Mengembalikan kursor saat proses selesai atau saat troli tujuan diganti
 */
watch(() => form.processing, (isProcessing) => {
    if (!isProcessing) focusInput();
});

watch(() => form.troli_tujuan_id, () => {
    focusInput();
});

const handleTransfer = () => {
    if (!form.troli_tujuan_id) {
        toast.error("Pilih Troli Tujuan", {
            description: "Silahkan pilih troli target perpindahan barang terlebih dahulu."
        });
        return;
    }

    if (!form.qr || form.processing) return;

    const currentQr = form.qr.toUpperCase();

    form.post(route('trolis.produk.scan_pindah_store', props.troli.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success("Berhasil Dipindahkan", {
                description: `Produk ${currentQr} berhasil dipindahkan ke tujuan.`
            });

            addToHistory(currentQr, true);
            form.reset('qr');
            // Fokus otomatis ditangani oleh watcher
        },
        onError: (errors) => {
            toast.error("Gagal Pindah", {
                description: errors.qr || "Terjadi kesalahan saat memproses data."
            });

            addToHistory(currentQr, false);
            form.reset('qr');
            focusInput();
        }
    });
};

const addToHistory = (code: string, success: boolean) => {
    const now = new Date();
    const timeString = now.getHours().toString().padStart(2, '0') + ":" +
                       now.getMinutes().toString().padStart(2, '0') + ":" +
                       now.getSeconds().toString().padStart(2, '0');

    scanHistory.value.unshift({ code, time: timeString, success });
    if (scanHistory.value.length > 5) scanHistory.value.pop();
};

const refocus = () => {
    if (!form.processing) focusInput();
};

defineOptions({ layout: AuthenticatedLayout });
</script>

<template>
    <Head title="Pindah Isi Troli" />

    <div class="max-w-6xl mx-auto p-4 md:p-6 space-y-6" @click="refocus">

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b pb-4">
            <div class="flex items-center gap-3">
                <Button variant="outline" size="icon" as-child>
                    <Link :href="route('trolis.produk.index', troli.id)">
                        <IconArrowLeft class="size-5" />
                    </Link>
                </Button>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-800">Transfer Antar Troli</h1>
                    <div class="flex items-center gap-2 mt-1">
                        <Badge variant="destructive" class="px-1.5 py-0 text-[10px]">SUMBER</Badge>
                        <span class="text-sm font-mono font-semibold text-slate-600">{{ troli.invoice }}</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 bg-blue-50 px-4 py-2 rounded-full border border-blue-100">
                <IconArrowsExchange class="text-blue-600 size-5" />
                <span class="text-xs font-bold text-blue-800 uppercase tracking-widest">Scanner Ready</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <div class="lg:col-span-4 space-y-4">
                <Card class="border-slate-200 shadow-sm">
                    <CardHeader class="py-4 bg-slate-50/50 border-b">
                        <CardTitle class="text-sm font-bold flex items-center gap-2">
                            <IconLayoutGrid class="size-4 text-slate-500" />
                            Pilih Troli Tujuan
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="p-3">
                        <div class="space-y-2 max-h-[400px] overflow-y-auto pr-1">
                            <button
                                v-for="t in daftarTroli"
                                :key="t.id"
                                @click="form.troli_tujuan_id = t.id"
                                :class="[
                                    'w-full text-left p-3 rounded-xl border-2 transition-all flex justify-between items-center group',
                                    form.troli_tujuan_id === t.id
                                        ? 'border-blue-600 bg-blue-50 ring-4 ring-blue-50'
                                        : 'border-slate-100 hover:border-slate-300 bg-white'
                                ]"
                            >
                                <div class="flex items-center gap-3">
                                    <div :class="[
                                        'p-2 rounded-lg transition-colors',
                                        form.troli_tujuan_id === t.id ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-400 group-hover:bg-slate-200'
                                    ]">
                                        <IconShoppingCart class="size-4" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold font-mono">{{ t.invoice }}</p>
                                        <p class="text-[10px] text-slate-400 uppercase tracking-tighter">Keperluan {{ t.keperluan }}</p>
                                    </div>
                                </div>
                                <IconCircleCheck v-if="form.troli_tujuan_id === t.id" class="size-5 text-blue-600" />
                            </button>

                            <div v-if="daftarTroli.length === 0" class="py-8 text-center">
                                <IconAlertCircle class="size-8 mx-auto text-slate-300 mb-2" />
                                <p class="text-xs text-slate-400 font-medium">Tidak ada troli tujuan tersedia.</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <div v-if="form.troli_tujuan_id" class="p-4 bg-green-600 text-white rounded-2xl shadow-lg shadow-green-100 flex items-center gap-4 transition-all animate-in zoom-in-95">
                    <div class="bg-white/20 p-2 rounded-lg">
                        <IconArrowsExchange class="size-6" />
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase opacity-80">Tujuan Terkunci:</p>
                        <p class="font-bold tracking-tight">
                            {{ daftarTroli.find(t => t.id === form.troli_tujuan_id)?.invoice }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-8 space-y-6">
                <Card :class="[
                    'overflow-hidden transition-all duration-500 border-2',
                    form.troli_tujuan_id ? 'border-blue-600 shadow-xl' : 'border-slate-200 opacity-60 grayscale-[0.5]'
                ]">
                    <CardContent class="p-0">
                        <div class="flex flex-col items-center justify-center py-12 px-6 relative">

                            <div v-if="!form.troli_tujuan_id" class="absolute inset-0 z-30 bg-white/40 backdrop-blur-[1px] flex items-center justify-center">
                                <Badge variant="secondary" class="text-sm px-4 py-1 shadow-sm border border-slate-200">
                                    Pilih tujuan di samping untuk memulai
                                </Badge>
                            </div>

                            <div class="mb-8">
                                <div :class="[
                                    'p-8 rounded-full transition-all duration-700 relative',
                                    form.processing ? 'bg-blue-600 text-white scale-110' : 'bg-blue-50 text-blue-600'
                                ]">
                                    <IconScan size="72" stroke-width="1.5" :class="form.processing ? 'animate-pulse' : ''" />
                                    <div v-if="form.processing" class="absolute inset-0 border-4 border-blue-600 rounded-full animate-ping opacity-20"></div>
                                </div>
                            </div>

                            <div class="w-full max-w-sm space-y-6 text-center">
                                <div class="space-y-1">
                                    <span class="text-[11px] font-black text-blue-600 uppercase tracking-[0.4em]">Barcode Scanner</span>
                                    <input
                                        ref="qrInput"
                                        v-model="form.qr"
                                        :disabled="!form.troli_tujuan_id || form.processing"
                                        type="text"
                                        maxlength="10"
                                        class="w-full text-center border-b-4 border-t-0 border-x-0 border-slate-100 focus:border-blue-600 focus:ring-0 outline-none text-5xl font-black py-4 bg-transparent transition-all placeholder:text-slate-100 uppercase"
                                        placeholder="......"
                                        @keyup.enter="handleTransfer"
                                        @input="form.qr = form.qr.toUpperCase()"
                                        @blur="focusInput"
                                        autocomplete="off"
                                    >
                                </div>

                                <div class="flex flex-col items-center gap-3">
                                    <div v-if="form.processing" class="flex items-center gap-2 text-blue-600 font-bold italic">
                                        <IconLoader2 class="animate-spin size-5" />
                                        <span>Memproses perpindahan barang...</span>
                                    </div>
                                    <div v-else class="flex items-center gap-2 text-slate-400">
                                        <IconBoxSeam class="size-4" />
                                        <span class="text-xs font-medium uppercase tracking-tight">Tekan ENTER setelah scan</span>
                                    </div>
                                </div>

                                <p v-if="form.errors.qr" class="text-xs font-bold text-red-600 animate-bounce">
                                    {{ form.errors.qr }}
                                </p>
                            </div>
                        </div>

                        <div class="bg-slate-50 border-t p-4 flex justify-between items-center">
                            <div class="flex items-center gap-2 text-slate-500">
                                <IconHistory class="size-4" />
                                <span class="text-xs font-bold uppercase tracking-tighter">Riwayat Sesi Ini</span>
                            </div>
                        </div>

                        <div class="divide-y border-t">
                            <div v-for="(log, i) in scanHistory" :key="i" class="p-3 flex items-center justify-between bg-white animate-in slide-in-from-top-2">
                                <div class="flex items-center gap-3">
                                    <div :class="['size-2 rounded-full', log.success ? 'bg-green-500' : 'bg-red-500']"></div>
                                    <span class="font-mono text-sm font-bold">{{ log.code }}</span>
                                </div>
                                <div class="flex items-center gap-4">
                                    <Badge :variant="log.success ? 'outline' : 'destructive'" class="text-[9px] h-5">
                                        {{ log.success ? 'SUKSES' : 'GAGAL' }}
                                    </Badge>
                                    <span class="text-[10px] font-mono text-slate-400">{{ log.time }}</span>
                                </div>
                            </div>
                            <div v-if="scanHistory.length === 0" class="p-8 text-center text-slate-300 italic text-sm">
                                Belum ada aktivitas pemindahan.
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

        </div>
    </div>
</template>

<style scoped>
input:focus {
    box-shadow: none !important;
}

.overflow-y-auto::-webkit-scrollbar {
  width: 4px;
}
.overflow-y-auto::-webkit-scrollbar-track {
  background: transparent;
}
.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 10px;
}
</style>
