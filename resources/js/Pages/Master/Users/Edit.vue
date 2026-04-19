<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
    CardDescription,
    CardFooter,
} from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import {
    IconArrowLeft,
    IconDeviceFloppy,
    IconLoader2,
    IconBuildingCommunity,
} from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    user: {
        id: number;
        name: string;
        username: string;
        email: string;
        departemen_id: number | null; // Tambahkan ini
    };
    // Tambahkan props untuk daftar departemen
    departemens: Array<{ id: number; nama: string }>;
}>();

const form = useForm({
    name: props.user.name,
    username: props.user.username,
    email: props.user.email,
    departemen_id: props.user.departemen_id, // Tambahkan ini
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.put(route("users.update", props.user.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="'Edit User - ' + user.name" />

    <div class="flex flex-col gap-6 p-4 md:p-8 pt-4">
        <div class="flex items-center">
            <Button
                variant="ghost"
                size="sm"
                as-child
                class="-ml-2 text-muted-foreground hover:text-foreground"
            >
                <Link :href="route('users.show', user.id)">
                    <IconArrowLeft class="mr-2 size-4" />
                    Kembali ke Detail
                </Link>
            </Button>
        </div>

        <div class="max-w-2xl mx-auto w-full">
            <Card class="border-none shadow-lg bg-card">
                <CardHeader class="pb-8">
                    <CardTitle class="text-2xl font-bold"
                        >Edit Profil Pengguna</CardTitle
                    >
                    <CardDescription class="text-sm">
                        Perbarui informasi akun dan penempatan departemen untuk
                        <span class="text-foreground font-semibold">{{
                            user.name
                        }}</span
                        >.
                    </CardDescription>
                </CardHeader>

                <form @submit.prevent="submit">
                    <CardContent class="grid gap-6">
                        <div class="grid gap-3">
                            <Label
                                for="name"
                                class="text-sm font-medium leading-none ml-0.5"
                                >Nama Lengkap</Label
                            >
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                placeholder="Masukkan nama lengkap"
                                class="h-11 shadow-sm"
                                :class="{
                                    'border-destructive': form.errors.name,
                                }"
                            />
                            <p
                                v-if="form.errors.name"
                                class="text-xs font-medium text-destructive"
                            >
                                {{ form.errors.name }}
                            </p>
                        </div>


                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="grid gap-3">
                                <Label
                                    for="username"
                                    class="text-sm font-medium leading-none ml-0.5"
                                    >Username</Label
                                >
                                <Input
                                    id="username"
                                    v-model="form.username"
                                    type="text"
                                    class="h-11 shadow-sm"
                                    :class="{
                                        'border-destructive':
                                            form.errors.username,
                                    }"
                                />
                                <p
                                    v-if="form.errors.username"
                                    class="text-xs font-medium text-destructive"
                                >
                                    {{ form.errors.username }}
                                </p>
                            </div>

                            <div class="grid gap-3">
                                <Label
                                    for="email"
                                    class="text-sm font-medium leading-none ml-0.5"
                                    >Email</Label
                                >
                                <Input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    class="h-11 shadow-sm"
                                    :class="{
                                        'border-destructive': form.errors.email,
                                    }"
                                />
                                <p
                                    v-if="form.errors.email"
                                    class="text-xs font-medium text-destructive"
                                >
                                    {{ form.errors.email }}
                                </p>
                            </div>
                        </div>

                        <div class="pt-6 mt-2 border-t border-dashed">
                            <h3
                                class="text-sm font-semibold mb-6 text-primary tracking-wide uppercase text-xs"
                            >
                                Ganti Password (Opsional)
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="grid gap-3">
                                    <Label
                                        for="password"
                                        class="text-sm font-medium leading-none ml-0.5"
                                        >Password Baru</Label
                                    >
                                    <Input
                                        id="password"
                                        v-model="form.password"
                                        type="password"
                                        placeholder="••••••••"
                                        class="h-11 shadow-sm"
                                    />
                                </div>
                                <div class="grid gap-3">
                                    <Label
                                        for="password_confirmation"
                                        class="text-sm font-medium leading-none ml-0.5"
                                        >Konfirmasi Password</Label
                                    >
                                    <Input
                                        id="password_confirmation"
                                        v-model="form.password_confirmation"
                                        type="password"
                                        placeholder="••••••••"
                                        class="h-11 shadow-sm"
                                    />
                                </div>
                            </div>
                            <p
                                v-if="form.errors.password"
                                class="text-xs font-medium text-destructive mt-3"
                            >
                                {{ form.errors.password }}
                            </p>
                        </div>
                    </CardContent>

                    <CardFooter
                        class="flex justify-end gap-3 border-t bg-muted/20 px-6 py-6 rounded-b-lg mt-4"
                    >
                        <Button
                            variant="outline"
                            type="button"
                            as-child
                            :disabled="form.processing"
                            class="h-10 px-6"
                        >
                            <Link :href="route('users.index')">Batal</Link>
                        </Button>
                        <Button
                            type="submit"
                            :disabled="form.processing"
                            class="h-10 px-6 shadow-md transition-all active:scale-95"
                        >
                            <IconLoader2
                                v-if="form.processing"
                                class="mr-2 size-4 animate-spin"
                            />
                            <IconDeviceFloppy v-else class="mr-2 size-4" />
                            Simpan Perubahan
                        </Button>
                    </CardFooter>
                </form>
            </Card>
        </div>
    </div>
</template>
