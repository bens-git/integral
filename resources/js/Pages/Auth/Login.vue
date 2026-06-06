<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

defineProps({
  canResetPassword: { type: Boolean },
  status: { type: String },
});

const form = useForm({ email: '', password: '', remember: false });

const submit = () => {
  form.post(route('login'), { onFinish: () => form.reset('password') });
};
</script>

<template>
  <GuestLayout>
    <Head title="Log in" />

    <div v-if="status">
      <v-alert type="success" density="comfortable">{{ status }}</v-alert>
    </div>

    <v-form @submit.prevent="submit">
      <v-text-field
        v-model="form.email"
        label="Email"
        type="email"
        :error="!!form.errors.email"
        :error-messages="form.errors.email"
        required
        autofocus
        autocomplete="username"
      />

      <v-text-field
        v-model="form.password"
        label="Password"
        type="password"
        :error="!!form.errors.password"
        :error-messages="form.errors.password"
        required
        autocomplete="current-password"
      />

      <v-checkbox
        v-model="form.remember"
        label="Remember me"
        class="mt-2"
      />

      <div class="d-flex justify-end align-center mt-4">
        <Link :href="route('register')" class="me-4">Register</Link>
        <Link v-if="canResetPassword" :href="route('password.request')" class="me-4">Forgot your password?</Link>
        <v-btn color="primary" type="submit" :loading="form.processing">Log in</v-btn>
      </div>
    </v-form>
  </GuestLayout>
</template>
