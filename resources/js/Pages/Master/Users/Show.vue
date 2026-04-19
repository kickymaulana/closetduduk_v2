<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
    CardDescription,
} from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Separator } from "@/components/ui/separator";
import { Badge } from "@/components/ui/badge";
import {
    IconArrowLeft,
    IconPencil,
    IconUser,
    IconMail,
    IconCalendar,
    IconFingerprint,
    IconClock,
    IconBuildingCommunity,
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
import { router } from "@inertiajs/vue3";

// 1. Definisikan Persistent Layout
defineOptions({ layout: AuthenticatedLayout });

// 2. Definisi Props
const props = defineProps<{
    user: {
        id: number;
        name: string;
        username: string;
        email: string;
        departemen_nama: string; // Data dari Eager Loading di Controller
        created_at: string;
        updated_at: string;
    };
}>();

const deleteUser = () => {
    router.delete(route("users.destroy", props.user.id), {
        preserveScroll: true,
    });
};

// 3. Helper Formatter
const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "long",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};
</script>

<template>
    <Head :title="'Detail User - ' + user.name" />

    <div class="flex flex-col gap-6 p-4 md:p-8 pt-4">
        <div class="flex items-center gap-2">
            <Button
                variant="ghost"
                size="sm"
                as-child
                class="-ml-2 text-muted-foreground hover:text-foreground"
            >
                <Link :href="route('users.index')">
                    <IconArrowLeft class="mr-2 size-4" />
                    Kembali ke Daftar
                </Link>
            </Button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <Card class="border-none shadow-sm col-span-1 h-fit">
                <CardContent class="pt-6">
                    <div class="flex flex-col items-center text-center">
                        <div
                            class="size-24 rounded-full bg-primary/10 flex items-center justify-center mb-4"
                        >
                            <IconUser class="size-12 text-primary" />
                        </div>
                        <h2 class="text-xl font-bold uppercase tracking-tight">
                            {{ user.name }}
                        </h2>
                        <p class="text-sm text-muted-foreground mb-3">
                            @{{ user.username }}
                        </p>


                        <div class="w-full mt-8 flex flex-col gap-2">
                            <Button class="w-full" variant="outline" as-child>
                                <Link :href="route('users.edit', user.id)">
                                    <IconPencil class="mr-2 size-4" />
                                    Edit Profil
                                </Link>
                            </Button>

                            <AlertDialog>
                                <AlertDialogTrigger as-child>
                                    <Button
                                        variant="destructive"
                                        class="w-full"
                                    >
                                        <IconTrash class="mr-2 size-4" />
                                        Hapus User
                                    </Button>
                                </AlertDialogTrigger>
                                <AlertDialogContent>
                                    <AlertDialogHeader>
                                        <AlertDialogTitle
                                            >Hapus Akun
                                            Pengguna?</AlertDialogTitle
                                        >
                                        <AlertDialogDescription>
                                            Tindakan ini permanen. Akun
                                            <strong>{{ user.name }}</strong>
                                            akan dihapus selamanya dari sistem
                                            Sisamcus.
                                        </AlertDialogDescription>
                                    </AlertDialogHeader>
                                    <AlertDialogFooter>
                                        <AlertDialogCancel
                                            >Batal</AlertDialogCancel
                                        >
                                        <AlertDialogAction
                                            @click="deleteUser"
                                            class="bg-destructive text-destructive-foreground hover:bg-destructive/90"
                                        >
                                            Ya, Hapus
                                        </AlertDialogAction>
                                    </AlertDialogFooter>
                                </AlertDialogContent>
                            </AlertDialog>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card class="border-none shadow-sm lg:col-span-2">
                <CardHeader>
                    <CardTitle>Informasi Pengguna</CardTitle>
                    <CardDescription
                        >Detail lengkap mengenai identitas dan penempatan
                        kerja.</CardDescription
                    >
                </CardHeader>
                <Separator />
                <CardContent class="pt-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                        <div class="space-y-1">
                            <div
                                class="flex items-center text-[10px] font-bold uppercase text-muted-foreground gap-2 tracking-wider"
                            >
                                <IconMail class="size-3.5" />
                                <span>Alamat Email</span>
                            </div>
                            <p class="font-medium text-sm">{{ user.email }}</p>
                        </div>

                        <div class="space-y-1">
                            <div
                                class="flex items-center text-[10px] font-bold uppercase text-muted-foreground gap-2 tracking-wider"
                            >
                                <IconFingerprint class="size-3.5" />
                                <span>ID Internal</span>
                            </div>
                            <p class="font-mono text-xs">#{{ user.id }}</p>
                        </div>

                        <div class="space-y-1">
                            <div
                                class="flex items-center text-[10px] font-bold uppercase text-muted-foreground gap-2 tracking-wider"
                            >
                                <IconCalendar class="size-3.5" />
                                <span>Terdaftar Sejak</span>
                            </div>
                            <p class="font-medium text-sm">
                                {{ formatDate(user.created_at) }}
                            </p>
                        </div>

                        <div class="space-y-1 md:col-span-2">
                            <div
                                class="flex items-center text-[10px] font-bold uppercase text-muted-foreground gap-2 tracking-wider"
                            >
                                <IconClock class="size-3.5" />
                                <span>Pembaruan Terakhir</span>
                            </div>
                            <p class="font-medium text-sm">
                                {{ formatDate(user.updated_at) }}
                            </p>
                        </div>
                    </div>

                    <div
                        class="mt-10 p-4 rounded-lg bg-muted/30 border border-dashed border-muted-foreground/20"
                    >
                        <p
                            class="text-[11px] text-muted-foreground italic text-center leading-relaxed"
                        >
                            Data ini merupakan identitas resmi di sistem
                            SISAMCUS. <br />
                            Pastikan departemen sudah sesuai dengan surat
                            penempatan kerja terbaru.
                        </p>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
