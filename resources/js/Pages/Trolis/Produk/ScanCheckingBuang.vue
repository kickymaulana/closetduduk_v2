<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, Head, Link } from "@inertiajs/vue3";
import { ref, onMounted, watch } from "vue";
import { Button } from "@/components/ui/button";
import { toast } from "vue-sonner";
import {
    IconScan, IconLoader2, IconArrowLeft, IconCheck,
    IconTrash, IconSettings, IconPalette, IconAlertCircle
} from "@tabler/icons-vue";

const props = defineProps<{
    troli: any;
    pilihan_cacat: Array<{ id: number; cacat: string }>;
    pilihan_kualitas: Array<{ id: number; kualitas: string }>;
    pilihan_warna: Array<{ id: number; warna: string }>;
}>();

const PREFIX = "scan_buang_ids_";
const STORAGE_KEY = `${PREFIX}${props.troli.id}`;
const nativeInput = ref<HTMLInputElement | null>(null);

const form = useForm({
    qr: "",
    cacat_ids: JSON.parse(localStorage.getItem(STORAGE_KEY) || "[]") as number[],
    kualitas_id: null as number | null,
    warna_id: null as number | null,
});

// Simpan pilihan cacat ke localStorage
watch(() => form.cacat_ids, (newVal) => {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(newVal));
}, { deep: true });

const focusInput = () => {
    setTimeout(() => nativeInput.value?.focus(), 50);
};

onMounted(() => focusInput());
watch(() => form.processing, (proc) => { if (!proc) focusInput(); });

const toggleCacat = (id: number) => {
    const idx = form.cacat_ids.indexOf(id);
    idx > -1 ? form.cacat_ids.splice(idx, 1) : form.cacat_ids.push(id);
    focusInput();
};

const handleScan = () => {
    if (!form.qr || form.processing) return;

    if (form.cacat_ids.length === 0) {
        toast.error("Wajib memilih minimal satu jenis cacat!");
        focusInput();
        return;
    }

    form.post(route('scan.checking.buang_store', props.troli.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success(`REJECT BERHASIL`, { description: `Produk ${form.qr} telah dibuang.` });
            form.qr = "";
        },
        onError: (err) => {
            toast.error(err.qr || err.error || "Gagal memproses.");
            form.reset('qr');
            focusInput();
        }
    });
};

defineOptions({ layout: AuthenticatedLayout });
</script>

