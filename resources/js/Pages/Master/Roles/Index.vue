<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import { Head, Link, router } from "@inertiajs/vue3"
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table"
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import {
  IconHierarchy,
  IconPencil,
  IconTrash,
  IconSearch,
  IconX,
  IconPlus,
  IconEye
} from "@tabler/icons-vue"
import { ref, watch } from 'vue'

// 1. Definisikan Persistent Layout
defineOptions({ layout: AuthenticatedLayout })

// 2. Definisi Props
const props = defineProps<{
  roles: {
    data: Array<{
      id: number
      name: string
      guard_name: string
      users_count?: number // Opsional: jika kamu meload count relasi di controller
      created_at: string
    }>
    links: Array<{
      url: string | null
      label: string
      active: boolean
    }>
    from: number
    to: number
    total: number
  },
  filters: {
    search: string
  }
}>()

// 3. Logika Pencarian (Search)
const search = ref(props.filters.search || '')

let timeout: ReturnType<typeof setTimeout>
watch(search, (value) => {
  clearTimeout(timeout)
  timeout = setTimeout(() => {
    router.get(
      route('roles.index'),
      { search: value },
      { preserveState: true, replace: true }
    )
  }, 500)
})

const clearSearch = () => {
  search.value = ''
}

// 4. Helper Formatter
const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  })
}

const cleanLabel = (label: string) => {
  if (label.includes('Previous')) return 'Sebelumnya'
  if (label.includes('Next')) return 'Selanjutnya'
  return label
}
</script>

<template>
  <Head title="Manajemen Jabatan" />

  <div class="flex flex-col gap-4 p-4 md:p-8 pt-4">

    <Card class="border-none shadow-sm">
      <CardHeader class="flex flex-col md:flex-row items-start md:items-center justify-between space-y-4 md:space-y-0 pb-6">
        <div class="flex items-center gap-2">
          <div class="p-2 bg-primary/10 rounded-lg">
            <IconHierarchy class="size-5 text-primary" />
          </div>
          <CardTitle class="text-xl font-bold">Daftar Jabatan (Role)</CardTitle>
        </div>

        <div class="flex items-center gap-2 w-full md:w-auto">
          <div class="relative w-full md:w-72">
            <IconSearch class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
            <Input
              v-model="search"
              placeholder="Cari jabatan..."
              class="pl-10 pr-10"
            />
            <button
              v-if="search"
              @click="clearSearch"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
            >
              <IconX class="size-4" />
            </button>
          </div>

          <Button as-child>
            <Link :href="route('roles.create')">
              <IconPlus class="mr-2 size-4" />
              <span class="hidden sm:inline">Tambah Jabatan</span>
            </Link>
          </Button>
        </div>
      </CardHeader>

      <CardContent>
        <div class="rounded-md border">
          <Table>
            <TableHeader>
              <TableRow class="bg-muted/50">
                <TableHead class="w-[50px]">ID</TableHead>
                <TableHead>Nama Jabatan</TableHead>
                <TableHead>Guard</TableHead>
                <TableHead class="text-center">Jumlah Pengguna</TableHead>
                <TableHead>Tgl Dibuat</TableHead>
                <TableHead class="text-right">Aksi</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-if="roles.data.length === 0">
                <TableCell colspan="6" class="h-24 text-center text-muted-foreground">
                  Data jabatan tidak ditemukan.
                </TableCell>
              </TableRow>

              <TableRow v-for="role in roles.data" :key="role.id">
                <TableCell class="text-muted-foreground font-mono text-xs">#{{ role.id }}</TableCell>
                <TableCell class="font-semibold text-primary">
                  {{ role.name }}
                </TableCell>
                <TableCell>
                  <span class="text-xs px-2 py-0.5 rounded-full bg-secondary text-secondary-foreground font-medium">
                    {{ role.guard_name }}
                  </span>
                </TableCell>
                <TableCell class="text-center">
                  <span class="font-medium">{{ role.users_count ?? 0 }}</span>
                </TableCell>
                <TableCell>{{ formatDate(role.created_at) }}</TableCell>
                <TableCell class="text-right space-x-1">
                  <Button variant="ghost" size="icon" class="size-8" as-child>
                    <Link :href="route('roles.edit', role.id)">
                        <IconEye class="size-4 text-destructive" />
                    </Link>
                  </Button>

                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>

        <div class="flex flex-col md:flex-row items-center justify-between gap-4 mt-6">
          <p class="text-xs text-muted-foreground">
            Menampilkan {{ roles.from ?? 0 }} - {{ roles.to ?? 0 }} dari {{ roles.total }} jabatan
          </p>

          <nav class="flex items-center gap-1">
            <template v-for="(link, k) in roles.links" :key="k">
              <Button
                v-if="link.url === null"
                variant="outline"
                size="sm"
                disabled
                class="opacity-50 text-xs px-3 h-8"
                v-html="cleanLabel(link.label)"
              />
              <Button
                v-else
                as-child
                variant="outline"
                size="sm"
                class="text-xs px-3 h-8"
                :class="{ 'bg-primary text-primary-foreground hover:bg-primary/90 hover:text-primary-foreground': link.active }"
              >
                <Link :href="link.url" v-html="cleanLabel(link.label)" />
              </Button>
            </template>
          </nav>
        </div>
      </CardContent>
    </Card>
  </div>
</template>
