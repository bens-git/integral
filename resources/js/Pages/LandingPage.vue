<template>
  <v-container class="fill-height d-flex align-center justify-center">
    <v-row>
      <v-col cols="12" md="8" lg="6" class="mx-auto">
        <v-card class="pa-8">
          <div class="text-center mb-4">
            <ApplicationLogo width="72" height="72" />
          </div>

          <Head title="Integral" />

          <h1 class="text-h4 text-center mb-2">Integral</h1>

          <p class="text-center mb-6">
            Integral is a collaborative decision system. Open the Consensus & Deliberation (CDS) module to frame issues, deliberate asynchronously, and build consensus.
          </p>

          <div class="text-center">
            <v-btn color="primary" @click="$inertia.visit(route('cds.dashboard'))">Open CDS</v-btn>
          </div>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Head } from '@inertiajs/vue3';
</script>

<style scoped>
.v-card { border-radius: 12px !important; }
</style>
