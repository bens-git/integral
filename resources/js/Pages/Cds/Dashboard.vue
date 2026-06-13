<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, usePage, router } from "@inertiajs/vue3";

import { computed } from "vue";
import StoreSubmissionDialog from '@/Components/StoreSubmissionDialog.vue';

function refresh() {
  // reload this page's props — fallback to full reload since Inertia core isn't available
  window.location.reload();
}

const page = usePage();
const stats = computed(() => page.props.stats || {});
const recent = computed(() => page.props.recentActivity || []);

function goProposals() {
    router.visit(route('cds.submissions.index'));
}

const statCards = computed(() => {
    const s = stats.value || {};
    return [
        {
            key: "active_proposals",
            label: "Active Submissions",
            value: s.active_proposals ?? 0,
            href: route("cds.submissions.index") + "?status=active",
        },
        {
            key: "issues_in_deliberation",
            label: "Issues in Deliberation",
            value: s.issues_in_deliberation ?? 0,
            href: route("cds.issues.index") + "?status=deliberation",
        },
        {
            key: "consensus_reached",
            label: "Consensus Reached",
            value: s.consensus_reached ?? 0,
            href: route("cds.issues.index") + "?status=decided",
        },
        {
            key: "pending_decisions",
            label: "Pending Decisions",
            value: s.pending_decisions ?? 0,
            href: route("cds.decisions.index"),
        },
    ];
});
</script>

<template>
    <Head title="CDS Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h1 class="text-h4 font-weight-bold">CDS Dashboard</h1>

                <div class="text-medium-emphasis mt-1">
                    Collaborative Decision System overview and activity
                    monitoring
                </div>
            </div>
        </template>

        <v-container fluid>
            <!-- Hero -->
            <v-card rounded="xl" elevation="2" class="mb-6 pa-6">
                <div
                    class="d-flex flex-wrap justify-space-between align-center"
                >
                    <div>
                        <div class="text-h5 font-weight-bold">
                            Collaborative Decision System
                        </div>

                        <div class="text-medium-emphasis">
                            Monitor proposals, deliberations, consensus and
                            decisions.
                        </div>
                    </div>

                    <div class="d-flex ga-2 mt-4 mt-md-0">
                        <v-btn variant="text" class="me-2" @click.prevent="goProposals">View Submissions</v-btn>
                        <StoreSubmissionDialog @submission-created="refresh" />
                    </div>
                </div>
            </v-card>

            <!-- Stats -->
            <v-row class="mb-4">
                <v-col cols="12" sm="6" md="3">
                    <v-card rounded="xl" elevation="1" class="pa-5">
                        <div class="d-flex justify-space-between">
                            <div>
                                <div class="text-caption text-medium-emphasis">
                                    Active Submissions
                                </div>

                                <div class="text-h4 font-weight-bold">
                                    {{ stats.active_proposals ?? 0 }}
                                </div>
                            </div>

                            <v-icon size="40" color="primary">
                                mdi-lightbulb-outline
                            </v-icon>
                        </div>
                    </v-card>
                </v-col>

                <v-col cols="12" sm="6" md="3">
                    <v-card rounded="xl" elevation="1" class="pa-5">
                        <div class="d-flex justify-space-between">
                            <div>
                                <div class="text-caption text-medium-emphasis">
                                    In Deliberation
                                </div>

                                <div class="text-h4 font-weight-bold">
                                    {{ stats.issues_in_deliberation ?? 0 }}
                                </div>
                            </div>

                            <v-icon size="40" color="warning">
                                mdi-forum-outline
                            </v-icon>
                        </div>
                    </v-card>
                </v-col>

                <v-col cols="12" sm="6" md="3">
                    <v-card rounded="xl" elevation="1" class="pa-5">
                        <div class="d-flex justify-space-between">
                            <div>
                                <div class="text-caption text-medium-emphasis">
                                    Consensus Reached
                                </div>

                                <div class="text-h4 font-weight-bold">
                                    {{ stats.consensus_reached ?? 0 }}
                                </div>
                            </div>

                            <v-icon size="40" color="success">
                                mdi-check-circle-outline
                            </v-icon>
                        </div>
                    </v-card>
                </v-col>

                <v-col cols="12" sm="6" md="3">
                    <v-card rounded="xl" elevation="1" class="pa-5">
                        <div class="d-flex justify-space-between">
                            <div>
                                <div class="text-caption text-medium-emphasis">
                                    Pending Decisions
                                </div>

                                <div class="text-h4 font-weight-bold">
                                    {{ stats.pending_decisions ?? 0 }}
                                </div>
                            </div>

                            <v-icon size="40" color="error">
                                mdi-clock-outline
                            </v-icon>
                        </div>
                    </v-card>
                </v-col>
            </v-row>

            <v-row>
                <!-- Activity -->
                <v-col cols="12" lg="8">
                    <v-card rounded="xl" elevation="1" class="pa-5">
                        <div class="text-h6 mb-4">Recent Activity</div>

                        <div v-if="recent.length > 0">
                            <v-timeline side="end" density="compact">
                                <v-timeline-item
                                    v-for="item in recent"
                                    :key="item.id"
                                    dot-color="primary"
                                    size="small"
                                >
                                    <div class="font-weight-medium">
                                        {{ item.title }}
                                    </div>

                                    <div class="text-caption">
                                        {{ item.action }}
                                    </div>

                                    <div
                                        class="text-caption text-medium-emphasis"
                                    >
                                        {{ item.time }}
                                    </div>
                                </v-timeline-item>
                            </v-timeline>
                        </div>

                        <div
                            v-else
                            class="text-medium-emphasis py-6 text-center"
                        >
                            No recent activity.
                        </div>
                    </v-card>
                </v-col>

                <!-- Status -->
                <v-col cols="12" lg="4">
                    <v-card rounded="xl" elevation="1" class="pa-5">
                        <div class="text-h6 mb-4">
                            Proposal Status Breakdown
                        </div>

                        <template
                            v-if="
                                stats.proposals_by_status &&
                                Object.keys(stats.proposals_by_status).length
                            "
                        >
                            <div
                                v-for="(
                                    count, status
                                ) in stats.proposals_by_status"
                                :key="status"
                                class="d-flex justify-space-between align-center mb-3"
                            >
                                <v-chip color="primary" variant="tonal">
                                    {{ status }}
                                </v-chip>

                                <span class="font-weight-bold">
                                    {{ count }}
                                </span>
                            </div>
                        </template>

                        <div v-else class="text-medium-emphasis">
                            No status data available.
                        </div>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </AuthenticatedLayout>
</template>
