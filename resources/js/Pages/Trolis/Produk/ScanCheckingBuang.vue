<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, Head, Link } from "@inertiajs/vue3";
import { ref, onMounted, watch } from "vue";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { toast } from "vue-sonner";
import {
    IconScan,
    IconLoader2,
    IconArrowLeft,
    IconCheck,
    IconTrash,
    IconClipboardCheck
} from "@tabler/icons-vue";

const props = defineProps<{
    troli: any;
    pilihan_cacat: Array<{ id: number; cacat: string }>;
}>();

const PREFIX = "scan_buang_ids_";
const STORAGE_KEY = `${PREFIX}${props.troli.id}`;

// Ref untuk Input Native
const nativeInput = ref<HTMLInputElement | null>(null);

const cleanupOldStorage = () => {
    try {
        const keys = Object.keys(localStorage);
        keys.forEach(key => {
            if (key.startsWith(PREFIX) && key !== STORAGE_KEY) {
                localStorage.removeItem(key);
            }
        });
    } catch (e) { console.error(e); }
};

const form = useForm({
    qr: "",
    cacat_ids: JSON.parse(localStorage.getItem(STORAGE_KEY) || "[]") as number[],
});

// Simpan pilihan cacat ke localStorage
watch(() => form.cacat_ids, (newVal) => {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(newVal));
}, { deep: true });

/**
 * FUNGSI FOKUS NATIVE
 */
const focusInput = () => {
    setTimeout(() => {
        if (nativeInput.value) {
            nativeInput.value.focus();
        }
    }, 50);
};

/**
 * WATCHDOG FOKUS
 * Mengembalikan kursor setelah proses simpan (berhasil/gagal) selesai
 */
watch(() => form.processing, (isProcessing) => {
    if (!isProcessing) {
        focusInput();
    }
});

onMounted(() => {
    cleanupOldStorage();
    focusInput();
});

const toggleCacat = (id: number) => {
    const index = form.cacat_ids.indexOf(id);
    if (index > -1) {
        form.cacat_ids.splice(index, 1);
    } else {
        form.cacat_ids.push(id);
    }
    // Paksa kursor balik ke input setelah pilih cacat
    focusInput();
};

const clearSelection = () => {
    form.cacat_ids = [];
    localStorage.removeItem(STORAGE_KEY);
    toast.info("Pilihan cacat dibersihkan.");
    focusInput();
};

const handleScan = () => {
    if (!form.qr || form.processing) return;

    if (form.cacat_ids.length === 0) {
        toast.error("Wajib pilih alasan cacat!");
        focusInput();
        return;
    }

    form.post(route('scan.checking.buang_store', props.troli.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success("REJECT BERHASIL", {
                description: `Produk ${form.qr} telah dibuang.`,
                duration: 2000,
            });
            form.qr = "";
            // Fokus otomatis ditangani oleh watcher processing
        },
        onError: (errors) => {
            const message = errors.qr || errors.error || errors.cacat_ids || "Terjadi kesalahan.";
            toast.error("Gagal", { description: message });
            form.reset('qr');
            focusInput();
        }
    });
};

defineOptions({ layout: AuthenticatedLayout });
</script>

