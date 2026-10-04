<script setup>
defineProps({
  label: { type: String, required: true },
  fill: { type: Boolean, default: false },
  maxHeight: { type: String, default: "min(20dvh, 160px)" },
});
</script>

<template>
  <div
    class="modal-scroll-region min-h-0 max-h-[var(--scroll-max-height)] overflow-y-auto overscroll-contain pe-1.5 [scrollbar-width:thin] [scrollbar-color:#b7c4bc_transparent] focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#27784f] focus-visible:-outline-offset-2"
    :class="{ 'flex-[0_1_auto]': fill }"
    :style="{ '--scroll-max-height': maxHeight }"
    role="region"
    :aria-label="label"
    tabindex="0"
  >
    <slot />
  </div>
</template>
