<template>
    <v-container class="fill-height d-flex align-center justify-center py-12">
        <v-row>
            <v-col cols="12" md="6" class="mx-auto">
                <v-card class="pa-6">
                    <div class="d-flex justify-center align-center mb-4">
                        <ApplicationLogo width="72" height="72" />
                    </div>

                    <Head title="Demo Login" />

                    <h2 class="text-h5 text-center mb-2">Demo Login</h2>
                    <p class="text-center text-subtitle-1 mb-6">
                        Pick a nickname to login instantly for demo/testing. If
                        the nickname does not exist a demo user will be
                        provisioned.
                    </p>

                    <form @submit.prevent="submit">
                        <v-text-field
                            v-model="form.nickname"
                            label="Nickname"
                            outlined
                            dense
                            required
                            :error="!!form.errors.nickname"
                            :error-messages="form.errors.nickname"
                        />

                        <div class="d-flex justify-center mt-4">
                            <v-btn
                                color="primary"
                                type="submit"
                                :loading="form.processing"
                                :disabled="form.processing"
                                >Sign in as demo user</v-btn
                            >
                        </div>
                    </form>

                    <div class="text-center mt-4 text-caption text--secondary">
                        Passwords are not required for demo users. All demo
                        accounts are prefixed with "demo+" in the email.
                    </div>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script setup>
import { computed } from "vue";
import { Head, useForm } from "@inertiajs/vue3";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";

const form = useForm({ nickname: "" });

const firstError = computed(() => {
    const vals = Object.values(form.errors || {});
    if (!vals.length) return null;
    const v = vals[0];
    return Array.isArray(v) ? v[0] : v;
});

function submit() {
    if (!form.nickname || form.nickname.trim().length === 0) return;

    form.post(route("demo.login.submit"), {
        onBefore: () => {},
        onStart: () => {},
        onSuccess: () => {},
        onError: () => {},
        onFinish: () => {},
    });
}
</script>

<style scoped>
.v-card {
    border-radius: 10px;
}
</style>
