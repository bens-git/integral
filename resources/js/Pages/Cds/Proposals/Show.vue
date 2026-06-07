<template>
  <Head :title="proposal?.title || 'Proposal'" />
  <AuthenticatedLayout>
    <template #header>
      <div>
        <h2 class="text-h5">{{ proposal?.title || 'Proposal' }}</h2>
        <div class="text-subtitle-2">Proposal details</div>
      </div>
    </template>

    <v-container class="py-6">
      <v-card class="pa-4">
        <div class="mb-4">
          <div class="text-caption">Submitted by {{ proposal?.submitter?.name || '—' }}</div>
          <div class="text-caption">Status: {{ proposal?.status || '—' }}</div>
        </div>

        <div>
          <div v-if="proposal?.description" style="white-space:pre-wrap">{{ proposal.description }}</div>
          <div v-else class="text-caption">No description</div>
        </div>

        <v-divider class="my-4" />
        <Link :href="route('cds.proposals.index')">
          <v-btn text>Back to proposals</v-btn>
        </Link>
      </v-card>
    </v-container>
  </AuthenticatedLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, usePage, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const page = usePage()
const proposal = computed(() => page.props.proposal ?? null)
</script>
