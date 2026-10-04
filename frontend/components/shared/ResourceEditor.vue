<script setup>
import { FORM_LIMITS } from "~/constants/forms";
import { X } from "lucide-vue-next";
defineProps({
  editingKind: { type: String, required: true },
  editingId: { type: [Number, null], required: true },
  clients: { type: Array, required: true },
  projects: { type: Array, required: true },
  people: { type: Array, required: true },
  saving: { type: Boolean, required: true },
  error: { type: String, required: true },
});
const form = defineModel("form", { type: Object, required: true });
const emit = defineEmits(["save", "clearError"]);
const modal = ref();
function showModal() {
  modal.value?.showModal();
}
function close() {
  modal.value?.close();
}
defineExpose({ showModal, close });
</script>
<template>
  <UiModal
    ref="modal"
    @close="emit('clearError')"
  >
    <form
      class="p-7"
      @submit.prevent="emit('save')"
    >
      <div class="flex justify-between mb-6">
        <h2 class="font-bold text-xl">
          {{ editingId ? "Edit" : "New" }}
          {{ editingKind === "users" ? "user" : editingKind.slice(0, -1) }}
        </h2>
        <button
          type="button"
          @click="close()"
          aria-label="Close"
        >
          <X :size="20" />
        </button>
      </div>
      <div class="field-grid">
        <div class="full">
          <label>{{ editingKind === "tasks" ? "Task title" : "Name" }}</label>
          <input
            v-if="editingKind === 'tasks'"
            v-model="form.title"
            required
            :maxlength="FORM_LIMITS.NAME"
          />
          <input
            v-else
            v-model="form.name"
            required
            :maxlength="FORM_LIMITS.NAME"
          />
        </div>
        <ClientsClientFields
          v-if="editingKind === 'clients'"
          v-model="form"
        />
        <ProjectsProjectFields
          v-if="editingKind === 'projects'"
          v-model="form"
          :clients="clients"
          :people="people"
        />
        <TasksTaskFields
          v-if="editingKind === 'tasks'"
          v-model="form"
          :projects="projects"
        />
        <TeamUserFields
          v-if="editingKind === 'users'"
          v-model="form"
          :editing-id="editingId"
        />
      </div>
      <p
        v-if="error"
        class="text-xs text-red-600 mt-4"
        role="alert"
      >
        {{ error }}
      </p>
      <div class="flex justify-end gap-3 mt-7">
        <button
          type="button"
          class="btn"
          @click="close()"
        >
          Cancel
        </button>
        <button
          class="btn primary"
          :disabled="saving"
        >
          {{
            saving
              ? "Saving…"
              : "Save " + (editingKind === "users" ? "user" : editingKind.slice(0, -1))
          }}
        </button>
      </div>
    </form>
  </UiModal>
</template>
