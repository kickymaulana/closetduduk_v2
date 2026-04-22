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
    IconClockPlay,
    IconUsers,
} from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    users: Array<{ id: number; name: string }>;
}>();

// Ambil waktu sekarang untuk default jam masuk (format: YYYY-MM-DDTHH:mm)
const now = new Date().toISOString().slice(0, 16);

const form = useForm({
    jam_masuk: now,
    jam_pulang: "",
    jenis: "Body",
    user_ids: [] as number[],
});

const submit = () => {
    form.post(route("sesikerjas.store"));
};
</script>

<template>
    <Head title="Tambah Sesi Kerja" />
    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">
        <div class="flex items-center gap-4">
            <Button variant="outline" size="icon" as-child class="rounded-full">
                <Link :href="route('sesikerjas.index')">
                    <IconArrowLeft class="size-4" />
                </Link>
            </Button>
            <h2 class="text-3xl font-bold tracking-tight">Catat Sesi Kerja</h2>
        </div>

        <div class="max-w-2xl">
            <Card class="border-none shadow-lg">
                <CardHeader>
                    <CardTitle
                        class="text-primary text-lg flex items-center gap-2"
                    >
                        <IconClockPlay class="size-5" />
                        Detail Waktu Kerja
                    </CardTitle>
                </CardHeader>

                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid grid-cols-1 gap-6">
                            <div class="grid gap-2">
                                <Label>Jenis Pekerjaan</Label>
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

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="grid gap-2">
                                    <Label for="jam_masuk">Jam Masuk</Label>
                                    <Input
                                        id="jam_masuk"
                                        type="datetime-local"
                                        v-model="form.jam_masuk"
                                        :class="{
                                            'border-destructive':
                                                form.errors.jam_masuk,
                                        }"
                                    />
                                    <p
                                        v-if="form.errors.jam_masuk"
                                        class="text-xs text-destructive italic"
                                    >
                                        {{ form.errors.jam_masuk }}
                                    </p>
                                </div>

                                <div class="grid gap-2">
                                    <Label for="jam_pulang">Jam Pulang</Label>
                                    <Input
                                        id="jam_pulang"
                                        type="datetime-local"
                                        v-model="form.jam_pulang"
                                        :class="{
                                            'border-destructive':
                                                form.errors.jam_pulang,
                                        }"
                                    />
                                    <p
                                        v-if="form.errors.jam_pulang"
                                        class="text-xs text-destructive italic"
                                    >
                                        {{ form.errors.jam_pulang }}
                                    </p>
                                </div>
                            </div>


                            <div class="grid gap-4 pt-4 border-t">
    <Label class="text-base flex items-center gap-2">
        <IconUsers class="size-5 text-primary" />
        Pilih Anggota Tim
    </Label>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-60 overflow-y-auto p-4 bg-muted/30 rounded-lg border">
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
                class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 cursor-pointer w-full"
            >
                {{ user.name }}
            </label>
        </div>

        <div v-if="users.length === 0" class="col-span-2 text-center py-4 text-muted-foreground text-sm italic">
            Tidak ada user lain yang tersedia.
        </div>
    </div>
    <p v-if="form.errors.user_ids" class="text-xs text-destructive italic">
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
                                Simpan Sesi
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
