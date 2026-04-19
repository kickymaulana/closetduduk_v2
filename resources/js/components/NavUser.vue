<script setup lang="ts">
import {
    IconCreditCard,
    IconDotsVertical,
    IconLogout,
    IconNotification,
    IconUserCircle,
} from "@tabler/icons-vue";

import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import {
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    useSidebar,
} from "@/components/ui/sidebar";

import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from "@/components/ui/alert-dialog";
import { Button } from "@/components/ui/button";
import { Link } from "@inertiajs/vue3";

interface User {
    name: string;
    email: string;
    avatar: string;
}

defineProps<{
    user: User;
}>();

const { isMobile } = useSidebar();

// Fungsi untuk mengambil inisial (Contoh: John Doe -> JD)
const getInitials = (name: string) => {
    return name
        .split(" ")
        .map((n) => n[0])
        .join("")
        .toUpperCase()
        .substring(0, 2);
};
</script>

<template>
    <SidebarMenu>
        <SidebarMenuItem>
            <AlertDialog>
                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <SidebarMenuButton
                            size="lg"
                            class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground"
                        >
                            <Avatar class="h-8 w-8 rounded-lg grayscale">
                                <AvatarImage
                                    :src="user.avatar ?? ''"
                                    :alt="user.name"
                                />
                                <AvatarFallback class="rounded-lg">
                                    {{ getInitials(user.name) }}
                                </AvatarFallback>
                            </Avatar>
                            <div
                                class="grid flex-1 text-left text-sm leading-tight"
                            >
                                <span class="truncate font-medium">{{
                                    user.name
                                }}</span>
                                <span
                                    class="text-muted-foreground truncate text-xs"
                                >
                                    {{ user.email }}
                                </span>
                            </div>
                            <IconDotsVertical class="ml-auto size-4" />
                        </SidebarMenuButton>
                    </DropdownMenuTrigger>

                    <DropdownMenuContent
                        class="w-(--reka-dropdown-menu-trigger-width) min-w-56 rounded-lg"
                        :side="isMobile ? 'bottom' : 'right'"
                        :side-offset="4"
                        align="end"
                    >
                        <DropdownMenuLabel class="p-0 font-normal">
                            <div
                                class="flex items-center gap-2 px-1 py-1.5 text-left text-sm"
                            >
                                <Avatar class="h-8 w-8 rounded-lg">
                                    <AvatarImage
                                        :src="user.avatar ?? ''"
                                        :alt="user.name"
                                    />
                                    <AvatarFallback class="rounded-lg">
                                        {{ getInitials(user.name) }}
                                    </AvatarFallback>
                                </Avatar>
                                <div
                                    class="grid flex-1 text-left text-sm leading-tight"
                                >
                                    <span class="truncate font-medium">{{
                                        user.name
                                    }}</span>
                                    <span
                                        class="text-muted-foreground truncate text-xs"
                                    >
                                        {{ user.email }}
                                    </span>
                                </div>
                            </div>
                        </DropdownMenuLabel>

                        <DropdownMenuSeparator />

                        <DropdownMenuGroup>
                            <DropdownMenuItem as-child>
                                <Link :href="route('profile.edit')">
                                    <IconUserCircle class="mr-2 size-4" />
                                    Account
                                </Link>
                            </DropdownMenuItem>
                            <!-- <DropdownMenuItem> -->
                            <!--     <IconNotification /> -->
                            <!--     Notifications -->
                            <!-- </DropdownMenuItem> -->
                        </DropdownMenuGroup>

                        <DropdownMenuSeparator />

                        <AlertDialogTrigger as-child>
                            <DropdownMenuItem @select.prevent>
                                <IconLogout class="size-4" />
                                Log out
                            </DropdownMenuItem>
                        </AlertDialogTrigger>
                    </DropdownMenuContent>
                </DropdownMenu>

                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>Keluar?</AlertDialogTitle>
                        <AlertDialogDescription>
                            Apakah anda yakin ingin keluar dari aplikasi
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel>Cancel</AlertDialogCancel>
                        <AlertDialogAction as-child>
                            <Link
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                Ya, Keluar
                            </Link>
                        </AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </SidebarMenuItem>
    </SidebarMenu>
</template>
