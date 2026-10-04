<script setup>
const dialog = ref();
const emit = defineEmits(["close"]);
function showModal() {
  if (!dialog.value?.open) dialog.value?.showModal();
}
function close() {
  dialog.value?.close();
}
function closeOnBackdrop(event) {
  if (event.target === dialog.value) close();
}
defineExpose({ showModal, close });
</script>
<template>
  <dialog
    ref="dialog"
    @click="closeOnBackdrop"
    @close="emit('close')"
  >
    <slot />
  </dialog>
</template>
