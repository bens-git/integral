<template>
  <div>
    <v-dialog v-model="dialog" persistent max-width="500">
      <template #activator="{ props: activatorProps }">
        <slot name="activator" v-bind="{ props: activatorProps }">
          <v-btn color="error" v-bind="activatorProps">Delete</v-btn>
        </slot>
      </template>

      <v-card>
        <v-card-title class="text-h6">
          Delete Submission
        </v-card-title>

        <v-card-text>
          <p class="mb-4">
            Are you sure you want to delete this submission? This action cannot be undone.
          </p>
          <p class="text-body-2 text-medium-emphasis">
            "{{ submission.title }}"
          </p>
        </v-card-text>

        <v-card-actions>
          <v-spacer />
          <v-btn text @click="close">Cancel</v-btn>
          <v-btn color="error" :loading="form.processing" @click="destroy">Delete</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
  submission: Object,
});

const emit = defineEmits(['submission-destroyed']);
const dialog = ref(false);
const form = useForm({});

function close() {
  dialog.value = false;
}

function destroy() {
  form.delete(route('cds.submissions.destroy', props.submission.id), {
    onSuccess: () => {
      dialog.value = false;
      emit('submission-destroyed');
    },
    onError: () => {
      dialog.value = false;
    },
  });
}
</script>