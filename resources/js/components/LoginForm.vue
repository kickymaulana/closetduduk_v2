<script setup lang="ts">
import { watch, type HTMLAttributes } from "vue";
import { cn } from "@/lib/utils";
import { Button } from "@/components/ui/button";
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from "@/components/ui/card";
import {
    Field,
    FieldGroup,
    FieldLabel,
} from "@/components/ui/field";
import { Input } from "@/components/ui/input";
import { useForm } from "@inertiajs/vue3";

// Tambahkan 'externalUsername' di props
const props = defineProps<{
    class?: HTMLAttributes["class"];
    externalUsername?: string;
}>();

const form = useForm({
    username: "",
    password: "",
    remember: false,
});

// Watcher: Jika user di list diklik, isi form.username secara otomatis
watch(() => props.externalUsername, (newValue) => {
    if (newValue) {
        form.username = newValue;
    }
});

const submit = () => {
    form.post(route("login.store"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <div :class="cn('flex flex-col gap-6', props.class)">
        <Card class="bg-white/40 dark:bg-black/30 backdrop-blur-3xl border-primary/20 dark:border-white/5 shadow-2xl rounded-2xl">
            <CardHeader class="text-center">
                <CardTitle class="text-2xl font-bold text-foreground">Akses Masuk</CardTitle>
                <CardDescription class="text-foreground/70 font-medium">
                    Masukkan kredensial untuk masuk ke sistem.
                </CardDescription>
            </CardHeader>
            <CardContent>
                <form @submit.prevent="submit" class="space-y-4">
                    <FieldGroup class="gap-4">
                        <Field>
                            <FieldLabel class="font-semibold text-primary dark:text-primary-foreground/90">
                                Username
                            </FieldLabel>
                            <Input
                                id="username"
                                v-model="form.username"
                                type="text"
                                placeholder="Username atau email"
                                class="bg-white/70 dark:bg-black/20 border-border focus:border-primary focus:ring-primary h-11"
                                required
                            />
                            <p v-if="form.errors.username" class="text-destructive text-xs mt-1 italic">
                                {{ form.errors.username }}
                            </p>
                        </Field>

                        <Field>
                            <div class="flex items-center justify-between">
                                <FieldLabel class="font-semibold text-primary dark:text-primary-foreground/90">
                                    Password
                                </FieldLabel>
                                <a href="#" class="text-xs text-primary hover:text-accent font-medium hover:underline transition-colors">
                                    Lupa sandi?
                                </a>
                            </div>
                            <Input
                                id="password"
                                type="password"
                                v-model="form.password"
                                class="bg-white/70 dark:bg-black/20 border-border focus:border-primary focus:ring-primary h-11"
                                required
                            />
                            <p v-if="form.errors.password" class="text-destructive text-xs mt-1 italic">
                                {{ form.errors.password }}
                            </p>
                        </Field>

                        <Button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full font-bold shadow-xl transition-all duration-300 hover:bg-accent hover:text-accent-foreground text-lg h-12 rounded-xl active:scale-95"
                        >
                            {{ form.processing ? "Memproses..." : "MASUK KE SISTEM" }}
                        </Button>
                    </FieldGroup>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
