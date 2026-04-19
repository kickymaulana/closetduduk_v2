<script setup lang="ts">
import type { Component } from "vue";

import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    useSidebar,
} from "@/components/ui/sidebar";
import { Link, usePage } from "@inertiajs/vue3";

interface Document {
    name: string;
    url: string;
    icon?: Component;
    root: string;
}

defineProps<{
    items: Document[];
}>();

const { isMobile } = useSidebar();
const page = usePage();
</script>

<template>
    <SidebarGroup class="group-data-[collapsible=icon]:hidden">
        <SidebarGroupLabel>Master</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in items" :key="item.name">
                <SidebarMenuButton
                    as-child
                    :is-active="page.component.startsWith(item.root)"
                    class="data-[active=true]:bg-primary/10 data-[active=true]:text-primary"
                >
                    <Link :href="item.url">
                        <component :is="item.icon" class="size-4" />
                        <span class="font-medium">{{ item.name }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