<template>
    <Head title="Scan Buang (Reject)" />

    <div class="flex flex-col items-center justify-center min-h-[80vh] p-4" @click="focusInput">

        <div class="w-full max-w-4xl grid grid-cols-3 gap-2 mb-6">
            <Button as-child variant="outline" class="text-blue-600 border-blue-200 hover:bg-blue-50">
                <Link :href="route('trolis.produk.scan', troli.id)">MODE OK</Link>
            </Button>
            <Button as-child variant="outline" class="text-orange-600 border-orange-200 hover:bg-orange-50">
                <Link :href="route('trolis.produk.scan_inproses', troli.id)">IN PROSES</Link>
            </Button>
            <Button as-child variant="default" class="bg-red-600 hover:bg-red-700 shadow-lg border-b-4 border-red-800">
                <Link :href="route('trolis.produk.scan_buang', troli.id)">BUANG</Link>
            </Button>
        </div>

        <div class="w-full max-w-2xl mb-4 text-left">
            <Button variant="ghost" as-child class="group text-muted-foreground hover:text-red-600">
                <Link :href="route('trolis.produk.index', troli.id)">
                    <IconArrowLeft class="mr-2 size-4 transition-transform group-hover:-translate-x-1" />
                    Kembali
                </Link>
            </Button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 w-full max-w-4xl">

            <Card class="md:col-span-5 border-2 border-red-500 shadow-xl overflow-hidden">
                <div class="h-2 bg-red-600 w-full"></div>
                <CardHeader class="text-center">
                    <CardTitle class="text-xl font-bold text-red-700 flex items-center justify-center gap-2">
                        <IconTrash class="size-6" />
                        SCAN REJECT
                    </CardTitle>
                </CardHeader>

                <CardContent class="space-y-6 py-6">
                    <div class="flex justify-center">
                        <div class="p-4 bg-red-50 rounded-full">
                            <IconScan :class="['size-16 text-red-600', form.processing ? 'animate-pulse' : '']" />
                        </div>
                    </div>

                    <div class="space-y-4">
                        <input
                            ref="nativeInput"
                            v-model="form.qr"
                            :disabled="form.processing"
                            type="text"
                            maxlength="10"
                            class="w-full text-center border-b-4 border-t-0 border-x-0 border-red-200 focus:ring-0 focus:border-red-600 transition-all font-bold uppercase rounded-none bg-transparent block outline-none"
                            style="font-size: 1.5rem; height: 60px; color: #b91c1c;"
                            placeholder="TAP DISINI"
                            @keyup.enter="handleScan"
                            @input="form.qr = form.qr.toUpperCase()"
                            @blur="focusInput"
                            autocomplete="off"
                        />

                        <p v-if="form.errors.qr" class="text-[11px] text-center font-bold text-red-600 animate-bounce">
                            {{ form.errors.qr }}
                        </p>

                        <p class="text-[10px] text-center font-bold text-red-400 uppercase tracking-widest">
                            MODE BUANG: {{ troli.invoice }}
                        </p>
                    </div>
                </CardContent>
            </Card>

            <Card class="md:col-span-7 border-2 border-red-100 shadow-xl">
                <CardHeader class="bg-red-50 border-b">
                    <CardTitle class="text-sm font-bold flex items-center gap-2 text-red-800">
                        ALASAN PRODUK DIBUANG
                    </CardTitle>
                    <p class="text-xs text-red-600/70 font-medium italic">Wajib pilih alasan sebelum scan!</p>
                </CardHeader>

                <CardContent class="py-6">
                    <div v-if="pilihan_cacat.length > 0" class="flex flex-wrap gap-2 max-h-[300px] overflow-y-auto">
                        <button
                            v-for="item in pilihan_cacat"
                            :key="item.id"
                            type="button"
                            @click="toggleCacat(item.id)"
                            :class="[
                                'px-4 py-2 rounded-full text-xs font-bold transition-all duration-200 border-2 flex items-center gap-2',
                                form.cacat_ids.includes(item.id)
                                    ? 'bg-red-600 border-red-700 text-white shadow-md transform scale-105'
                                    : 'bg-white border-red-100 text-red-600 hover:bg-red-50'
                            ]"
                        >
                            <IconCheck v-if="form.cacat_ids.includes(item.id)" class="size-3" />
                            {{ item.cacat }}
                        </button>
                    </div>

                    <div v-else class="text-center py-10 text-muted-foreground">
                        <IconClipboardCheck class="size-10 mx-auto opacity-20 mb-2" />
                        <p class="text-xs">Daftar cacat tidak tersedia.</p>
                    </div>
                </CardContent>

                <div class="p-4 bg-red-50/50 border-t flex justify-between items-center">
                    <div class="flex flex-col">
                        <span class="text-sm font-black text-red-700">
                            {{ form.cacat_ids.length }} Alasan Terpilih
                        </span>
                    </div>
                    <Button
                        size="sm"
                        variant="ghost"
                        class="text-red-700 hover:bg-red-200 font-bold"
                        @click="clearSelection"
                        :disabled="form.cacat_ids.length === 0"
                    >
                        Reset
                    </Button>
                </div>
            </Card>

        </div>

        <div v-if="form.processing" class="fixed inset-0 bg-red-900/20 backdrop-blur-sm z-50 flex items-center justify-center">
            <div class="flex flex-col items-center gap-2 bg-white p-6 rounded-lg shadow-2xl border-2 border-red-600">
                <IconLoader2 class="size-10 animate-spin text-red-600" />
                <span class="font-bold text-red-900">Menghapus Produk...</span>
            </div>
        </div>
    </div>
</template>

<style scoped>
input:focus {
    outline: none !important;
    box-shadow: none !important;
}
.overflow-y-auto::-webkit-scrollbar { width: 4px; }
.overflow-y-auto::-webkit-scrollbar-thumb { background-color: #fecaca; border-radius: 20px; }
</style>

