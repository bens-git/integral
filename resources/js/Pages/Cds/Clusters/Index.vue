<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="d-flex align-center justify-space-between">
                <div>
                    <h2 class="text-h5 font-weight-medium">Submission Clusters</h2>
                    <div class="text-medium-emphasis">
                        AI-grouped themes across submissions
                    </div>
                </div>
            </div>
        </template>

        <v-container fluid>
            <v-row>
                <v-col
                    v-for="cluster in clusters.data"
                    :key="cluster.id"
                    cols="12"
                    md="6"
                    lg="4"
                >
                    <v-card
                        class="h-100"
                        :to="route('cds.clusters.show', cluster.id)"
                        hover
                        ripple
                    >
                        <v-card-item>
                            <template #prepend>
                                <v-avatar
                                    color="primary"
                                    variant="tonal"
                                    class="me-3"
                                >
                                    <v-icon>mdi-brain</v-icon>
                                </v-avatar>
                            </template>

                            <v-card-title class="text-wrap">
                                {{ cluster.title }}
                            </v-card-title>

                            <v-card-subtitle class="text-wrap mt-1">
                                {{ cluster.summary }}
                            </v-card-subtitle>
                        </v-card-item>

                        <v-card-text>
                            <div class="d-flex flex-wrap ga-2 mb-3">
                                <v-chip
                                    v-for="keyword in cluster.keywords"
                                    :key="keyword"
                                    size="small"
                                    variant="outlined"
                                >
                                    {{ keyword }}
                                </v-chip>

                                <v-chip
                                    v-if="!cluster.keywords || !cluster.keywords.length"
                                    size="small"
                                    variant="tonal"
                                >
                                    Unclassified
                                </v-chip>
                            </div>

                            <div class="d-flex ga-4 text-caption text-medium-emphasis">
                                <div>
                                    <v-icon size="small">mdi-account-group-outline</v-icon>
                                    {{ cluster.submissions_count }} submissions
                                </div>

                                <div v-if="cluster.confidence">
                                    <v-icon size="small">mdi-chart-donut</v-icon>
                                    {{ Math.round((cluster.confidence || 0) * 100) }}%
                                    confidence
                                </div>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>

                <v-col v-if="!clusters.data || !clusters.data.length" cols="12">
                    <v-card variant="outlined">
                        <v-card-text class="text-center pa-8 text-medium-emphasis">
                            No clusters yet. Clusters are created automatically as submissions come in.
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    clusters: Object,
});
</script>
