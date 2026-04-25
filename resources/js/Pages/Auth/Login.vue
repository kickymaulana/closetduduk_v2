<script lang="ts">
export const description =
    "Fullscreen background login form with user selection.";
</script>

<script setup lang="ts">
import { ref } from "vue";
import LoginForm from "@/components/LoginForm.vue";
import { Button } from "@/components/ui/button";
import { useDark, useToggle } from "@vueuse/core";
import { IconSun, IconMoon, IconUserCircle } from "@tabler/icons-vue";

// Menerima props dari Laravel/Inertia
defineProps<{
    users: Array<{ id: number; name: string; username: string }>;
    backgroundImage: string;
}>();

const isDark = useDark();
const toggleDark = useToggle(isDark);

// State untuk menyimpan username yang diklik
const selectedUsername = ref("");

const handleSelectUser = (username: string) => {
    selectedUsername.value = username;
};
</script>

<template>
    <div
        class="relative flex min-h-svh w-full items-center justify-center p-6 md:p-10 bg-cover bg-center bg-no-repeat transition-all duration-500"
        style="background-image: url(&quot;images/bg-login.png&quot;)"
    >
        <div
            class="absolute inset-0 bg-blue-950/20 dark:bg-black/80 backdrop-blur-[1px]"
        ></div>

        <div class="absolute right-4 top-4 md:right-10 md:top-10 z-20">
            <Button
                variant="outline"
                size="icon"
                @click="toggleDark()"
                class="bg-white/30 dark:bg-black/40 backdrop-blur-md border-white/20 dark:border-white/10 shadow-lg group"
            >
                <IconSun
                    v-if="isDark"
                    class="size-5 text-accent transition-transform group-hover:rotate-90"
                />
                <IconMoon
                    v-else
                    class="size-5 text-primary transition-transform group-hover:-rotate-12"
                />
                <span class="sr-only">Toggle Theme</span>
            </Button>
        </div>

        <div
            class="relative z-10 w-full max-w-5xl flex flex-col lg:flex-row gap-12 items-center justify-center"
        >
            <div class="w-full max-w-md space-y-4">
                <div class="text-white mb-6">
                    <h2 class="text-2xl font-bold tracking-tight">
                        Pilih Akun
                    </h2>
                    <p class="text-sm opacity-80 font-medium">
                        Klik pada nama untuk mengisi otomatis.
                    </p>
                </div>

                <div
                    class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar"
                >
                    <button
                        v-for="user in users"
                        :key="user.id"
                        @click="handleSelectUser(user.username)"
                        class="flex items-center gap-3 p-3 rounded-xl bg-white/10 hover:bg-white/30 border border-white/10 hover:border-white/40 transition-all text-white text-left group"
                    >
                        <div
                            class="bg-primary/20 p-2 rounded-lg group-hover:bg-primary transition-colors"
                        >
                            <IconUserCircle class="size-6 text-white" />
                        </div>
                        <div class="truncate">
                            <p class="font-bold text-sm truncate">
                                {{ user.name }}
                            </p>
                            <p class="text-[10px] opacity-60 tracking-wider">
                                @{{ user.username }}
                            </p>
                        </div>
                    </button>
                </div>
            </div>

            <div class="w-full max-w-sm">
                <div
                    class="mb-10 text-center drop-shadow-[0_2px_10px_rgba(0,0,0,0.5)]"
                >
                    <h1
                        class="text-4xl font-extrabold tracking-tighter bg-gradient-to-r from-primary via-accent to-primary bg-clip-text text-transparent"
                    >
                        CLOSET DUDUK <span class="text-accent">V2</span>
                    </h1>
                    <p
                        class="text-sm font-semibold text-white uppercase tracking-widest mt-1.5 opacity-90"
                    >
                        Production Control System
                    </p>
                </div>

                <LoginForm
                    :externalUsername="selectedUsername"
                    class="shadow-[0_25px_70px_-15px_rgba(132,204,22,0.3)] border-none"
                />
            </div>
        </div>

        <div
            class="absolute bottom-6 text-[10px] md:text-xs font-semibold text-white/90 z-10 tracking-wide uppercase bg-black/30 px-3 py-1.5 rounded-full backdrop-blur-sm"
        >
            &copy; 2026 PT Mark Dynamics Indonesia Tbk. All rights reserved.
        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 10px;
}
</style>
