<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Label } from "@/components/ui/label";
import { Input } from "@/components/ui/input";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import {
    IconArrowLeft,
    IconDeviceFloppy,
    IconLoader2,
    IconEdit,
    IconUsers,
    IconClock,
} from "@tabler/icons-vue";

const props = defineProps<{
    sesikerja: {
        id: number;
        shift_id: number;
        jenis: string;
        // Kita butuh data member yang sudah ada
        sesi_kerja_members: Array<{ user_id: number }>;
    };
    // Kita butuh daftar user untuk dipilih kembali
    shifts: Array<{ id: number; shift: string }>;
    users: Array<{ id: number; name: string }>;
}>();

defineOptions({ layout: AuthenticatedLayout });

// Ambil ID user yang sudah terdaftar sebagai member untuk default value
const existingMemberIds = props.sesikerja.sesi_kerja_members.map(
    (m) => m.user_id,
);

const form = useForm({
    shift_id: props.sesikerja.shift_id,
    jenis: props.sesikerja.jenis,
    user_ids: existingMemberIds, // Masukkan anggota yang sudah ada
});

const submit = () => {
    form.put(route("sesikerjas.update", props.sesikerja.id));
};
</script>

<template>
    <Head title="Edit Sesi Kerja" />
    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">
        <div class="flex items-center gap-4">
            <Button variant="outline" size="icon" as-child class="rounded-full">
                <Link :href="route('sesikerjas.show', props.sesikerja.id)">
                    <IconArrowLeft class="size-4" />
                </Link>
            </Button>
            <h2 class="text-3xl font-bold tracking-tight">Edit Sesi Kerja</h2>
        </div>

        <div class="max-w-2xl">
            <Card class="border-none shadow-lg">
                <CardHeader>
                    <CardTitle
                        class="text-primary text-lg flex items-center gap-2"
                    >
                        <IconEdit class="size-5" />
                        Ubah Detail Sesi
                    </CardTitle>
                </CardHeader>

                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid grid-cols-1 gap-6">
                            <div class="grid gap-2">
                                <Label class="flex items-center gap-2">
                                    <IconClock class="size-4" /> Jenis Pekerjaan
                                </Label>

                                <Select v-model="form.jenis">
                                    <SelectTrigger>
                                        <SelectValue
                                            placeholder="Pilih Jenis"
                                        />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="Body"
                                            >Body</SelectItem
                                        >
                                        <SelectItem value="Tangki"
                                            >Tangki</SelectItem
                                        >
                                    </SelectContent>
                                </Select>
                            </div>

                            <div class="grid gap-2">
                                <Label class="flex items-center gap-2">
                                    <IconClock class="size-4" /> Pilih Shift
                                </Label>
                                <Select v-model="form.shift_id">
                                    <SelectTrigger
                                        :class="{
                                            'border-destructive':
                                                form.errors.shift_id,
                                        }"
                                    >
                                        <SelectValue
                                            placeholder="Pilih Shift Kerja"
                                        />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="s in shifts"
                                            :key="s.id"
                                            :value="s.id"
                                        >
                                            {{ s.shift }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <p
                                    v-if="form.errors.shift_id"
                                    class="text-xs text-destructive italic"
                                >
                                    {{ form.errors.shift_id }}
                                </p>
                            </div>

                            <div class="grid gap-4 pt-4 border-t">
                                <Label
                                    class="text-base flex items-center gap-2"
                                >
                                    <IconUsers class="size-5 text-primary" />
                                    Perbarui Anggota Tim
                                </Label>

                                <div
                                    class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-60 overflow-y-auto p-4 bg-muted/30 rounded-lg border"
                                >
                                    <div
                                        v-for="user in users"
                                        :key="user.id"
                                        class="flex items-center space-x-3 p-2 hover:bg-background rounded-md transition-colors"
                                    >
                                        <input
                                            type="checkbox"
                                            :id="'user-' + user.id"
                                            :value="user.id"
                                            v-model="form.user_ids"
                                            class="size-4 rounded border-gray-300 text-primary focus:ring-primary"
                                        />
                                        <label
                                            :for="'user-' + user.id"
                                            class="text-sm font-medium leading-none cursor-pointer w-full"
                                        >
                                            {{ user.name }}
                                        </label>
                                    </div>
                                </div>
                                <p
                                    v-if="form.errors.user_ids"
                                    class="text-xs text-destructive italic"
                                >
                                    {{ form.errors.user_ids }}
                                </p>
                            </div>
                        </div>

                        <div class="pt-4 border-t">
                            <Button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full bg-primary h-11"
                            >
                                <IconLoader2
                                    v-if="form.processing"
                                    class="mr-2 animate-spin"
                                />
                                <IconDeviceFloppy v-else class="mr-2" />
                                Simpan Perubahan
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
