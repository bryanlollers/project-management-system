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
    class="w-[calc(100%-32px)] max-w-[620px] max-h-[calc(100dvh-32px)] overflow-hidden rounded-2xl border-0 p-0 shadow-[0_20px_100px_#0003] backdrop:bg-[#142b2466] backdrop:backdrop-blur-[3px]"
    @click="closeOnBackdrop"
    @close="emit('close')"
  >
    <slot />
  </dialog>
</template>
