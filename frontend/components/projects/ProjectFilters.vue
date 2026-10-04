<script setup>
import { PROJECT_STATUSES } from "~/constants/domain";
import { formatLabel } from "~/utils/format";
defineProps({ total: { type: Number, required: true } });
const search = defineModel("search", { type: String, required: true }),
  status = defineModel("status", { type: String, required: true });
const emit = defineEmits(["filter"]);
</script>
<template>
  <div class="flex flex-wrap gap-3 justify-between mb-5">
    <div class="flex flex-wrap gap-3">
      <UiSearchInput
        v-model="search"
        placeholder="Search projects…"
      />
      <select
        v-model="status"
        class="!w-36 !text-xs"
        @change="emit('filter')"
      >
        <option value="">All statuses</option>
        <option
          v-for="s in PROJECT_STATUSES"
          :key="s"
          :value="s"
        >
          {{ formatLabel(s) }}
        </option>
      </select>
    </div>
    <span class="text-xs muted self-center">{{ total }} projects</span>
  </div>
</template>
