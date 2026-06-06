<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({ href: { type: String, required: true } });
</script>

<template>
  <Link :href="href" style="text-decoration:none; color:inherit;">
    <v-list-item>
      <v-list-item-title>
        <slot />
      </v-list-item-title>
    </v-list-item>
  </Link>
</template>
