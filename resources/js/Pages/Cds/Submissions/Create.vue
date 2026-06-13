<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, useForm, Head } from '@inertiajs/vue3';

defineProps({
    categories: Array,
    priorities: Array,
    scopes: Array,
});

const form = useForm({
    title: '',
    description: '',
    content: '',
    category: 'policy',
    priority: 'normal',
    scope: null,
});

function submit() {
    form.post(route('cds.submissions.store'));
}

const formatLabel = (value) => {
    if (!value) return '';
    return value
        .replace('_', ' ')
        .replace(/\b\w/g, c => c.toUpperCase());
};
</script>


<template>
    <Head title="New Proposal" />

    <AuthenticatedLayout>

        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-h5">
                    Create New Proposal
                </h2>
            </div>
        </template>


        <v-container class="py-8">

            <v-row justify="center">

                <v-col cols="12" md="9" lg="8">

                    <v-card elevation="2">

                        <v-card-title class="pa-6">
                            Proposal Details
                        </v-card-title>


                        <v-divider />


                        <v-card-text class="pa-6">

                            <v-form @submit.prevent="submit">


                                <!-- Title -->
                                <v-text-field
                                    v-model="form.title"
                                    label="Title"
                                    variant="outlined"
                                    required
                                    :error-messages="form.errors.title"
                                    class="mb-4"
                                />


                                <!-- Description -->
                                <v-textarea
                                    v-model="form.description"
                                    label="Description"
                                    variant="outlined"
                                    rows="4"
                                    required
                                    :error-messages="form.errors.description"
                                    class="mb-4"
                                />


                                <!-- Content -->
                                <v-textarea
                                    v-model="form.content"
                                    label="Full Content"
                                    hint="Provide detailed information about your proposal"
                                    persistent-hint
                                    variant="outlined"
                                    rows="8"
                                    class="mb-6"
                                />



                                <v-row>


                                    <!-- Category -->
                                    <v-col cols="12" md="4">

                                        <v-select
                                            v-model="form.category"
                                            label="Category"
                                            variant="outlined"
                                            :items="categories"
                                            :item-title="formatLabel"
                                            item-value="this"
                                            :error-messages="form.errors.category"
                                        />

                                    </v-col>



                                    <!-- Priority -->
                                    <v-col cols="12" md="4">

                                        <v-select
                                            v-model="form.priority"
                                            label="Priority"
                                            variant="outlined"
                                            :items="priorities"
                                            :item-title="formatLabel"
                                            item-value="this"
                                            :error-messages="form.errors.priority"
                                        />

                                    </v-col>



                                    <!-- Scope -->
                                    <v-col cols="12" md="4">

                                        <v-select
                                            v-model="form.scope"
                                            label="Scope"
                                            variant="outlined"
                                            clearable
                                            :items="scopes"
                                            :item-title="formatLabel"
                                            item-value="this"
                                        />

                                    </v-col>


                                </v-row>



                                <v-divider class="my-6" />


                                <!-- Actions -->
                                <div class="d-flex justify-end ga-3">


                                    <v-btn
                                        variant="outlined"
                                        color="grey"
                                        :href="route('cds.submissions.index')"
                                        as="a"
                                    >
                                        Cancel
                                    </v-btn>



                                    <v-btn
                                        color="primary"
                                        type="submit"
                                        :loading="form.processing"
                                    >
                                        Create Proposal
                                    </v-btn>


                                </div>


                            </v-form>


                        </v-card-text>


                    </v-card>


                </v-col>

            </v-row>


        </v-container>


    </AuthenticatedLayout>
</template>