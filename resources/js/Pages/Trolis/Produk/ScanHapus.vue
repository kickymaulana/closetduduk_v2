<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, Head, Link } from "@inertiajs/vue3";
import { ref, onMounted, nextTick } from "vue";
import { Card, CardContent } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { toast } from "vue-sonner";
import {
    IconScan,
    IconLoader2,
    IconArrowLeft,
    IconTrash,
    IconHistory,
    IconAlertTriangle,
    IconTrashX
} from "@tabler/icons-vue";

const props = defineProps<{
    troli: any;
}>();

const qrInput = ref<HTMLInputElement | null>(null);
const scanHistory = ref<Array<{ code: string; time: string; success: boolean; msg?: string }>>([]);

const form = useForm({
    qr: "",
});

onMounted(() => {
    qrInput.value?.focus();
});

const handleRemove = () => {
    if (!form.qr || form.processing) return;

    form.post(route('trolis.produk.scan_hapus_store', props.troli.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success("Berhasil Dihapus", {
                description: `Produk ${form.qr} telah dikeluarkan dari troli.`
            });
            addToHistory(form.qr, true);
            form.reset('qr');
            nextTick(() => qrInput.value?.focus());
        },
        onError: (errors) => {
            const errorMsg = errors.qr || "Gagal menghapus produk.";
            toast.error("Gagal Hapus", { description: errorMsg });
            addToHistory(form.qr, false, errorMsg);
            form.reset('qr');
            nextTick(() => qrInput.value?.focus());
        }
    });
};

const addToHistory = (code: string, success: boolean, msg?: string) => {
    const now = new Date();
    const timeString = now.toLocaleTimeString('id-ID');
    scanHistory.value.unshift({ code, time: timeString, success, msg });
    if (scanHistory.value.length > 8) scanHistory.value.pop();
};

const refocus = () => {
    if (!form.processing) qrInput.value?.focus();
};

defineOptions({ layout: AuthenticatedLayout });
</script>

<template>
    <Head title="Hapus Produk dari Troli" />

    <div class="max-w-4xl mx-auto p-4 md:p-6 space-y-6" @click="refocus">

        <div class="flex items-center justify-between border-b pb-4">
            <div class="flex items-center gap-3">
                <Button variant="outline" size="icon" as-child>
                    <Link :href="route('trolis.produk.index', troli.id)">
                        <IconArrowLeft class="size-5" />
                    </Link>
                </Button>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-red-600">Scan Hapus Produk</h1>
                    <p class="text-xs text-slate-500 font-mono italic">Troli: {{ troli.invoice }}</p>
                </div>
            </div>
            <Badge variant="outline" class="bg-red-50 text-red-700 border-red-200 gap-1 px-3 py-1">
                <IconAlertTriangle class="size-3" /> Syarat: Status Buang
            </Badge>
        </div>

        <div class="grid grid-cols-1 gap-6">
            <Card :class="['border-2 transition-all duration-300', form.processing ? 'border-red-500 shadow-lg' : 'border-slate-200']">
                <CardContent class="p-0">
                    <div class="flex flex-col items-center justify-center py-16 px-6">

                        <div class="mb-8">
                            <div :class="['p-8 rounded-full transition-all duration-500', form.processing ? 'bg-red-600 text-white scale-110' : 'bg-red-50 text-red-600']">
                                <IconScan size="72" stroke-width="1.5" :class="form.processing ? 'animate-pulse' : ''" />
                            </div>
                        </div>

                        <div class="w-full max-w-md text-center space-y-6">
                            <div class="space-y-1">
                                <span class="text-[11px] font-black text-red-600 uppercase tracking-[0.4em]">Ready to Remove</span>
                                <input
                                    ref="qrInput"
                                    v-model="form.qr"
                                    :disabled="form.processing"
                                    type="text"
                                    class="w-full text-center border-b-4 border-t-0 border-x-0 border-slate-100 focus:border-red-600 focus:ring-0 outline-none text-5xl font-black py-4 bg-transparent transition-all placeholder:text-slate-100"
                                    placeholder="......"
                                    @keyup.enter="handleRemove"
                                    autocomplete="off"
                                >
                            </div>

                            <div v-if="form.processing" class="flex items-center justify-center gap-2 text-red-600 font-bold italic">
                                <IconLoader2 class="animate-spin size-5" />
                                <span>Memvalidasi & Menghapus...</span>
                            </div>
                            <p v-else class="text-xs text-slate-400 uppercase font-bold tracking-widest flex items-center justify-center gap-2">
                                <IconTrashX class="size-4" /> Scan barcode lalu tekan ENTER
                            </p>
                        </div>
                    </div>

                    <div class="bg-slate-50 border-t p-4 flex items-center gap-2 text-slate-500">
                        <IconHistory class="size-4" />
                        <span class="text-xs font-bold uppercase">Riwayat Penghapusan</span>
                    </div>

                    <div class="divide-y border-t max-h-64 overflow-y-auto">
                        <div v-for="(log, i) in scanHistory" :key="i" class="p-4 flex items-center justify-between bg-white">
                            <div class="flex flex-col">
                                <div class="flex items-center gap-2">
                                    <div :class="['size-2 rounded-full', log.success ? 'bg-green-500' : 'bg-red-500']"></div>
                                    <span class="font-mono text-sm font-bold">{{ log.code }}</span>
                                </div>
                                <span v-if="!log.success" class="text-[10px] text-red-500 font-medium ml-4 mt-0.5">{{ log.msg }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <Badge :variant="log.success ? 'secondary' : 'destructive'" class="text-[9px]">
                                    {{ log.success ? 'BERHASIL' : 'GAGAL' }}
                                </Badge>
                                <span class="text-[10px] font-mono text-slate-400">{{ log.time }}</span>
                            </div>
                        </div>
                        <div v-if="scanHistory.length === 0" class="p-8 text-center text-slate-300 italic text-sm">
                            Belum ada produk yang discan untuk dihapus.
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>

<style scoped>
input:focus { box-shadow: none !important; }
</style>
