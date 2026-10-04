<script setup>
import { Search } from "lucide-vue-next";
defineProps({ placeholder: { type: String, required: true } });
const model = defineModel({ type: String, required: true });
</script>
<template>
  <div class="relative w-64">
    <Search
      :size="15"
      class="absolute left-3 top-3 muted"
    />
    <input
      v-model="model"
      :placeholder="placeholder"
      class="!pl-9 !text-xs"
      aria-label="Search"
    />
  </div>
</template>
