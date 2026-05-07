<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3"; // Tambahkan Link
import { ref, onMounted, nextTick } from "vue";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { toast } from "vue-sonner";
import axios from "axios";
import {
    IconScan,
    IconTrash,
    IconPackage,
    IconLoader2,
    IconInfoCircle,
    IconArrowLeft,
    IconEye // Tambahkan Icon Eye
} from "@tabler/icons-vue";

const props = defineProps<{
    backUrl?: string;
}>();

const qrInput = ref<HTMLInputElement | null>(null);
const isLoading = ref(false);
const scannedList = ref<any[]>([]);

const form = useForm({
    qr: "",
});

const focusInput = () => {
    qrInput.value?.focus();
};

onMounted(() => focusInput());

const handleScan = async () => {
    const code = form.qr.trim().toUpperCase();
    if (!code || isLoading.value) return;

    if (scannedList.value.some(p => p.qrcode === code)) {
        toast.warning(`Produk ${code} sudah ada di daftar.`);
        form.qr = "";
        return;
    }

    isLoading.value = true;
    try {
        const response = await axios.post(route('periksa_post'), {
            qr: code
        });

        if (response.data) {
            scannedList.value.unshift(response.data);
            toast.success(`Berhasil memproses ${code}`);
        }
    } catch (error: any) {
        const msg = error.response?.data?.message || "Produk tidak ditemukan.";
        toast.error("Gagal", { description: msg });
    } finally {
        isLoading.value = false;
        form.qr = "";
        nextTick(() => focusInput());
    }
};

const removeFromList = (index: number) => {
    scannedList.value.splice(index, 1);
    focusInput();
};

const clearAll = () => {
    if (confirm("Bersihkan semua daftar periksa?")) {
        scannedList.value = [];
        focusInput();
    }
};

defineOptions({ layout: AuthenticatedLayout });
</script>

<template>
    <Head title="Periksa Produk" />

    <div class="max-w-6xl mx-auto p-4 md:p-6 space-y-6" @click="focusInput">
        <!-- Header -->
        <div class="flex items-center justify-between border-b pb-5">
            <div class="flex items-center gap-4">
                <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
                    <IconScan class="size-7 text-blue-600" />
                    Periksa Produk
                </h1>
            </div>
            <Button
                v-if="scannedList.length > 0"
                variant="ghost"
                @click.stop="clearAll"
                class="text-red-500 hover:text-red-600 font-bold uppercase text-xs"
            >
                <IconTrash class="size-4 mr-2" /> Hapus Semua
            </Button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Sidebar Input Scan -->
            <div class="lg:col-span-4 space-y-4">
                <Card class="border-2 border-blue-100 shadow-md sticky top-6">
                    <CardHeader class="bg-blue-50/50 pb-4">
                        <CardTitle class="text-[11px] font-black text-blue-600 uppercase tracking-widest">
                            Ready to Scan
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="pt-6">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <IconLoader2 v-if="isLoading" class="size-5 text-blue-500 animate-spin" />
                                <IconScan v-else class="size-5 text-slate-400" />
                            </div>
                            <input
                                ref="qrInput"
                                v-model="form.qr"
                                type="text"
                                class="block w-full pl-10 pr-3 py-4 text-2xl font-mono font-black border-2 border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-600 transition-all uppercase outline-none"
                                placeholder="......"
                                :disabled="isLoading"
                                @keyup.enter="handleScan"
                                autocomplete="off"
                            />
                        </div>
                        <div class="mt-6 flex items-start gap-3 p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <IconInfoCircle class="size-5 text-slate-400 shrink-0 mt-0.5" />
                            <p class="text-[11px] text-slate-500 leading-relaxed font-medium">
                                Masukkan nomor QR Code. Riwayat scan akan muncul sementara di tabel sebelah kanan.
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Main Content Table -->
            <div class="lg:col-span-8">
                <Card class="shadow-xl border-none ring-1 ring-slate-200">
                    <CardContent class="p-0">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-b text-[10px] font-black text-slate-500 uppercase tracking-widest">
                                        <th class="px-6 py-4">QR Code</th>
                                        <th class="px-6 py-4 text-center">Status Scan</th>
                                        <th class="px-6 py-4 text-center">Kondisi</th>
                                        <th class="px-6 py-4">Troli / Proses</th>
                                        <th class="px-6 py-4 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <tr v-for="(item, index) in scannedList" :key="item.id" class="hover:bg-slate-50/50 transition-colors animate-in fade-in slide-in-from-top-1">
                                        <!-- QR -->
                                        <td class="px-6 py-4 font-mono font-bold text-blue-700 text-base">
                                            {{ item.qrcode }}
                                        </td>

                                        <!-- STATUS SCAN -->
                                        <td class="px-6 py-4 text-center">
                                            <Badge v-if="item.sudah_scan === 'Sudah'" class="bg-blue-600 hover:bg-blue-600 text-white rounded-md border-none font-bold text-[10px] py-1 shadow-sm">
                                                SUDAH SCAN
                                            </Badge>
                                            <Badge v-else variant="secondary" class="text-slate-400 opacity-50 font-medium">
                                                BELUM
                                            </Badge>
                                        </td>

                                        <!-- STATUS AKHIR (Kondisi Barang) -->
                                        <td class="px-6 py-4 text-center">
                                            <Badge
                                                :class="item.status_akhir === 'OK' ? 'border-emerald-500 text-emerald-600 bg-emerald-50' : 'bg-red-50 text-red-600 border-red-500'"
                                                variant="outline"
                                                class="font-bold rounded-md"
                                            >
                                                {{ item.status_akhir }}
                                            </Badge>
                                        </td>

                                        <!-- TROLI & PROSES -->
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col">
                                                <span class="font-bold text-slate-800 tracking-tight">
                                                    {{ item.troli?.nomor ?? '---' }}
                                                </span>
                                                <span v-if="item.troli?.proses" class="text-[10px] text-blue-600 font-black uppercase tracking-widest mt-0.5">
                                                    {{ item.troli.proses.proses }}
                                                </span>
                                            </div>
                                        </td>

                                        <!-- ACTIONS -->
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <!-- TOMBOL DETAIL -->
                                                <Button
                                                    variant="outline"
                                                    size="icon"
                                                    class="size-8 rounded-full border-slate-200 text-slate-500 hover:text-blue-600 hover:border-blue-200"
                                                    as-child
                                                    title="Lihat Detail Produk"
                                                >
                                                    <Link :href="route('produk.show', item.id)">
                                                        <IconEye class="size-4" />
                                                    </Link>
                                                </Button>

                                                <!-- TOMBOL HAPUS LIST -->
                                                <Button
                                                    variant="ghost"
                                                    size="icon"
                                                    @click.stop="removeFromList(index)"
                                                    class="size-8 rounded-full text-slate-300 hover:text-red-500"
                                                    title="Hapus dari daftar sementara"
                                                >
                                                    <IconTrash class="size-4" />
                                                </Button>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Empty State -->
                                    <tr v-if="scannedList.length === 0">
                                        <td colspan="5" class="py-24 text-center">
                                            <div class="flex flex-col items-center gap-2 text-slate-300">
                                                <IconPackage class="size-12 opacity-20" />
                                                <p class="text-sm font-medium italic opacity-50 tracking-wide">Daftar periksa masih kosong.</p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>

<style scoped>
input:focus {
    outline: none !important;
    box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1) !important;
}
</style>
