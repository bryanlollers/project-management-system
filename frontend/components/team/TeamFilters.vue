<script setup>
defineProps({ total: { type: Number, required: true } });
const search = defineModel("search", { type: String, required: true });
</script>
<template>
  <div class="flex flex-wrap gap-3 justify-between mb-5">
    <UiSearchInput
      v-model="search"
      placeholder="Search team members…"
    />
    <span class="text-xs muted self-center">{{ total }} team members</span>
  </div>
</template>
