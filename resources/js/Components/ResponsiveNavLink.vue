<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({ href: { type: String, required: true }, active: { type: Boolean } });
</script>

<template>
  <Link :href="href" style="text-decoration:none; color:inherit; width:100%; display:block">
    <v-list-item :active="active">
      <v-list-item-title>
        <slot />
      </v-list-item-title>
    </v-list-item>
  </Link>
</template>
