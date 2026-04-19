<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
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
    IconBuildingCommunity,
} from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

// Definisi Props untuk menerima data departemen dari Controller
const props = defineProps<{
    departemens: Array<{ id: number; departemen: string }>;
}>();

const form = useForm({
    name: "",
    username: "",
    email: "",
    departemen_id: "", // Tambahkan field ini
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.post(route("users.store"));
};
</script>

<template>
    <Head title="Tambah Pengguna" />

    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1 md:pt-1">
        <div class="flex items-center gap-4">
            <Button variant="outline" size="icon" as-child>
                <Link :href="route('users.index')">
                    <IconArrowLeft class="size-4" />
                </Link>
            </Button>
            <div>
                <h2 class="text-3xl font-bold tracking-tight">
                    Tambah Pengguna
                </h2>
                <p class="text-muted-foreground text-sm">
                    Buat akun baru untuk sistem Sisamcus.
                </p>
            </div>
        </div>

        <div class="max-w-2xl">
            <Card class="shadow-md border-none">
                <CardHeader>
                    <CardTitle>Informasi Akun</CardTitle>
                    <CardDescription
                        >Pastikan semua informasi terisi dengan benar, terutama
                        penempatan departemen.</CardDescription
                    >
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid gap-2">
                            <Label for="name">Nama Lengkap</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                placeholder="Masukkan nama lengkap..."
                                :class="{
                                    'border-destructive': form.errors.name,
                                }"
                            />
                            <p
                                v-if="form.errors.name"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <div class="grid gap-2">
                            <Label for="departemen_id"
                                >Departemen / Unit Kerja</Label
                            >
                            <div class="relative">
                                <select
                                    id="departemen_id"
                                    v-model="form.departemen_id"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 shadow-sm appearance-none"
                                    :class="{
                                        'border-destructive':
                                            form.errors.departemen_id,
                                    }"
                                >
                                    <option value="" disabled>
                                        Pilih Departemen
                                    </option>
                                    <option
                                        v-for="dept in departemens"
                                        :key="dept.id"
                                        :value="dept.id"
                                    >
                                        {{ dept.departemen }}
                                    </option>
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-muted-foreground"
                                >
                                    <IconBuildingCommunity class="size-4" />
                                </div>
                            </div>
                            <p
                                v-if="form.errors.departemen_id"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.departemen_id }}
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label for="username">Username</Label>
                                <Input
                                    id="username"
                                    v-model="form.username"
                                    placeholder="johndoe"
                                    :class="{
                                        'border-destructive':
                                            form.errors.username,
                                    }"
                                />
                                <p
                                    v-if="form.errors.username"
                                    class="text-sm text-destructive"
                                >
                                    {{ form.errors.username }}
                                </p>
                            </div>
                            <div class="grid gap-2">
                                <Label for="email">Email</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    v-model="form.email"
                                    placeholder="email@contoh.com"
                                    :class="{
                                        'border-destructive': form.errors.email,
                                    }"
                                />
                                <p
                                    v-if="form.errors.email"
                                    class="text-sm text-destructive"
                                >
                                    {{ form.errors.email }}
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label for="password">Password</Label>
                                <Input
                                    id="password"
                                    type="password"
                                    v-model="form.password"
                                    :class="{
                                        'border-destructive':
                                            form.errors.password,
                                    }"
                                />
                                <p
                                    v-if="form.errors.password"
                                    class="text-sm text-destructive"
                                >
                                    {{ form.errors.password }}
                                </p>
                            </div>
                            <div class="grid gap-2">
                                <Label for="password_confirmation"
                                    >Konfirmasi Password</Label
                                >
                                <Input
                                    id="password_confirmation"
                                    type="password"
                                    v-model="form.password_confirmation"
                                />
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 gap-3">
                            <Button variant="ghost" type="button" as-child>
                                <Link :href="route('users.index')">Batal</Link>
                            </Button>
                            <Button type="submit" :disabled="form.processing">
                                <IconLoader2
                                    v-if="form.processing"
                                    class="mr-2 size-4 animate-spin"
                                />
                                <IconDeviceFloppy v-else class="mr-2 size-4" />
                                Simpan Pengguna
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
