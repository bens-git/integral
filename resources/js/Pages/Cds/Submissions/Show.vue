<template>
  <Head :title="submission?.title || 'Submission'" />
  <AuthenticatedLayout>
    <template #header>
      <div>
        <h2 class="text-h5">{{ submission?.title || 'Submission' }}</h2>
        <div class="text-subtitle-2">Submission details</div>
      </div>
    </template>

    <v-container class="py-6">
      <v-card class="pa-4">
        <div class="mb-4">
          <div class="text-caption">Submitted by {{ submission?.submitter?.name || '—' }}</div>
          <div class="text-caption">Status: {{ submission?.status || '—' }}</div>
        </div>

        <div>
          <div v-if="submission?.description" style="white-space:pre-wrap">{{ submission.description }}</div>
          <div v-else class="text-caption">No description</div>
        </div>

        <v-divider class="my-4" />
        <v-btn text @click="goBack">Back to submissions</v-btn>
      </v-card>
    </v-container>
  </AuthenticatedLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, usePage, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const page = usePage()
const submission = computed(() => page.props.submission ?? null)

function goBack() {
  let href = route('cds.submissions.index');
  try {
    const url = new URL(href);
    href = url.pathname + url.search + url.hash;
  } catch (e) {
    // leave href as-is if URL parsing fails
  }
  router.visit(href);
}

</script>
