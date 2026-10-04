<script setup>
import { X } from "lucide-vue-next";
defineProps({ message: { type: String, required: true } });
const emit = defineEmits(["dismiss"]);
</script>
<template>
  <div
    v-if="message"
    role="alert"
    class="bg-red-50 text-red-700 border border-red-100 rounded-lg p-3 mb-5 flex justify-between"
  >
    {{ message }}
    <button
      aria-label="Dismiss error"
      @click="emit('dismiss')"
    >
      <X :size="16" />
    </button>
  </div>
</template>
