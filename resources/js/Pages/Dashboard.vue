<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.user || page.props.auth?.user || null);
const stats = computed(() => page.props.stats || {});
const recent = computed(() => page.props.recentProposals || []);
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Dashboard</h2>
        </template>

        <v-container class="py-8">
            <v-row>
                <v-col cols="12" md="8">
                    <v-card class="pa-4 mb-4">
                        <div class="d-flex justify-space-between align-center">
                            <div>
                                <h3 class="text-h6">Welcome{{ user ? `, ${user?.name || ''}` : '' }}</h3>
                                <div class="text-subtitle-2">Overview of your workspace</div>
                            </div>
                            <div>
                                <v-btn small text :href="route('cds.dashboard')">Open CDS</v-btn>
                            </div>
                        </div>
                    </v-card>

                    <v-row>
                        <v-col cols="12" sm="6">
                            <v-card class="pa-4">
                                <div class="text-h6">Users</div>
                                <div class="text-h4">{{ stats.users ?? '—' }}</div>
                                <div class="text-caption">Total registered users</div>
                            </v-card>
                        </v-col>

                        <v-col cols="12" sm="6">
                            <v-card class="pa-4">
                                <div class="text-h6">Submissions</div>
                                <div class="text-h4">{{ stats.submissions ?? '—' }}</div>
                                <div class="text-caption">All CDS submissions</div>
                            </v-card>
                        </v-col>
                    </v-row>

                    <v-card class="pa-4 mt-4">
                        <h3 class="text-h6 mb-2">Recent submissions</h3>
                        <v-list two-line>
                            <v-list-item v-for="p in recent" :key="p.id">
                                <div class="v-list-item-content">
                                    <Link :href="route('cds.submissions.show', p.id)">
                                        <div class="font-medium">{{ p.title }}</div>
                                    </Link>
                                    <div class="text-caption">{{ p.status }} • {{ new Date(p.created_at).toLocaleString() }}</div>
                                </div>
                                <div class="v-list-item-action">
                                    <v-btn icon :href="route('cds.submissions.show', p.id)">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 12h14"></path><path d="M12 5l7 7-7 7"></path></svg>
                                    </v-btn>
                                </div>
                            </v-list-item>
                            <v-list-item v-if="!recent || recent.length === 0">
                                <div class="v-list-item-content">No recent proposals</div>
                            </v-list-item>
                        </v-list>
                    </v-card>
                </v-col>

                <v-col cols="12" md="4">
                    <v-card class="pa-4 mb-4">
                        <h3 class="text-h6">Submissions by status</h3>
                        <v-list dense>
                            <v-list-item v-for="(count, status) in stats.submissions_by_status || {}" :key="status">
                                <v-list-item-content>
                                    <div class="d-flex justify-space-between">
                                        <div>{{ status }}</div>
                                        <div class="font-medium">{{ count }}</div>
                                    </div>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item v-if="!stats.proposals_by_status || Object.keys(stats.proposals_by_status).length === 0">
                                <v-list-item-content>No data</v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </v-card>

                    <v-card class="pa-4">
                        <h3 class="text-h6">Quick actions</h3>
                        <v-btn block color="primary" class="mb-2" :href="route('cds.submissions.create')">New Submission</v-btn>
                        <v-btn block outlined :href="route('cds.issues.create')">New Issue</v-btn>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </AuthenticatedLayout>
</template>
