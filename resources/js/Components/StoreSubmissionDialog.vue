<template>
  <div>
    <v-dialog v-model="dialog" persistent max-width="720">
      <template #activator="{ props: activatorProps }">
        <slot name="activator" v-bind="{ props: activatorProps }">
          <v-btn color="primary" v-bind="activatorProps">New Submission</v-btn>
        </slot>
      </template>

      <v-card>
        <v-card-title>
          Create Submission
        </v-card-title>

        <v-card-text>
          <v-form v-model="formValid" @submit.prevent="submit">
            <!-- Title -->
            <v-text-field
              v-model="form.title"
              label="Title"
              variant="outlined"
              required
              :error="!!form.errors.title || (showClientErrors && !form.title)"
              :error-messages="form.errors.title || (showClientErrors && !form.title ? ['Title is required'] : [])"
              class="mb-4"
            />

            <!-- Description -->
            <v-textarea
              v-model="form.description"
              label="Description"
              variant="outlined"
              rows="4"
              required
              :error="!!form.errors.description || (showClientErrors && !form.description)"
              :error-messages="form.errors.description || (showClientErrors && !form.description ? ['Description is required'] : [])"
              class="mb-4"
            />

            <!-- Content -->
            <v-textarea
              v-model="form.content"
              label="Full Content"
              hint="Provide detailed information about your submission"
              persistent-hint
              variant="outlined"
              rows="6"
              class="mb-4"
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
                  :error-messages="form.errors.scope"
                />
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>

        <v-card-actions>
          <v-spacer />
          <v-btn text @click="close">Cancel</v-btn>
          <v-btn color="primary" :loading="form.processing" :disabled="!isValid || form.processing" @click="submit">Create</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  categories: {
    type: Array,
    default: () => ['policy', 'resource', 'design', 'coordination', 'review']
  },
  priorities: {
    type: Array,
    default: () => ['low', 'normal', 'high', 'urgent']
  },
  scopes: {
    type: Array,
    default: () => ['local', 'regional', 'bioregional', 'global']
  }
});

const emit = defineEmits(['submission-created']);
const dialog = ref(false);
const formValid = ref(false);
const form = useForm({ title: '', description: '', content: '', category: 'policy', priority: 'normal', scope: null });
const showClientErrors = ref(false);

const isValid = computed(() => {
  return form.title && form.title.trim().length > 0 && form.description && form.description.trim().length > 0;
});

const formatLabel = (value) => {
  if (!value) return '';
  return value
    .replace('_', ' ')
    .replace(/\b\w/g, c => c.toUpperCase());
};

function close() {
  dialog.value = false;
  form.reset();
  showClientErrors.value = false;
}

function submit() {
  showClientErrors.value = true;
  if (!isValid.value) return;

  form.post(route('cds.submissions.store'), {
    onSuccess: () => {
      dialog.value = false;
      form.reset();
      showClientErrors.value = false;
      emit('submission-created');
    },
    onError: () => {
      showClientErrors.value = true;
    }
  });
}
</script>