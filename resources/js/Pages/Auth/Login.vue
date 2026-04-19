<script lang="ts">
export const description = "Fullscreen background login form.";
</script>

<script setup lang="ts">
import LoginForm from "@/components/LoginForm.vue";
import { Button } from "@/components/ui/button";
import { useDark, useToggle } from "@vueuse/core";
import { IconSun, IconMoon } from "@tabler/icons-vue";

const isDark = useDark();
const toggleDark = useToggle(isDark);
</script>

<template>
    <div
        class="relative flex min-h-svh w-full items-center justify-center p-6 md:p-10 bg-cover bg-center bg-no-repeat transition-all duration-500"
        style="
            background-image: url(&quot;https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?q=80&w=2064&auto=format&fit=crop&quot;);
        "
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

        <div class="relative z-10 w-full max-w-sm">
            <div
                class="mb-12 text-center drop-shadow-[0_2px_10px_rgba(0,0,0,0.5)]"
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
                class="shadow-[0_25px_70px_-15px_rgba(132,204,22,0.3)] border-none"
            />
        </div>

        <div
            class="absolute bottom-6 text-xs font-semibold text-white/90 z-10 tracking-wide uppercase bg-black/30 px-3 py-1.5 rounded-full backdrop-blur-sm"
        >
            &copy; 2026 PT Mark Dynamics Indonesia Tbk. All rights reserved.
        </div>
    </div>
</template>
