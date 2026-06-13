<script setup>
import { ref, watch, computed } from "vue";
import { Head, router, usePage } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DestroySubmissionDialog from "@/Pages/Cds/Submissions/DestroySubmissionDialog.vue";
import SubmissionDialog from "@/Pages/Cds/Submissions/SubmissionDialog.vue";
import StoreSubmissionDialog from "@/Pages/Cds/Submissions/StoreSubmissionDialog.vue";

const page = usePage();
const currentUser = computed(() => page.props.auth.user);

const props = defineProps({
    submissions: Object,
    filters: Object,
});

const statusColors = {
    draft: "grey",
    submitted: "blue",
    validated: "green",
    framed: "purple",
    active: "amber",
    accepted: "success",
    rejected: "error",
    implemented: "success",
    archived: "grey",
};

const headers = [
    {
        title: "Title",
        key: "title",
        sortable: true,
    },
    {
        title: "Category",
        key: "category",
        sortable: true,
    },
    {
        title: "Status",
        key: "status",
        sortable: true,
    },
    {
        title: "Priority",
        key: "priority",
        sortable: true,
    },
    {
        title: "Submitted",
        key: "created_at",
        sortable: true,
    },
    {
        title: "",
        key: "actions",
        sortable: false,
        width: 120,
    },
];

const search = ref(props.filters.search || "");
const status = ref(props.filters.status || null);
const category = ref(props.filters.category || null);

const loading = ref(false);

function reload(options = {}) {
    loading.value = true;

    router.get(
        route("cds.submissions.index"),
        {
            search: search.value,
            status: status.value,
            category: category.value,
            sort_by: options.sortBy ?? props.filters.sort_by,
            sort_direction:
                options.sortDirection ?? props.filters.sort_direction,
            page: options.page ?? 1,
            per_page: options.itemsPerPage ?? 15,
        },
        {
            preserveState: true,
            replace: true,
            onFinish: () => {
                loading.value = false;
            },
        },
    );
}

let searchTimeout = null;

watch(search, () => {
    clearTimeout(searchTimeout);

    searchTimeout = setTimeout(() => {
        reload();
    }, 500);
});

watch([status, category], () => {
    reload();
});

function tableChanged(options) {
    const sort = options.sortBy?.[0];

    reload({
        page: options.page,
        itemsPerPage: options.itemsPerPage,
        sortBy: sort?.key,
        sortDirection: sort?.order,
    });
}

function formatDate(date) {
    return new Date(date).toLocaleDateString();
}

function refresh() {
    router.reload({
        only: ["submissions"],
    });
}
</script>

<template>
    <Head title="Submissions" />

    <AuthenticatedLayout>
        <template #header>
            <div class="d-flex align-center justify-space-between">
                <h2 class="text-h5 font-weight-medium">CDS Submissions</h2>

                <StoreSubmissionDialog @submission-created="refresh" />
            </div>
        </template>

        <v-container fluid>
            <v-card class="mb-6">
                <v-card-text>
                    <div class="d-flex flex-wrap ga-4 align-center">
                        <v-text-field
                            v-model="search"
                            label="Search submissions"
                            prepend-inner-icon="mdi-magnify"
                            density="compact"
                            hide-details
                            clearable
                            style="max-width: 350px"
                        />

                        <v-select
                            v-model="status"
                            label="Status"
                            density="compact"
                            hide-details
                            clearable
                            style="max-width: 220px"
                            :items="[
                                'draft',
                                'submitted',
                                'validated',
                                'framed',
                                'active',
                                'accepted',
                                'rejected',
                                'implemented',
                                'archived',
                            ]"
                        />

                        <v-select
                            v-model="category"
                            label="Category"
                            density="compact"
                            hide-details
                            clearable
                            style="max-width: 220px"
                            :items="[
                                'Submission',
                                'Issue',
                                'Objection',
                                'Comment',
                            ]"
                        />

                        <v-spacer />

                        <v-btn
                            variant="text"
                            prepend-icon="mdi-refresh"
                            @click="refresh"
                        >
                            Refresh
                        </v-btn>
                    </div>
                </v-card-text>
            </v-card>

            <v-card>
                <v-data-table-server
                    :headers="headers"
                    :items="submissions.data"
                    :items-length="submissions.total"
                    :loading="loading"
                    :items-per-page="submissions.per_page"
                    hover
                    @update:options="tableChanged"
                >
                    <template #item.title="{ item }">
                        <div class="font-weight-medium">
                            {{ item.title }}
                        </div>
                    </template>

                    <template #item.status="{ item }">
                        <v-chip
                            :color="statusColors[item.status] || 'grey'"
                            size="small"
                            variant="tonal"
                        >
                            {{ item.status }}
                        </v-chip>
                    </template>

                    <template #item.created_at="{ item }">
                        {{ formatDate(item.created_at) }}
                    </template>

                    <template #item.actions="{ item }">
                        <SubmissionDialog :submission="item">
                            <template #activator="{ props }">
                                <v-btn
                                    icon="mdi-eye"
                                    size="small"
                                    variant="text"
                                    v-bind="props"
                                />
                            </template>
                        </SubmissionDialog>
                        <DestroySubmissionDialog :submission="item" @submission-destroyed="refresh" v-if="item.submitter?.user_id === currentUser.id">
                            <template #activator="{ props }">
                                <v-btn
                                    icon="mdi-delete"
                                    size="small"
                                    variant="text"
                                    color="error"
                                    v-bind="props"
                                />
                            </template>
                        </DestroySubmissionDialog>
                    </template>

                    <template #no-data>
                        <div class="pa-8 text-center">
                            <div class="text-h6 mb-2">No submissions found</div>

                            <div class="text-body-2 text-medium-emphasis mb-4">
                                Adjust your filters or create a new submission.
                            </div>

<StoreSubmissionDialog @submission-created="refresh" />
                        </div>
                    </template>
                </v-data-table-server>
            </v-card>
        </v-container>
    </AuthenticatedLayout>
</template>
