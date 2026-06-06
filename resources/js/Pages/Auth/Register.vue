<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

const form = useForm({ name: '', email: '', password: '', password_confirmation: '' });

const submit = () => {
  form.post(route('register'), { onFinish: () => form.reset('password', 'password_confirmation') });
};
</script>

<template>
  <GuestLayout>
    <Head title="Register" />

    <v-form @submit.prevent="submit">
      <v-text-field v-model="form.name" label="Name" :error-messages="form.errors.name" required autofocus autocomplete="name" />

      <v-text-field v-model="form.email" label="Email" type="email" :error-messages="form.errors.email" required autocomplete="username" />

      <v-text-field v-model="form.password" label="Password" type="password" :error-messages="form.errors.password" required autocomplete="new-password" />

      <v-text-field v-model="form.password_confirmation" label="Confirm Password" type="password" :error-messages="form.errors.password_confirmation" required autocomplete="new-password" />

      <div class="d-flex justify-end mt-4">
        <Link :href="route('login')" class="me-4">Already registered?</Link>
        <v-btn color="primary" type="submit" :loading="form.processing">Register</v-btn>
      </div>
    </v-form>
  </GuestLayout>
</template>
