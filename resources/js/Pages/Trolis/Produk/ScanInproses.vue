<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, Head, Link } from "@inertiajs/vue3";
import { ref, onMounted, watch } from "vue"; // Tambahkan watch
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Checkbox } from "@/components/ui/checkbox";
import { toast } from "vue-sonner";
import {
    IconScan,
    IconLoader2,
    IconArrowLeft,
    IconCheck,
    IconAlertTriangle,
    IconClipboardCheck
} from "@tabler/icons-vue";

const props = defineProps<{
    troli: any;
    pilihan_cacat: Array<{ id: number; cacat: string }>;
}>();

// Key unik untuk Local Storage agar tidak bentrok antar troli
const STORAGE_KEY = `scan_cacat_ids_${props.troli.id}`;

const qrInput = ref<any>(null);

const form = useForm({
    qr: "",
    // Ambil data dari localStorage saat load, jika tidak ada default []
    cacat_ids: JSON.parse(localStorage.getItem(STORAGE_KEY) || "[]") as number[],
});

// Pantau perubahan cacat_ids dan simpan ke localStorage secara otomatis
watch(() => form.cacat_ids, (newVal) => {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(newVal));
}, { deep: true });

const focusInput = () => {
    qrInput.value?.$el?.querySelector('input')?.focus();
};

onMounted(() => {
    focusInput();
});

const toggleCacat = (id: number) => {
    const index = form.cacat_ids.indexOf(id);
    if (index > -1) {
        form.cacat_ids.splice(index, 1);
    } else {
        form.cacat_ids.push(id);
    }
};

// Fungsi reset manual untuk membersihkan pilihan dan storage
const clearSelection = () => {
    form.cacat_ids = [];
    localStorage.removeItem(STORAGE_KEY);
    toast.info("Pilihan cacat telah dibersihkan.");
};

const handleScan = () => {
    if (!form.qr) return;

    form.post(route('trolis.produk.scan_inproses_store', props.troli.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success("Berhasil!", {
                description: `Produk ${form.qr} berhasil diproses.`,
                duration: 2000,
            });

            // PENTING: Jangan pakai form.reset() karena akan menghapus cacat_ids.
            // Cukup kosongkan QR saja agar pilihan cacat tetap "lengket".
            form.qr = "";
            focusInput();
        },
        onError: (errors) => {
            const message = errors.qr || errors.error || "Terjadi kesalahan.";
            toast.error("Gagal", { description: message });
            form.reset('qr');
            focusInput();
        }
    });
};

defineOptions({ layout: AuthenticatedLayout });
</script>

<template>
    <Head title="Scan In-Proses" />

    <div class="flex flex-col items-center justify-center min-h-[80vh] p-4">

        <div class="w-full max-w-2xl mb-4">
            <Button variant="ghost" as-child class="group text-muted-foreground">
                <Link :href="route('trolis.produk.index', troli.id)">
                    <IconArrowLeft class="mr-2 size-4 transition-transform group-hover:-translate-x-1" />
                    Kembali
                </Link>
            </Button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 w-full max-w-4xl">

            <Card class="md:col-span-5 border-2 border-orange-500/20 shadow-xl overflow-hidden">
                <div class="h-2 bg-orange-500 w-full"></div>
                <CardHeader class="text-center">
                    <CardTitle class="text-xl font-bold text-orange-700 flex items-center justify-center gap-2">
                        <IconScan class="size-6" />
                        Scan QR Produk
                    </CardTitle>
                </CardHeader>

                <CardContent class="space-y-6 py-6">
                    <div class="flex justify-center">
                        <div class="p-4 bg-orange-50 rounded-full">
                            <IconScan :class="['size-16 text-orange-600', form.processing ? 'animate-pulse' : '']" />
                        </div>
                    </div>

                    <div class="space-y-4">
                        <Input
                            ref="qrInput"
                            v-model="form.qr"
                            :disabled="form.processing"
                            type="text"
                            class="w-full text-center border-b-4 border-t-0 border-x-0 border-orange-200 focus-visible:ring-0 focus-visible:border-orange-500 transition-all font-bold uppercase rounded-none bg-transparent"
                            style="font-size: 1.5rem; height: 60px;"
                            placeholder="TAP DISINI"
                            @keyup.enter="handleScan"
                            autocomplete="off"
                        />

                        <p class="text-[10px] text-center font-bold text-orange-400 uppercase tracking-widest">
                            In-Proses: {{ troli.invoice }}
                        </p>
                    </div>
                </CardContent>
            </Card>


            <Card class="md:col-span-7 border-2 border-slate-200 shadow-xl">
                <CardHeader class="bg-slate-50 border-b">
                    <CardTitle class="text-sm font-bold flex items-center gap-2">
                        <IconAlertTriangle class="size-4 text-red-500" />
                        LAPORAN CACAT (OPSIONAL)
                    </CardTitle>
                    <p class="text-xs text-muted-foreground italic">Pilihan tersimpan otomatis (F5 tidak akan hilang)</p>
                </CardHeader>

                <CardContent class="py-6">
                    <div v-if="pilihan_cacat.length > 0" class="flex flex-wrap gap-2 max-h-[400px] overflow-y-auto">
                        <button
                            v-for="item in pilihan_cacat"
                            :key="item.id"
                            type="button"
                            @click="toggleCacat(item.id)"
                            :class="[
                                'px-4 py-2 rounded-full text-xs font-semibold transition-all duration-200 border-2 flex items-center gap-2',
                                form.cacat_ids.includes(item.id)
                                    ? 'bg-red-500 border-red-600 text-white shadow-md transform scale-105'
                                    : 'bg-white border-slate-200 text-slate-600 hover:border-red-300 hover:bg-red-50'
                            ]"
                        >
                            <IconCheck v-if="form.cacat_ids.includes(item.id)" class="size-3" />
                            {{ item.cacat }}
                        </button>
                    </div>

                    <div v-else class="text-center py-10 text-muted-foreground">
                        <IconClipboardCheck class="size-10 mx-auto opacity-20 mb-2" />
                        <p class="text-xs">Tidak ada daftar jenis cacat.</p>
                    </div>
                </CardContent>

                <div class="p-4 bg-slate-50 border-t flex justify-between items-center">
                    <div class="flex flex-col">
                        <span class="text-[10px] uppercase tracking-wider font-bold text-slate-400 leading-none">Terpilih:</span>
                        <span class="text-sm font-bold text-red-600">
                            {{ form.cacat_ids.length }} Jenis Kerusakan
                        </span>
                    </div>
                    <Button
                        size="sm"
                        variant="ghost"
                        class="text-red-600 hover:bg-red-100 hover:text-red-700 font-bold"
                        @click="clearSelection"
                        :disabled="form.cacat_ids.length === 0"
                    >
                        Bersihkan Semua
                    </Button>
                </div>
            </Card>

        </div>

        <div v-if="form.processing" class="fixed inset-0 bg-white/60 backdrop-blur-sm z-50 flex items-center justify-center">
            <div class="flex flex-col items-center gap-2">
                <IconLoader2 class="size-10 animate-spin text-orange-600" />
                <span class="font-bold text-orange-900">Memproses Data...</span>
            </div>
        </div>
    </div>
</template>

<style scoped>
input:focus {
    outline: none !important;
    box-shadow: none !important;
}
/* Custom scrollbar untuk list cacat */
.overflow-y-auto::-webkit-scrollbar {
    width: 4px;
}
.overflow-y-auto::-webkit-scrollbar-thumb {
    background-color: #e2e8f0;
    border-radius: 20px;
}
</style>