<template>
    <Head title="Scan Buang (Reject)" />

    <div class="p-4 max-w-5xl mx-auto space-y-4" @click="focusInput">

        <!-- Header Navigasi -->
        <div class="flex items-center justify-between bg-white p-2 rounded-lg shadow-sm border border-red-100">
            <Link :href="route('trolis.produk.index', troli.id)" class="flex items-center text-xs font-bold text-slate-500">
                <IconArrowLeft class="size-4 mr-1" /> KEMBALI
            </Link>
            <div class="flex gap-1">
                <Button as-child variant="outline" size="sm" class="h-8 text-[10px] border-blue-200 text-blue-600">
                    <Link :href="route('trolis.produk.scan', troli.id)">MODE OK</Link>
                </Button>
                <Button as-child variant="outline" size="sm" class="h-8 text-[10px] border-orange-200 text-orange-600">
                    <Link :href="route('scan.checking.inproses', troli.id)">IN PROSES</Link>
                </Button>
                <Button size="sm" class="h-8 text-[10px] bg-red-600 hover:bg-red-700">BUANG</Button>
            </div>
        </div>

        <!-- Baris Atas: Input Scan Minimalis -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2 flex items-center bg-red-50 border-2 border-red-200 p-2 rounded-xl shadow-inner">
                <IconScan class="size-6 text-red-500 ml-2 mr-3" />
                <input
                    ref="nativeInput"
                    v-model="form.qr"
                    :disabled="form.processing"
                    type="text"
                    class="flex-1 bg-transparent border-none focus:ring-0 text-xl font-black uppercase tracking-widest text-red-700 placeholder:text-red-200"
                    placeholder="SCAN QR REJECT DISINI..."
                    @keyup.enter="handleScan"
                    @input="form.qr = form.qr.toUpperCase()"
                    @blur="focusInput"
                    autocomplete="off"
                />
            </div>
            <div class="bg-red-900 text-white p-3 rounded-xl flex flex-col justify-center items-center shadow-lg border-b-4 border-red-950">
                <span class="text-[9px] uppercase opacity-70 tracking-tighter font-bold">Mode Reject</span>
                <span class="font-black text-sm">{{ troli.invoice }}</span>
            </div>
        </div>

        <!-- Grid Utama -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <!-- Kolom Atribut -->
            <div class="space-y-4">
                <!-- Kualitas -->
                <div class="bg-white border rounded-xl overflow-hidden shadow-sm">
                    <div class="bg-slate-700 px-3 py-1.5 flex items-center gap-2">
                        <IconSettings class="size-3 text-white" />
                        <span class="text-[10px] font-bold text-white uppercase">Kualitas Produk</span>
                    </div>
                    <div class="p-3 flex flex-wrap gap-2">
                        <button
                            v-for="k in pilihan_kualitas" :key="k.id"
                            type="button"
                            @click="form.kualitas_id = (form.kualitas_id === k.id ? null : k.id); focusInput()"
                            :class="['px-4 py-2 rounded-lg text-xs font-bold border-2 transition-all',
                                     form.kualitas_id === k.id ? 'bg-slate-800 border-slate-900 text-white' : 'bg-slate-50 border-slate-100 text-slate-500']"
                        >
                            {{ k.kualitas }}
                        </button>
                    </div>
                </div>

                <!-- Warna -->
                <div class="bg-white border rounded-xl overflow-hidden shadow-sm">
                    <div class="bg-slate-700 px-3 py-1.5 flex items-center gap-2">
                        <IconPalette class="size-3 text-white" />
                        <span class="text-[10px] font-bold text-white uppercase">Warna Produk</span>
                    </div>
                    <div class="p-3 flex flex-wrap gap-2">
                        <button
                            v-for="w in pilihan_warna" :key="w.id"
                            type="button"
                            @click="form.warna_id = (form.warna_id === w.id ? null : w.id); focusInput()"
                            :class="['px-4 py-2 rounded-lg text-xs font-bold border-2 transition-all',
                                     form.warna_id === w.id ? 'bg-slate-800 border-slate-900 text-white' : 'bg-slate-50 border-slate-100 text-slate-500']"
                        >
                            {{ w.warna }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Kolom Alasan Cacat (Wajib) -->
            <div class="bg-white border-2 border-red-100 rounded-xl overflow-hidden shadow-sm flex flex-col">
                <div class="bg-red-600 px-3 py-1.5 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <IconAlertCircle class="size-3 text-white" />
                        <span class="text-[10px] font-bold text-white uppercase tracking-wider">Alasan Reject (Wajib)</span>
                    </div>
                    <button type="button" @click="form.cacat_ids = []; focusInput()" class="text-[9px] text-white underline font-bold">RESET</button>
                </div>
                <div class="p-3 flex-1 bg-red-50/30">
                    <div v-if="pilihan_cacat.length" class="flex flex-wrap gap-2 overflow-y-auto max-h-[300px] p-1">
                        <button
                            v-for="c in pilihan_cacat" :key="c.id"
                            type="button"
                            @click="toggleCacat(c.id)"
                            :class="['px-3 py-2 rounded-md text-[11px] font-bold border-2 transition-all flex items-center gap-1',
                                     form.cacat_ids.includes(c.id) ? 'bg-red-600 border-red-700 text-white scale-105' : 'bg-white border-red-100 text-red-600 shadow-sm']"
                        >
                            <IconCheck v-if="form.cacat_ids.includes(c.id)" class="size-3" />
                            {{ c.cacat }}
                        </button>
                    </div>
                    <div v-else class="h-full flex flex-col items-center justify-center text-red-300 italic text-xs py-10">
                        <IconTrash class="size-8 mb-2 opacity-20" />
                        Daftar alasan tidak tersedia
                    </div>
                </div>
                <div class="bg-red-100 p-2 border-t border-red-200 text-center">
                    <span class="text-[10px] font-black text-red-700 uppercase tracking-widest">
                        {{ form.cacat_ids.length }} Alasan Terpilih
                    </span>
                </div>
            </div>
        </div>

        <!-- Overlay Loading -->
        <div v-if="form.processing" class="fixed inset-0 bg-red-950/40 backdrop-blur-sm z-50 flex items-center justify-center">
            <div class="bg-white p-5 rounded-2xl shadow-2xl flex flex-col items-center border-t-4 border-red-600">
                <IconLoader2 class="size-8 animate-spin text-red-600 mb-2" />
                <span class="text-xs font-black uppercase tracking-widest text-red-900">Sedang Reject...</span>
            </div>
        </div>
    </div>
</template>

<style scoped>
input:focus {
    outline: none !important;
}
.overflow-y-auto::-webkit-scrollbar {
    width: 4px;
}
.overflow-y-auto::-webkit-scrollbar-thumb {
    background-color: #fecaca;
    border-radius: 10px;
}
</style>
