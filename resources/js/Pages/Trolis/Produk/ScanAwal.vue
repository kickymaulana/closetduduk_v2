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
} from "@tabler/icons-vue";

const props = defineProps<{
    troli: any;
}>();

const qrInput = ref<HTMLInputElement | null>(null);

// Form dengan data default
const form = useForm({
    qr: "",
    nomor_mesin: "Mesin 01",
    nomor_mould: "Mould 1",
    asal_slip: "SS1",
});

// Pilihan data
const listMesin = ["Mesin 01", "Mesin 02"];
const listMould = Array.from({ length: 12 }, (_, i) => `Mould ${i + 1}`);
const listSlip = [
    { label: "SS1 (Standard Slip 1)", value: "SS1" },
    { label: "SS2 (Standard Slip 2)", value: "SS2" },
];

// Fungsi untuk kembalikan kursor ke input scan
const focusScanner = () => {
    qrInput.value?.focus();
};

onMounted(() => {
    focusScanner();
});

// Otomatis arahkan kursor ke input scan setelah berhasil/gagal menyimpan
watch(
    () => form.processing,
    (isProcessing) => {
        if (!isProcessing) {
            focusScanner();
        }
    },
);

const scan = () => {
    if (!form.qr || form.processing) return;

    form.post(route("trolis.produk.scan_awal_store", props.troli.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success("Berhasil!", {
                description: `Produk ${form.qr} dicatat.`,
            });
            form.reset("qr"); // Reset HANYA kolom QR agar dropdown tidak berubah
        },
        onError: (errors) => {
            const message =
                errors.qr || errors.error || "Gagal menyimpan data.";
            toast.error("Gagal Scan", { description: message });
            form.reset("qr");
        },
    });
};

defineOptions({ layout: AuthenticatedLayout });
</script>

<template>
    <Head title="Scan Masuk Produk" />

    <div class="flex flex-col items-center justify-center min-h-[80vh] p-4">
        <div class="w-full max-w-md mb-4">
            <Button
                variant="ghost"
                as-child
                class="group text-muted-foreground hover:text-primary"
            >
                <Link :href="route('trolis.produk.index', troli.id)">
                    <IconArrowLeft class="mr-2 size-4" />
                    Batal & Kembali
                </Link>
            </Button>
        </div>

        <Card
            class="w-full max-w-md border-2 border-primary/20 shadow-xl overflow-hidden"
        >
            <div class="h-2 bg-primary w-full"></div>
            <CardHeader class="text-center">
                <CardTitle
                    class="text-2xl font-bold text-primary flex items-center justify-center gap-2"
                >
                    <IconScan class="size-6" />
                    Scan Produk
                </CardTitle>
                <p
                    class="text-muted-foreground font-mono text-sm tracking-widest bg-muted py-1 rounded-md mt-2"
                >
                    INV: {{ troli.invoice }}
                </p>
            </CardHeader>

            <CardContent class="space-y-6 py-6">
                <div
                    class="grid grid-cols-1 gap-4 p-4 bg-slate-50 rounded-lg border border-slate-200"
                >
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label
                                class="text-[10px] font-bold text-slate-500 uppercase"
                                >Mesin</label
                            >
                            <select
                                v-model="form.nomor_mesin"
                                class="w-full rounded-md border-slate-300 text-sm dark:bg-slate-800 dark:text-white"
                            >
                                <option
                                    v-for="m in listMesin"
                                    :key="m"
                                    :value="m"
                                >
                                    {{ m }}
                                </option>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label
                                class="text-[10px] font-bold text-slate-500 uppercase"
                                >Mould</label
                            >
                            <select
                                v-model="form.nomor_mould"
                                class="w-full rounded-md border-slate-300 text-sm dark:bg-slate-800 dark:text-white"
                            >
                                <option
                                    v-for="md in listMould"
                                    :key="md"
                                    :value="md"
                                >
                                    {{ md }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label
                            class="text-[10px] font-bold text-slate-500 uppercase"
                            >Asal Slip</label
                        >
                        <select
                            v-model="form.asal_slip"
                            class="w-full rounded-md border-slate-300 text-sm dark:bg-slate-800 dark:text-white"
                        >
                            <option
                                v-for="s in listSlip"
                                :key="s.value"
                                :value="s.value"
                            >
                                {{ s.label }}
                            </option>
                        </select>
                    </div>
                </div>

                <hr class="border-dashed" />

                <div class="space-y-4">
                    <input
                        ref="qrInput"
                        v-model="form.qr"
                        :disabled="form.processing"
                        type="text"
                        maxlength="10"
                        class="w-full text-center border-b-4 border-t-0 border-x-0 border-primary/30 focus:border-primary focus:ring-0 transition-all outline-none font-bold placeholder:text-slate-200 uppercase"
                        style="
                            background: transparent;
                            font-size: 2.2rem;
                            color: #1e3a8a;
                            height: 80px;
                        "
                        placeholder="SCAN DISINI"
                        @keyup.enter="scan"
                        @input="form.qr = form.qr.toUpperCase()"
                        autocomplete="off"
                    />

                    <div
                        v-if="form.processing"
                        class="flex items-center justify-center gap-2 text-primary font-medium"
                    >
                        <IconLoader2 class="animate-spin size-5" />
                        <span>Menyimpan...</span>
                    </div>

                    <p
                        v-if="form.errors.qr"
                        class="text-sm text-red-600 text-center font-semibold animate-pulse"
                    >
                        {{ form.errors.qr }}
                    </p>
                </div>
            </CardContent>
        </Card>

        <div
            class="mt-8 flex flex-col items-center gap-2 text-xs text-muted-foreground italic"
        >
            <div class="flex gap-2 text-green-600 font-medium">
                <span
                    class="w-2 h-2 rounded-full bg-green-500 animate-ping"
                ></span>
                <span>Siap menerima input QR</span>
            </div>
        </div>
    </div>
</template>
