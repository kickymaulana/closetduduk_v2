<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, Head, Link } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";
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

const qrInput = ref<any>(null);

const form = useForm({
    qr: "",
    cacat_ids: [] as number[], // Menyimpan ID cacat yang dipilih
});

const focusInput = () => {
    qrInput.value?.$el?.querySelector('input')?.focus();
};

onMounted(() => {
    focusInput();
});

// Fungsi toggle checkbox manual jika diperlukan
const toggleCacat = (id: number) => {
    const index = form.cacat_ids.indexOf(id);
    if (index > -1) {
        form.cacat_ids.splice(index, 1);
    } else {
        form.cacat_ids.push(id);
    }
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
            form.reset(); // Reset QR dan Checkbox
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
                    <p class="text-xs text-muted-foreground">Pilih jenis cacat jika ditemukan kendala pada produk</p>
                </CardHeader>
                <CardContent class="py-4">
                    <div v-if="pilihan_cacat.length > 0" class="grid grid-cols-1 gap-3 max-h-[300px] overflow-y-auto pr-2">
                        <div
                            v-for="item in pilihan_cacat"
                            :key="item.id"
                            class="flex items-center space-x-3 space-y-0 rounded-md border p-3 shadow-sm hover:bg-slate-50 transition-colors cursor-pointer"
                            @click="toggleCacat(item.id)"
                        >
                            <Checkbox
                                :id="'cacat-' + item.id"
                                :checked="form.cacat_ids.includes(item.id)"
                                @update:checked="toggleCacat(item.id)"
                            />
                            <div class="grid gap-1.5 leading-none">
                                <Label
                                    :for="'cacat-' + item.id"
                                    class="text-sm font-medium leading-none cursor-pointer"
                                >
                                    {{ item.cacat }}
                                </Label>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-10 text-muted-foreground">
                        <IconClipboardCheck class="size-10 mx-auto opacity-20 mb-2" />
                        <p class="text-xs">Tidak ada daftar jenis cacat untuk proses ini.</p>
                    </div>
                </CardContent>
                <div class="p-4 bg-slate-50 border-t flex justify-between items-center">
                    <span class="text-xs font-medium text-slate-500">
                        {{ form.cacat_ids.length }} Cacat dipilih
                    </span>
                    <Button
                        size="sm"
                        variant="outline"
                        @click="form.reset('cacat_ids')"
                        :disabled="form.cacat_ids.length === 0"
                    >
                        Reset Pilihan
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
