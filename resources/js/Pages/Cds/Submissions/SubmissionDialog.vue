<template>
  <div>
    <v-dialog v-model="dialog" max-width="800">
      <template #activator="{ props: activatorProps }">
        <slot name="activator" v-bind="{ props: activatorProps }">
          <!-- fallback activator -->
          <v-btn v-bind="activatorProps">View</v-btn>
        </slot>
      </template>

      <v-card>
        <v-card-title class="d-flex justify-space-between align-center">
          <div>
            <div class="text-h6">{{ proposal.title }}</div>
            <div class="text-caption text-medium-emphasis">{{ formattedDate }}</div>
          </div>
          <v-chip :color="proposal.status ? statusColor : 'grey'" variant="tonal" class="ma-0">{{ proposal.status }}</v-chip>
        </v-card-title>

        <v-card-text>
          <div class="mb-4">
            <strong>Category:</strong> {{ proposal.category || '-' }}
            <span class="mx-2">•</span>
            <strong>Priority:</strong> {{ proposal.priority || '-' }}
          </div>

          <div class="mb-4">
            <div v-if="proposal.description">{{ proposal.description }}</div>
            <div v-else class="text-medium-emphasis">No description provided.</div>
          </div>

          <div v-if="proposal.meta" class="text-caption text-medium-emphasis">
            <!-- show any meta fields if present -->
            {{ proposal.meta }}
          </div>
        </v-card-text>

        <v-card-actions>
          <v-spacer />
          <v-btn text @click="dialog = false">Close</v-btn>
          <v-btn color="primary" :href="route('cds.submissions.show', proposal.id)"}>Open</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  proposal: Object,
});

const dialog = ref(false);

const formattedDate = computed(() => {
  if (!props.proposal || !props.proposal.created_at) return '';
  return new Date(props.proposal.created_at).toLocaleString();
});

const statusColor = computed(() => {
  const s = (props.proposal && props.proposal.status) || '';
  switch (s) {
    case 'draft': return 'grey';
    case 'submitted': return 'blue';
    case 'validated': return 'green';
    case 'framed': return 'purple';
    case 'active': return 'amber';
    case 'accepted': return 'green';
    case 'rejected': return 'red';
    default: return 'grey';
  }
});
</script>
