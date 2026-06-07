<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import ProposalDialog from '@/Components/ProposalDialog.vue'

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

          <Link :href="route('cds.proposals.create')">
            <v-btn color="primary">New Proposal</v-btn>
          </Link>
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
                  <Link :href="route('cds.proposals.show', proposal.id)">
                    {{ proposal.title }}
                  </Link>
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

                  <Link
                    v-if="proposal.status === 'draft'"
                    :href="route('cds.proposals.submit', proposal.id)"
                  >
                    <v-btn size="small" color="success" variant="text">
                      Submit
                    </v-btn>
                  </Link>
                </td>
              </tr>

              <tr v-if="!proposals.data.length">
                <td colspan="6" class="text-center pa-6">
                  No proposals found.
                  <Link :href="route('cds.proposals.create')">
                    Create one?
                  </Link>
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