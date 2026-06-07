<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import ProposalDialog from '@/Components/ProposalDialog.vue'
import StoreProposalDialog from '@/Components/StoreProposalDialog.vue'

defineProps({
  proposals: Object,
  filters: Object,
})

const statusColors = {
  draft: 'grey',
  submitted: 'blue',
  validated: 'green',
  framed: 'purple',
  active: 'amber',
  accepted: 'green',
  rejected: 'red',
  implemented: 'green',
  archived: 'grey',
}

function filterByStatus(status) {
  router.get(route('cds.proposals.index'), { status }, { preserveState: true })
}

function formatDate(date) {
  return new Date(date).toLocaleDateString()
}

function refresh() {
  router.get(route('cds.proposals.index'), {}, { preserveState: true })
}
</script>

<template>
  <Head title="Proposals" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-h5 font-weight-medium">Proposals</h2>
    </template>

    <v-container class="py-6">

      <!-- Filters -->
      <v-card class="mb-6 pa-4">
        <div class="d-flex flex-wrap align-center">
          <div class="me-4 text-caption">Filter by status:</div>

          <v-chip
            v-for="(color, status) in statusColors"
            :key="status"
            class="me-2"
            :color="color"
            variant="tonal"
            @click="filterByStatus(status)"
          >
            {{ status }}
          </v-chip>

          <v-chip
            v-if="filters?.status"
            class="me-2"
            variant="tonal"
            @click="filterByStatus(null)"
          >
            Clear
          </v-chip>

          <v-spacer />

          <StoreProposalDialog @proposal-created="refresh" />
        </div>
      </v-card>

      <!-- Table -->
      <v-card>
        <v-card-text>

          <v-table>
            <thead>
              <tr>
                <th>Title</th>
                <th>Category</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Submitted</th>
                <th>Actions</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="proposal in proposals.data" :key="proposal.id">
                <td>
                    {{ proposal.title }}
                </td>

                <td>{{ proposal.category }}</td>

                <td>
                  <v-chip
                    :color="statusColors[proposal.status] || 'grey'"
                    variant="tonal"
                  >
                    {{ proposal.status }}
                  </v-chip>
                </td>

                <td>{{ proposal.priority }}</td>

                <td>{{ formatDate(proposal.created_at) }}</td>

                <td class="d-flex ga-2">
                  <ProposalDialog :proposal="proposal">
                    <template #activator="{ props }">
                      <v-btn size="small" variant="text" v-bind="props">View</v-btn>
                    </template>
                  </ProposalDialog>

                  
                </td>
              </tr>

              <tr v-if="!proposals.data.length">
                <td colspan="6" class="text-center pa-6">
                  No proposals found.
                  <StoreProposalDialog @proposal-created="refresh">
                    <template #activator="{ props }">
                      <v-btn text v-bind="props">Create one?</v-btn>
                    </template>
                  </StoreProposalDialog>
                </td>
              </tr>
            </tbody>
          </v-table>

          <!-- Pagination -->
          <div
            v-if="proposals.data.length"
            class="mt-4 d-flex align-center justify-space-between"
          >
            <div>
              <Link v-if="proposals.prev_page_url" :href="proposals.prev_page_url">
                <v-btn variant="text">Previous</v-btn>
              </Link>
            </div>

            <div class="text-caption">
              Page {{ proposals.current_page }} of {{ proposals.last_page }}
            </div>

            <div>
              <Link v-if="proposals.next_page_url" :href="proposals.next_page_url">
                <v-btn variant="text">Next</v-btn>
              </Link>
            </div>
          </div>

        </v-card-text>
      </v-card>

    </v-container>
  </AuthenticatedLayout>
</template>