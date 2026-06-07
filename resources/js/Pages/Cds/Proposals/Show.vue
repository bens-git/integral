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
        <v-btn text @click="goBack">Back to proposals</v-btn>
      </v-card>
    </v-container>
  </AuthenticatedLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, usePage, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const page = usePage()
const proposal = computed(() => page.props.proposal ?? null)

function goBack() {
  let href = route('cds.proposals.index');
  try {
    const url = new URL(href);
    href = url.pathname + url.search + url.hash;
  } catch (e) {
    // leave href as-is if URL parsing fails
  }
  router.visit(href);
}

</script>
