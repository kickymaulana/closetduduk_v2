<script setup lang="ts">
import type { HTMLAttributes } from "vue";
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
    FieldDescription,
    FieldGroup,
    FieldLabel,
} from "@/components/ui/field";
import { Input } from "@/components/ui/input";
import { useForm } from "@inertiajs/vue3";

const props = defineProps<{
    class?: HTMLAttributes["class"];
}>();

// 2. Inisialisasi Form Inertia
const form = useForm({
    username: "",
    password: "",
    remember: false,
});

// 3. Fungsi Submit
const submit = () => {
    form.post(route("login.store"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <div :class="cn('flex flex-col gap-6', props.class)">
        <Card
            class="bg-white/30 dark:bg-black/20 backdrop-blur-xl border-white/20 dark:border-white/5 shadow-2xl"
        >
            <CardHeader>
                <CardTitle>Closet Duduk V2</CardTitle>
                <CardDescription>
                    Masukan username untuk masuk ke aplikasi
                </CardDescription>
            </CardHeader>
            <CardContent>
                <form @submit.prevent="submit">
                    <FieldGroup>
                        <Field>
                            <FieldLabel for="email"> Username </FieldLabel>
                            <Input
                                id="username"
                                v-model="form.username"
                                type="text"
                                placeholder="m@example.com"
                                required
                            />
                            <p
                                v-if="form.errors.username"
                                class="text-destructive text-xs mt-1"
                            >
                                {{ form.errors.username }}
                            </p>
                        </Field>
                        <Field>
                            <div class="flex items-center">
                                <FieldLabel for="password">
                                    Password
                                </FieldLabel>
                                <a
                                    href="#"
                                    class="ml-auto inline-block text-sm underline-offset-4 hover:underline"
                                >
                                    Lupa password?
                                </a>
                            </div>
                            <Input
                                id="password"
                                type="password"
                                required
                                v-model="form.password"
                            />
                            <p
                                v-if="form.errors.password"
                                class="text-destructive text-xs mt-1"
                            >
                                {{ form.errors.password }}
                            </p>
                        </Field>
                        <Field>
                            <Button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full"
                            >
                                {{
                                    form.processing ? "Signing in..." : "Login"
                                }}
                            </Button>
                            <!--<Button variant="outline" type="button">
                Login with Google
              </Button>-->
                            <!--<FieldDescription class="text-center">
                Don't have an account?
                <a href="#">
                  Sign up
                </a>
              </FieldDescription>-->
                        </Field>
                    </FieldGroup>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
