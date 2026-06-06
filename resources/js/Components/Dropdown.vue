<script setup>
import { ref } from 'vue';

const props = defineProps({
  align: { type: String, default: 'right' },
  width: { type: String, default: '48' },
  contentClasses: { type: String, default: '' },
});

const open = ref(false);
</script>

<template>
  <v-menu v-model="open" :close-on-content-click="true" :offset-y="true">
    <template #activator="{ props: activatorProps }">
      <div v-bind="activatorProps">
        <slot name="trigger" />
      </div>
    </template>

    <v-card :class="contentClasses">
      <v-list dense>
        <slot name="content" />
      </v-list>
    </v-card>
  </v-menu>
</template>
