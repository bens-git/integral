<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="d-flex align-center justify-space-between">
                <div>
                    <h2 class="text-h5 font-weight-medium">{{ cluster.title }}</h2>
                    <div class="text-medium-emphasis">
                        {{ cluster.summary }}
                    </div>
                </div>

                <v-chip variant="tonal" color="primary">
                    {{ cluster.submissions_count }} submissions
                </v-chip>
            </div>
        </template>

        <v-container fluid>
            <v-row>
                <v-col cols="12" md="8">
                    <v-card variant="outlined" class="mb-6">
                        <v-card-title>Submissions in cluster</v-card-title>

                        <v-list lines="two" bg-color="transparent">
                            <v-list-item
                                v-for="submission in cluster.submissions"
                                :key="submission.id"
                                :title="submission.title"
                                :subtitle="submission.submitter?.name || 'Unknown submitter'"
                                :href="route('cds.submissions.show', submission.id)"
                            >
                                <template #prepend>
                                    <v-avatar color="grey-lighten-3" class="me-3">
                                        <v-icon color="grey-darken-1">mdi-file-document-outline</v-icon>
                                    </v-avatar>
                                </template>

                                <template #append>
                                    <v-chip size="small" variant="tonal">
                                        {{ submission.pivot.similarity ? Math.round(submission.pivot.similarity * 100) + '%' : '—' }}
                                    </v-chip>
                                </template>
                            </v-list-item>

                            <v-list-item
                                v-if="!cluster.submissions || !cluster.submissions.length"
                                class="text-center text-medium-emphasis"
                            >
                                No submissions attached to this cluster yet.
                            </v-list-item>
                        </v-list>
                    </v-card>
                </v-col>

                <v-col cols="12" md="4">
                    <v-card variant="outlined" class="mb-6">
                        <v-card-title>Cluster details</v-card-title>

                        <v-card-text>
                            <div class="mb-4">
                                <div class="text-caption text-medium-emphasis mb-1">Keywords</div>
                                <div class="d-flex flex-wrap ga-2">
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
                            </div>

                            <div class="mb-4">
                                <div class="text-caption text-medium-emphasis mb-1">Confidence</div>
                                <div class="d-flex align-center ga-2">
                                    <v-progress-linear
                                        :model-value="Math.round((cluster.confidence || 0) * 100)"
                                        color="primary"
                                        height="12"
                                        rounded
                                    />

                                    <span class="text-caption">
                                        {{ Math.round((cluster.confidence || 0) * 100) }}%
                                    </span>
                                </div>
                            </div>

                            <div>
                                <div class="text-caption text-medium-emphasis mb-1">Members</div>
                                <div class="text-h6 font-weight-bold">
                                    {{ cluster.submissions_count }}
                                </div>
                            </div>
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

</script>