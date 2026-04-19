<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import { ref } from "vue";
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
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
    DropdownMenuSeparator,
} from "@/components/ui/dropdown-menu";
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from "@/components/ui/alert-dialog";
import {
    IconArrowLeft,
    IconDeviceFloppy,
    IconLoader2,
    IconDotsVertical,
    IconTrash,
} from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    role: {
        id: number;
        name: string;
        permissions: Array<{ id: number; name: string }>;
    };
    all_permissions: Array<{ id: number; name: string }>;
}>();

// State untuk mengontrol AlertDialog
const showDeleteDialog = ref(false);

const form = useForm({
    name: props.role.name,
    permissions: props.role.permissions.map((p) => p.id),
});

const togglePermission = (id: number) => {
    const index = form.permissions.indexOf(id);
    if (index > -1) {
        form.permissions.splice(index, 1);
    } else {
        form.permissions.push(id);
    }
};

const submit = () => {
    form.put(route("roles.update", props.role.id), {
        preserveScroll: true,
    });
};

const deleteRole = () => {
    router.delete(route("roles.destroy", props.role.id), {
        onSuccess: () => {
            showDeleteDialog.value = false;
        },
    });
};
</script>

<template>
    <Head :title="'Edit Jabatan - ' + role.name" />

    <div class="flex flex-col gap-6 p-4 md:p-8 pt-4">
        <div class="flex items-center">
            <Button
                variant="ghost"
                size="sm"
                as-child
                class="-ml-2 text-muted-foreground hover:text-foreground"
            >
                <Link :href="route('roles.index')">
                    <IconArrowLeft class="mr-2 size-4" />
                    Kembali ke Daftar
                </Link>
            </Button>
        </div>

        <div class="max-w-4xl mx-auto w-full">
            <Card class="border-none shadow-lg bg-card">
                <CardHeader
                    class="pb-8 flex flex-row items-start justify-between space-y-0"
                >
                    <div class="grid gap-1.5">
                        <CardTitle class="text-2xl font-bold"
                            >Edit Jabatan / Role</CardTitle
                        >
                        <CardDescription class="text-sm">
                            Perbarui nama jabatan dan atur hak akses untuk
                            <span class="text-foreground font-semibold">{{
                                role.name
                            }}</span
                            >.
                        </CardDescription>
                    </div>

                    <div class="flex items-center">
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    class="h-8 w-8 rounded-full"
                                >
                                    <IconDotsVertical
                                        class="size-5 text-muted-foreground"
                                    />
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end" class="w-48">
                                <div
                                    class="px-2 py-1.5 text-xs font-semibold text-muted-foreground uppercase tracking-wider"
                                >
                                    Opsi Lanjutan
                                </div>
                                <DropdownMenuSeparator />
                                <DropdownMenuItem
                                    @select="showDeleteDialog = true"
                                    :disabled="role.name === 'admin'"
                                    class="text-destructive focus:bg-destructive/10 focus:text-destructive cursor-pointer"
                                >
                                    <IconTrash class="mr-2 size-4" />
                                    Hapus Jabatan
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </div>
                </CardHeader>

                <form @submit.prevent="submit">
                    <CardContent class="grid gap-6">
                        <div class="grid gap-3">
                            <Label
                                for="name"
                                class="text-sm font-medium leading-none ml-0.5"
                                >Nama Jabatan</Label
                            >
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                placeholder="Masukkan nama jabatan"
                                class="h-11 shadow-sm focus-visible:ring-primary"
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

                        <div class="pt-6 mt-2 border-t border-dashed">
                            <h3
                                class="text-sm font-semibold mb-6 text-primary tracking-wide uppercase text-[10px]"
                            >
                                Hak Akses (Permissions)
                            </h3>

                            <div
                                class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4"
                            >
                                <div
                                    v-for="permission in all_permissions"
                                    :key="permission.id"
                                    @click="togglePermission(permission.id)"
                                    class="flex items-center p-3 rounded-lg border bg-card hover:bg-muted/50 transition-colors cursor-pointer group"
                                    :class="
                                        form.permissions.includes(permission.id)
                                            ? 'border-primary/50 bg-primary/5'
                                            : 'border-border'
                                    "
                                >
                                    <div class="flex items-center h-5">
                                        <input
                                            type="checkbox"
                                            :checked="
                                                form.permissions.includes(
                                                    permission.id,
                                                )
                                            "
                                            class="size-4 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer"
                                            @change.stop="
                                                togglePermission(permission.id)
                                            "
                                        />
                                    </div>
                                    <label
                                        class="ml-3 text-sm font-medium leading-none cursor-pointer capitalize group-hover:text-foreground"
                                    >
                                        {{ permission.name.replace(/-/g, " ") }}
                                    </label>
                                </div>
                            </div>

                            <p
                                v-if="form.errors.permissions"
                                class="text-xs font-medium text-destructive mt-4"
                            >
                                {{ form.errors.permissions }}
                            </p>
                        </div>
                    </CardContent>

                    <CardFooter
                        class="flex justify-end gap-3 border-t bg-muted/20 px-6 py-6 rounded-b-lg"
                    >
                        <Button
                            variant="outline"
                            type="button"
                            as-child
                            :disabled="form.processing"
                            class="h-10 px-6"
                        >
                            <Link :href="route('roles.index')">Batal</Link>
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

    <AlertDialog
        :open="showDeleteDialog"
        @update:open="showDeleteDialog = $event"
    >
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle
                    >Apakah anda benar-benar yakin?</AlertDialogTitle
                >
                <AlertDialogDescription>
                    Tindakan ini tidak dapat dibatalkan. Ini akan menghapus
                    jabatan
                    <strong>{{ role.name }}</strong> dan mencabut akses dari
                    semua pengguna yang memilikinya.
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel>Batal</AlertDialogCancel>
                <AlertDialogAction
                    @click="deleteRole"
                    class="bg-destructive text-destructive-foreground hover:bg-destructive/90"
                >
                    <IconTrash class="mr-2 size-4" />
                    Ya, Hapus Jabatan
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
