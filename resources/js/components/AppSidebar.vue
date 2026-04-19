<script setup lang="ts">
import {
    IconInnerShadowTop,
    IconUsers,
    IconHierarchy,
    IconGitMerge,
    IconAlertTriangle,
    IconGavel,
    IconClock,
} from "@tabler/icons-vue";

import Master from "@/components/Master.vue";
import NavMain from "@/components/NavMain.vue";
import NavUser from "@/components/NavUser.vue";
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from "@/components/ui/sidebar";
import { Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

// Ambil data auth dari shared props (HandleInertiaRequests.php)
const page = usePage();
const user = computed(() => page.props.auth.user);
const userRoles = computed(() => (page.props.auth as any).roles as string[]);

/**
 * Logika Hak Akses:
 * isAdminOrQC untuk menu Sampel, Formulir, dan Master Data
 */
const isAdminOrQC = computed(() => {
    return (
        userRoles.value.includes("admin") ||
        userRoles.value.includes("Quality Control")
    );
});

const filteredNavMain = computed(() => {
    let menus: any[] = [];

    menus.push({
        title: "Sesi Kerja",
        url: route("sesikerjas.index"),
        icon: IconClock,
        root: "SesiKerja",
    });
    return menus;
});

// Data untuk menu Master
const masterData = [
    {
        name: "Pengguna",
        url: route("users.index"),
        icon: IconUsers,
        root: "Master/Users",
    },
    {
        name: "Departemen",
        url: route("departemens.index"),
        icon: IconGitMerge,
        root: "Master/Departemens",
    },
    {
        name: "Cacat",
        url: route("cacats.index"),
        icon: IconAlertTriangle, // Import icon ini dari tabler
        root: "Master/Cacats",
    },
    {
        name: "Aturan Penolakan",
        url: route("aturanpenolakans.index"),
        icon: IconGavel,
        root: "Master/AturanPenolakans",
    },
    {
        name: "Jabatan",
        url: route("roles.index"),
        icon: IconHierarchy,
        root: "Master/Roles",
    },
];
</script>

<template>
    <Sidebar collapsible="offcanvas">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton
                        as-child
                        class="data-[slot=sidebar-menu-button]:!p-1.5"
                    >
                        <Link
                            :href="route('dashboard')"
                            as="button"
                            class="w-full text-left"
                        >
                            <IconInnerShadowTop class="!size-5" />
                            <span class="font-bold tracking-tight"
                                >Closet Duduk V2</span
                            >
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="filteredNavMain" />
            <Master v-if="isAdminOrQC" :items="masterData" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser :user="user" />
        </SidebarFooter>
    </Sidebar>
</template>
