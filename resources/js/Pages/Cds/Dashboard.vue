<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const stats = computed(() => page.props.stats || {});
const recent = computed(() => page.props.recentActivity || []);

const statCards = computed(() => {
  const s = stats.value || {};
  return [
    { key: 'active_proposals', label: 'Active Proposals', value: s.active_proposals ?? 0, href: route('cds.proposals.index') + '?status=active' },
    { key: 'issues_in_deliberation', label: 'Issues in Deliberation', value: s.issues_in_deliberation ?? 0, href: route('cds.issues.index') + '?status=deliberation' },
    { key: 'consensus_reached', label: 'Consensus Reached', value: s.consensus_reached ?? 0, href: route('cds.issues.index') + '?status=decided' },
    { key: 'pending_decisions', label: 'Pending Decisions', value: s.pending_decisions ?? 0, href: route('cds.decisions.index') },
  ];
});
</script>

<template>
  <Head title="CDS Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">CDS Dashboard</h2>
    </template>

    <v-container class="py-8">
      <v-row>
        <v-col cols="12">
          <v-card class="pa-4 mb-6">
            <div class="d-flex justify-space-between align-center">
              <div>
                <h3 class="text-h6">Collaborative Decision System</h3>
                <div class="text-subtitle-2">Overview of CDS activity and quick actions</div>
              </div>
              <div>
                <v-btn small text :href="route('cds.proposals.create')">New Proposal</v-btn>
                <v-btn small text :href="route('cds.issues.create')">New Issue</v-btn>
              </div>
            </div>
          </v-card>
        </v-col>

        <v-col cols="12">
          <v-row>
            <v-col cols="12" sm="6" md="3" v-for="card in statCards" :key="card.key">
              <v-card class="pa-4">
                <div class="d-flex align-center justify-space-between">
                  <div>
                    <div class="text-subtitle-2 text--secondary">{{ card.label }}</div>
                    <div class="text-h5 font-weight-medium">{{ card.value }}</div>
                  </div>
                  <div>
                    <v-btn small icon :href="card.href">
                      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </v-btn>
                  </div>
                </div>
              </v-card>
            </v-col>
          </v-row>
        </v-col>

        <v-col cols="12" md="8">
          <v-card class="pa-4">
            <h3 class="text-h6 mb-3">Recent Activity</h3>
            <v-list two-line>
              <v-list-item v-for="item in recent" :key="item.id">
                <div class="v-list-item-content">
                  <div class="font-medium">{{ item.title }}</div>
                  <div class="text-caption">{{ item.action }} • {{ item.time }}</div>
                </div>
                <div class="v-list-item-action">
                  <v-btn icon :href="item.type === 'proposal' ? route('cds.proposals.show', item.id) : route('cds.deliberation.show', item.id)">
                    <v-icon>mdi-open-in-new</v-icon>
                  </v-btn>
                </div>
              </v-list-item>
              <v-list-item v-if="!recent || recent.length === 0">
                <div class="v-list-item-content">No recent activity</div>
              </v-list-item>
            </v-list>
          </v-card>
        </v-col>

        <v-col cols="12" md="4">
          <v-card class="pa-4 mb-4">
            <h3 class="text-h6 mb-2">Status Breakdown</h3>
            <v-list dense>
              <v-list-item v-for="(count, status) in stats.proposals_by_status || {}" :key="status">
                <v-list-item-content>
                  <div class="d-flex justify-space-between">
                    <div class="text-caption">{{ status }}</div>
                    <div class="font-medium">{{ count }}</div>
                  </div>
                </v-list-item-content>
              </v-list-item>
              <v-list-item v-if="!stats.proposals_by_status || Object.keys(stats.proposals_by_status || {}).length === 0">
                <v-list-item-content>No data</v-list-item-content>
              </v-list-item>
            </v-list>
          </v-card>

          <v-card class="pa-4">
            <h3 class="text-h6 mb-2">Quick Links</h3>
            <v-btn block color="primary" class="mb-2" :href="route('cds.proposals.create')">New Proposal</v-btn>
            <v-btn block outlined :href="route('cds.issues.index')">All Issues</v-btn>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </AuthenticatedLayout>
</template>

