<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
    CardDescription,
} from "@/components/ui/card";
import {
    IconArrowLeft,
    IconDeviceFloppy,
    IconLoader2,
    IconHierarchy2,
    IconTrash,
    IconDotsVertical,
    IconAlertCircle,
} from "@tabler/icons-vue";
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from "@/components/ui/alert-dialog";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    departemen: {
        id: number;
        departemen: string;
        urutan: number;
    };
}>();

const form = useForm({
    departemen: props.departemen.departemen,
    urutan: props.departemen.urutan,
});

const submit = () => {
    form.put(route("departemens.update", props.departemen.id));
};

// Fungsi hapus tanpa konfirmasi browser lagi
const deleteDepartemen = () => {
    router.delete(route("departemens.destroy", props.departemen.id));
};
</script>

<template>
    <Head title="Edit Departemen" />

    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1 md:pt-1">
        <div class="flex items-center gap-4">
            <Button
                variant="outline"
                size="icon"
                as-child
                class="rounded-full shadow-sm"
            >
                <Link :href="route('departemens.index')">
                    <IconArrowLeft class="size-4" />
                </Link>
            </Button>
            <div>
                <h2 class="text-3xl font-bold tracking-tight">
                    Pengaturan Departemen
                </h2>
                <p class="text-muted-foreground text-sm">
                    Kelola struktur organisasi produksi.
                </p>
            </div>
        </div>

        <div class="max-w-2xl">
            <Card
                class="border-none shadow-lg bg-white/50 dark:bg-black/20 backdrop-blur-md overflow-hidden"
            >
                <CardHeader
                    class="flex flex-row items-center justify-between border-b bg-muted/20"
                >
                    <div class="flex items-center gap-2 text-primary">
                        <IconHierarchy2 class="size-5" />
                        <CardTitle class="text-lg">Form Perubahan</CardTitle>
                    </div>

                    <AlertDialog>
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    class="size-8"
                                >
                                    <IconDotsVertical class="size-4" />
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end" class="w-40">
                                <AlertDialogTrigger as-child>
                                    <DropdownMenuItem
                                        class="text-destructive focus:bg-destructive focus:text-white cursor-pointer"
                                    >
                                        <IconTrash class="mr-2 size-4" />
                                        Hapus Data
                                    </DropdownMenuItem>
                                </AlertDialogTrigger>
                            </DropdownMenuContent>
                        </DropdownMenu>

                        <AlertDialogContent>
                            <AlertDialogHeader>
                                <div
                                    class="flex items-center gap-2 text-destructive mb-2"
                                >
                                    <IconAlertCircle class="size-5" />
                                    <AlertDialogTitle
                                        >Konfirmasi Hapus</AlertDialogTitle
                                    >
                                </div>
                                <AlertDialogDescription>
                                    Apakah Anda yakin ingin menghapus departemen
                                    <strong>{{
                                        props.departemen.departemen
                                    }}</strong
                                    >? Semua pengaturan terkait departemen ini
                                    akan hilang permanen dari sistem.
                                </AlertDialogDescription>
                            </AlertDialogHeader>
                            <AlertDialogFooter>
                                <AlertDialogCancel>Batal</AlertDialogCancel>
                                <AlertDialogAction
                                    @click="deleteDepartemen"
                                    class="bg-destructive text-white hover:bg-destructive/90 transition-all active:scale-95"
                                >
                                    Ya, Hapus Sekarang
                                </AlertDialogAction>
                            </AlertDialogFooter>
                        </AlertDialogContent>
                    </AlertDialog>
                </CardHeader>

                <CardContent class="pt-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid gap-2">
                            <Label
                                for="departemen"
                                class="font-semibold text-primary"
                                >Nama Departemen</Label
                            >
                            <Input
                                id="departemen"
                                v-model="form.departemen"
                                class="uppercase bg-white/50 dark:bg-black/20 border-primary/20"
                            />
                            <p
                                v-if="form.errors.departemen"
                                class="text-sm text-destructive italic"
                            >
                                {{ form.errors.departemen }}
                            </p>
                        </div>

                        <div class="grid gap-2 max-w-[200px]">
                            <Label
                                for="urutan"
                                class="font-semibold text-primary"
                                >Nomor Urutan</Label
                            >
                            <Input
                                id="urutan"
                                type="number"
                                v-model="form.urutan"
                                class="bg-white/50 dark:bg-black/20 border-primary/20"
                            />
                            <p
                                v-if="form.errors.urutan"
                                class="text-sm text-destructive italic"
                            >
                                {{ form.errors.urutan }}
                            </p>
                        </div>

                        <div class="flex justify-end pt-4 border-t gap-3">
                            <Button
                                type="submit"
                                :disabled="form.processing"
                                class="bg-primary hover:bg-primary/90 text-white shadow-md transition-all active:scale-95 px-8"
                            >
                                <IconLoader2
                                    v-if="form.processing"
                                    class="mr-2 size-4 animate-spin"
                                />
                                <IconDeviceFloppy v-else class="mr-2 size-4" />
                                Simpan Perubahan
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
