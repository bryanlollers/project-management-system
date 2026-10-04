<script setup>
defineProps({
  deleteTarget: { type: [Object, null], required: true },
  deleteKind: { type: String, required: true },
  saving: { type: Boolean, required: true },
  error: { type: String, required: true },
});
const emit = defineEmits(["confirm"]);
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
  <UiModal ref="modal">
    <div
      class="flex flex-col max-h-[calc(100dvh-32px)] p-7 [overflow-wrap:anywhere] [&>:first-child]:shrink-0"
    >
      <h2 class="text-lg font-bold">Delete record?</h2>
      <div class="modal-body min-h-0 overflow-y-auto overscroll-contain">
        <p class="font-semibold text-sm mt-3">{{ deleteTarget?.name || deleteTarget?.title }}</p>
        <p class="muted text-sm mt-3">
          This permanently removes the record{{
            deleteKind === "projects" ? " and its tasks, comments, and activity" : ""
          }}.
        </p>
        <p
          v-if="error"
          role="alert"
          class="text-red-600 text-xs mt-4"
        >
          {{ error }}
        </p>
      </div>
      <div class="modal-footer shrink-0 flex justify-end gap-3 mt-6">
        <button
          class="btn"
          @click="close()"
        >
          Cancel
        </button>
        <button
          class="btn !bg-red-600 text-white"
          :disabled="saving"
          @click="emit('confirm')"
        >
          {{ saving ? "Deleting…" : "Delete" }}
        </button>
      </div>
    </div>
  </UiModal>
</template>
