<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
  <v-container fluid class="fill-height d-flex align-center justify-center">
    <v-row>
      <v-col cols="12" md="8" lg="6" class="mx-auto">
        <v-card class="pa-6">
          <div class="text-center mb-4">
            <Link href="/"><ApplicationLogo width="80" height="80" /></Link>
          </div>

          <slot />
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>
