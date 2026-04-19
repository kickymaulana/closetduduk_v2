<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import { Head, Link, useForm } from "@inertiajs/vue3"
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from "@/components/ui/card"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import {
  IconArrowLeft,
  IconPlus,
  IconLoader2,
  IconHierarchy
} from "@tabler/icons-vue"

// 1. Definisikan Persistent Layout
defineOptions({ layout: AuthenticatedLayout })

// 2. Inisialisasi Form Inertia
const form = useForm({
  name: '',
})

// 3. Submit Handler
const submit = () => {
  form.post(route('roles.store'), {
    onSuccess: () => form.reset(),
  })
}
</script>

<template>
  <Head title="Tambah Jabatan Baru" />

  <div class="flex flex-col gap-6 p-4 md:p-8 pt-4">
    <div class="flex items-center">
      <Button variant="ghost" size="sm" as-child class="-ml-2 text-muted-foreground hover:text-foreground">
        <Link :href="route('roles.index')">
          <IconArrowLeft class="mr-2 size-4" />
          Kembali ke Daftar
        </Link>
      </Button>
    </div>

    <div class="max-w-2xl mx-auto w-full">
      <Card class="border-none shadow-lg">
        <CardHeader class="flex flex-row items-center gap-4 pb-8">
          <div class="p-3 bg-primary/10 rounded-xl">
            <IconHierarchy class="size-6 text-primary" />
          </div>
          <div class="grid gap-1">
            <CardTitle class="text-2xl font-bold">Tambah Jabatan</CardTitle>
            <CardDescription>
              Buat kategori jabatan baru untuk mengatur hak akses pengguna.
            </CardDescription>
          </div>
        </CardHeader>

        <form @submit.prevent="submit">
          <CardContent class="grid gap-6">
            <div class="grid gap-3">
              <Label for="name" class="text-sm font-medium ml-0.5">Nama Jabatan</Label>
              <Input
                id="name"
                v-model="form.name"
                type="text"
                placeholder="Misal: Manager, Staff Gudang, Operator"
                class="h-11 shadow-sm focus-visible:ring-primary"
                :class="{ 'border-destructive': form.errors.name }"
                autofocus
              />
              <p v-if="form.errors.name" class="text-xs font-medium text-destructive">
                {{ form.errors.name }}
              </p>
            </div>

            <div class="p-4 rounded-lg bg-muted/50 border border-dashed text-xs text-muted-foreground">
              Sistem akan otomatis menetapkan <strong>Guard: web</strong> untuk jabatan baru ini agar dapat digunakan pada aplikasi utama.
            </div>
          </CardContent>

          <CardFooter class="flex justify-end gap-3 border-t bg-muted/20 px-6 py-6 rounded-b-lg">
            <Button variant="outline" type="button" as-child class="h-10 px-6">
              <Link :href="route('roles.index')">Batal</Link>
            </Button>
            <Button type="submit" :disabled="form.processing" class="h-10 px-6 shadow-md transition-all active:scale-95">
              <IconLoader2 v-if="form.processing" class="mr-2 size-4 animate-spin" />
              <IconPlus v-else class="mr-2 size-4" />
              Simpan Jabatan
            </Button>
          </CardFooter>
        </form>
      </Card>
    </div>
  </div>
</template>
