<script setup>
import { ChevronLeft, ChevronRight } from "lucide-vue-next";
defineProps({ meta: { type: Object, required: true }, page: { type: Number, required: true } });
const emit = defineEmits(["change"]);
</script>
<template>
  <div class="p-4 border-t flex justify-between items-center">
    <span class="text-[10px] muted">
      {{ meta.from || 0 }}–{{ meta.to || 0 }} of {{ meta.total }}
    </span>
    <div class="flex items-center gap-3">
      <button
        class="btn !p-1.5"
        :disabled="page <= 1"
        aria-label="Previous page"
        @click="emit('change', page - 1)"
      >
        <ChevronLeft :size="14" />
      </button>
      <span class="text-xs muted">{{ page }} / {{ meta.last_page }}</span>
      <button
        class="btn !p-1.5"
        :disabled="page >= meta.last_page"
        aria-label="Next page"
        @click="emit('change', page + 1)"
      >
        <ChevronRight :size="14" />
      </button>
    </div>
  </div>
</template>
