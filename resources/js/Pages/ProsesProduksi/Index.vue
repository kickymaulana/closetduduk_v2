<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { IconArrowsSplit2, IconBuildingCommunity, IconCircleCheck } from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    proses: Array<{
        id: number;
        proses: string;
        urutan: number;
        departemen: { departemen: string };
    }>;
}>();

// Preset warna untuk variasi stepper (akan berulang jika proses > 5)
const colors = [
    'text-blue-600 bg-blue-50 border-blue-200 shadow-blue-100',
    'text-emerald-600 bg-emerald-50 border-emerald-200 shadow-emerald-100',
    'text-amber-600 bg-amber-50 border-amber-200 shadow-amber-100',
    'text-rose-600 bg-rose-50 border-rose-200 shadow-rose-100',
    'text-purple-600 bg-purple-50 border-purple-200 shadow-purple-100',
    'text-indigo-600 bg-indigo-50 border-indigo-200 shadow-indigo-100',
];

const getBadgeColor = (index: number) => colors[index % colors.length];
</script>

<template>
    <Head title="Alur Produksi" />

    <div class="flex flex-col gap-4 p-4 md:p-8 pt-4 max-w-4xl mx-auto">
        <Card class="border-none shadow-lg">
            <CardHeader class="border-b">
                <CardTitle class="text-xl font-bold flex items-center gap-2">
                    <IconArrowsSplit2 class="size-6 text-primary" />
                    Visualisasi Alur Produksi
                </CardTitle>
                <p class="text-sm text-muted-foreground">Daftar tahapan produksi yang sedang berjalan secara berurutan.</p>
            </CardHeader>

            <CardContent class="pt-8">
                <div class="relative space-y-2">
                    <div class="absolute left-6 top-2 w-0.5 h-[calc(100%-24px)] bg-slate-200"></div>

                    <div v-if="proses.length === 0" class="text-center py-10 text-muted-foreground italic">
                        Belum ada data proses.
                    </div>

                    <div
                        v-for="(item, index) in proses"
                        :key="item.id"
                        class="relative flex gap-4 pb-4 last:pb-0"
                    >
                        <div class="relative z-10 flex items-center justify-center">
                            <div
                                class="size-12 rounded-full flex items-center justify-center font-bold text-lg border-4 border-white shadow-sm transition-transform hover:scale-110"
                                :class="getBadgeColor(index)"
                            >
                                {{ item.urutan }}
                            </div>
                        </div>

                        <div class="flex-1">
                            <div
                                class="flex flex-col md:flex-row md:items-center justify-between p-3 px-5 rounded-2xl border transition-all"
                                :class="getBadgeColor(index)"
                            >
                                <div>
                                    <h4 class="font-black text-sm uppercase tracking-wider mb-0.5">
                                        {{ item.proses }}
                                    </h4>
                                    <div class="flex items-center gap-1.5 opacity-80">
                                        <IconBuildingCommunity class="size-3.5" />
                                        <span class="text-xs font-semibold">{{ item.departemen.departemen }}</span>
                                    </div>
                                </div>

                                <div class="mt-2 md:mt-0">
                                    <div class="flex items-center gap-1 bg-white/50 px-2 py-0.5 rounded-full border border-white/50 w-fit">
                                        <IconCircleCheck class="size-3.5 text-current" />
                                        <span class="text-[10px] font-bold uppercase">Langkah {{ index + 1 }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-10 pt-6 border-t border-dashed flex justify-center text-center">
                    <div class="bg-slate-100 px-4 py-2 rounded-full">
                        <p class="text-[11px] font-bold text-slate-500 uppercase tracking-[0.2em]">
                            Total Sistem: {{ proses.length }} Tahapan Produksi
                        </p>
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>

<style scoped>
/* Transisi halus saat komponen muncul */
.flex-1 > div {
    animation: slideIn 0.3s ease-out forwards;
}

@keyframes slideIn {
    from { opacity: 0; transform: translateX(-10px); }
    to { opacity: 1; transform: translateX(0); }
}
</style>
