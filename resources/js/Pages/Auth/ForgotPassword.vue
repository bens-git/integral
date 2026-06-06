<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

defineProps({ status: { type: String } });

const form = useForm({ email: '' });
const submit = () => { form.post(route('password.email')); };
</script>

<template>
  <GuestLayout>
    <Head title="Forgot Password" />

    <div class="mb-4">
      <p>Forgot your password? No problem. Enter your email and we'll send a reset link.</p>
    </div>

    <div v-if="status">
      <v-alert type="success" density="comfortable">{{ status }}</v-alert>
    </div>

    <v-form @submit.prevent="submit">
      <v-text-field v-model="form.email" label="Email" type="email" :error-messages="form.errors.email" required autofocus autocomplete="username" />

      <div class="d-flex justify-end mt-4">
        <v-btn color="primary" type="submit" :loading="form.processing">Email Password Reset Link</v-btn>
      </div>
    </v-form>
  </GuestLayout>
</template>
