<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

const props = defineProps({ email: { type: String, required: true }, token: { type: String, required: true } });

const form = useForm({ token: props.token, email: props.email, password: '', password_confirmation: '' });
const submit = () => { form.post(route('password.store'), { onFinish: () => form.reset('password', 'password_confirmation') }); };
</script>

<template>
  <GuestLayout>
    <Head title="Reset Password" />

    <v-form @submit.prevent="submit">
      <v-text-field v-model="form.email" label="Email" type="email" :error-messages="form.errors.email" required autofocus autocomplete="username" />

      <v-text-field v-model="form.password" label="Password" type="password" :error-messages="form.errors.password" required autocomplete="new-password" />

      <v-text-field v-model="form.password_confirmation" label="Confirm Password" type="password" :error-messages="form.errors.password_confirmation" required autocomplete="new-password" />

      <div class="d-flex justify-end mt-4">
        <v-btn color="primary" type="submit" :loading="form.processing">Reset Password</v-btn>
      </div>
    </v-form>
  </GuestLayout>
</template>
