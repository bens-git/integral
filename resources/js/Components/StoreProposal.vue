<template>
  <div>
    <v-dialog v-model="dialog" persistent max-width="720">
      <template #activator="{ props: activatorProps }">
        <v-btn color="primary" v-bind="activatorProps">New Proposal</v-btn>
      </template>

      <v-card>
        <v-card-title>
          Create Proposal
        </v-card-title>

        <v-card-text>
          <v-form>
            <v-text-field
              v-model="form.title"
              label="Title"
              :error="!!form.errors.title || (showClientErrors && !form.title)"
              :error-messages="form.errors.title || (showClientErrors && !form.title ? ['Title is required'] : [])"
              required
            />

            <v-textarea
              v-model="form.body"
              label="Description"
              :error="!!form.errors.body || (showClientErrors && !form.body)"
              :error-messages="form.errors.body || (showClientErrors && !form.body ? ['Description is required'] : [])"
              rows="6"
              required
            />
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

const emit = defineEmits(['proposal-created']);
const dialog = ref(false);
const form = useForm({ title: '', body: '' });
const showClientErrors = ref(false);

const isValid = computed(() => {
  return form.title && form.title.trim().length > 0 && form.body && form.body.trim().length > 0;
});

function close() {
  dialog.value = false;
  form.reset();
  showClientErrors.value = false;
}

function submit() {
  showClientErrors.value = true;
  if (!isValid.value) return;

  form.post(route('cds.proposals.store'), {
    onSuccess: () => {
      dialog.value = false;
      form.reset();
      showClientErrors.value = false;
      // emit so parent can refresh
      emit('proposal-created');
    },
  });
}
</script>
