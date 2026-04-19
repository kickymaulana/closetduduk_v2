<script setup lang="ts">
import { Button } from "@/components/ui/button";
import { Separator } from "@/components/ui/separator";
import { SidebarTrigger } from "@/components/ui/sidebar";
import { useDark, useToggle } from "@vueuse/core";
import { IconSun, IconMoon } from "@tabler/icons-vue";

// Logika dark mode: otomatis mendeteksi sistem & menambah/menghapus class 'dark'
const isDark = useDark();
const toggleDark = useToggle(isDark);
</script>

<template>
    <header
        class="flex h-(--header-height) shrink-0 items-center gap-2 border-b transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-(--header-height)"
    >
        <div class="flex w-full items-center gap-1 px-4 lg:gap-2 lg:px-6">
            <SidebarTrigger class="-ml-1" />
            <Separator
                orientation="vertical"
                class="mx-2 data-[orientation=vertical]:h-4"
            />
            <div class="ml-auto flex items-center gap-2">
                <Button
                    variant="ghost"
                    as-child
                    size="sm"
                    class="hidden sm:flex"
                >
                    <a
                        href="#"
                        rel="noopener noreferrer"
                        target="_blank"
                        class="dark:text-foreground"
                    >
                        Closet Duduk V2
                    </a>
                </Button>
                <Button variant="ghost" size="icon" @click="toggleDark()">
                    <IconSun v-if="isDark" class="size-5 text-fuchsia-400" />
                    <IconMoon v-else class="size-5 text-fuchsia-600" />
                    <span class="sr-only">Toggle Theme</span>
                </Button>
            </div>
        </div>
    </header>
</template>
