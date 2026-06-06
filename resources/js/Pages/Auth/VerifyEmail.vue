<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({ status: { type: String } });
const form = useForm({});
const submit = () => { form.post(route('verification.send')); };
const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
  <GuestLayout>
    <Head title="Email Verification" />

    <div class="mb-4">
      <p>Thanks for signing up! Please verify your email by clicking the link we sent. If you didn't receive it, request another.</p>
    </div>

    <div v-if="verificationLinkSent">
      <v-alert type="success" dense>A new verification link has been sent to your email.</v-alert>
    </div>

    <v-form @submit.prevent="submit">
      <div class="d-flex justify-space-between mt-4">
        <v-btn color="primary" :loading="form.processing" type="submit">Resend Verification Email</v-btn>

        <Link :href="route('logout')" method="post" as="button">Log Out</Link>
      </div>
    </v-form>
  </GuestLayout>
</template>
