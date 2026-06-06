<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({ password: '' });
const submit = () => { form.post(route('password.confirm'), { onFinish: () => form.reset() }); };
</script>

<template>
  <GuestLayout>
    <Head title="Confirm Password" />

    <div class="mb-4">
      <p>This is a secure area. Please confirm your password before continuing.</p>
    </div>

    <v-form @submit.prevent="submit">
      <v-text-field v-model="form.password" label="Password" type="password" :error-messages="form.errors.password" required autocomplete="current-password" autofocus />

      <div class="d-flex justify-end mt-4">
        <v-btn color="primary" type="submit" :loading="form.processing">Confirm</v-btn>
      </div>
    </v-form>
  </GuestLayout>
</template>
