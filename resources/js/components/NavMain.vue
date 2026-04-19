<script setup lang="ts">
import type { Component } from "vue";
import { IconCirclePlusFilled, IconMail } from "@tabler/icons-vue";
import { Link } from "@inertiajs/vue3"; // Import Link

import { Button } from "@/components/ui/button";
import {
    SidebarGroup,
    SidebarGroupContent,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from "@/components/ui/sidebar";

// Update Interface agar sesuai dengan data dari AppSidebar
interface NavItem {
    title: string;
    url: string;
    icon?: Component;
    root?: string; // Tambahkan ini agar tidak error TS
}

defineProps<{
    items: NavItem[];
}>();
</script>

<template>
    <SidebarGroup>
        <SidebarGroupContent class="flex flex-col gap-2">
            <SidebarMenu>
                <SidebarMenuItem v-for="item in items" :key="item.title">
                    <SidebarMenuButton
                        :tooltip="item.title"
                        as-child
                        :is-active="$page.component.startsWith(item.root ?? '')"
                    >
                        <Link :href="item.url">
                            <component :is="item.icon" v-if="item.icon" />
                            <span>{{ item.title }}</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarGroupContent>
    </SidebarGroup>
</template>
