<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import {
    IconArrowLeft,
    IconDeviceFloppy,
    IconLoader2,
    IconDotsVertical,
    IconTrash,
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
    troliFisik: { id: number; nomor: string; status: string };
}>();

const form = useForm({
    nomor: props.troliFisik.nomor,
    status: props.troliFisik.status,
});

const submit = () => {
    form.put(route("trolifisiks.update", props.troliFisik.id));
};
</script>

<template>
    <Head :title="'Edit Troli - ' + troliFisik.nomor" />

    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Button
                    variant="outline"
                    size="icon"
                    as-child
                    class="rounded-full shadow-sm"
                >
                    <Link :href="route('trolifisiks.index')">
                        <IconArrowLeft class="size-4" />
                    </Link>
                </Button>
                <h2 class="text-3xl font-bold tracking-tight">
                    Edit Troli Fisik
                </h2>
            </div>
        </div>

        <div class="max-w-2xl">
            <Card class="border-none shadow-lg">
                <CardHeader
                    class="flex flex-row items-center justify-between border-b py-4"
                >
                    <CardTitle class="text-primary text-lg"
                        >Update Data Troli</CardTitle
                    >

                    <AlertDialog>
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button variant="ghost" size="icon">
                                    <IconDotsVertical class="size-4" />
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end" class="w-40">
                                <AlertDialogTrigger as-child>
                                    <DropdownMenuItem
                                        class="text-destructive focus:text-destructive cursor-pointer"
                                    >
                                        <IconTrash class="mr-2 size-4" />
                                        Hapus Data
                                    </DropdownMenuItem>
                                </AlertDialogTrigger>
                            </DropdownMenuContent>
                        </DropdownMenu>

                        <AlertDialogContent>
                            <AlertDialogHeader>
                                <AlertDialogTitle
                                    >Hapus Data Troli?</AlertDialogTitle
                                >
                                <AlertDialogDescription>
                                    Tindakan ini tidak dapat dibatalkan. Data
                                    troli
                                    <strong class="text-foreground">{{
                                        props.troliFisik.nomor
                                    }}</strong>
                                    akan dihapus secara permanen dari server.
                                </AlertDialogDescription>
                            </AlertDialogHeader>
                            <AlertDialogFooter>
                                <AlertDialogCancel>Batal</AlertDialogCancel>
                                <AlertDialogAction
                                    @click="
                                        router.delete(
                                            route(
                                                'trolifisiks.destroy',
                                                props.troliFisik.id,
                                            ),
                                        )
                                    "
                                    class="bg-destructive text-white hover:bg-destructive/90"
                                >
                                    Ya, Hapus
                                </AlertDialogAction>
                            </AlertDialogFooter>
                        </AlertDialogContent>
                    </AlertDialog>
                </CardHeader>

                <CardContent class="pt-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid gap-2">
                            <Label for="nomor">Nomor Troli</Label>
                            <Input
                                id="nomor"
                                v-model="form.nomor"
                                class="uppercase h-11"
                                placeholder="Contoh: TRL-001"
                                :class="{
                                    'border-destructive': form.errors.nomor,
                                }"
                            />
                            <p
                                v-if="form.errors.nomor"
                                class="text-sm text-destructive font-medium"
                            >
                                {{ form.errors.nomor }}
                            </p>
                        </div>

                        <div class="grid gap-2">
                            <Label for="status">Status Penggunaan</Label>
                            <select
                                id="status"
                                v-model="form.status"
                                class="flex h-11 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring shadow-sm"
                            >
                                <option value="Tidak">Tidak</option>
                                <option value="Digunakan">Digunakan</option>
                            </select>
                            <p
                                v-if="form.errors.status"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.status }}
                            </p>
                        </div>

                        <Button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full bg-primary hover:bg-primary/90 shadow-md transition-all active:scale-95"
                        >
                            <IconLoader2
                                v-if="form.processing"
                                class="mr-2 size-4 animate-spin"
                            />
                            <IconDeviceFloppy v-else class="mr-2 size-4" />
                            Simpan Perubahan
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
