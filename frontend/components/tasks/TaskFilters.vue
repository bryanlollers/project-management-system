<script setup>
import { TASK_VIEW_MODES } from "~/constants/domain";
import { TASK_STATUSES } from "~/constants/domain";
import { formatLabel } from "~/utils/format";
defineProps({ projects: { type: Array, required: true } });
const search = defineModel("search", { type: String, required: true }),
  status = defineModel("status", { type: String, required: true }),
  projectFilter = defineModel("projectFilter", { type: [String, Number], required: true }),
  viewMode = defineModel("viewMode", { type: String, required: true });
const emit = defineEmits(["filter"]);
const modes = TASK_VIEW_MODES;
function setMode(mode) {
  viewMode.value = mode;
  emit("filter");
}
</script>
<template>
  <div class="flex flex-wrap gap-3 justify-between mb-5">
    <div class="flex flex-wrap gap-3">
      <UiSearchInput
        v-model="search"
        placeholder="Search tasks…"
      />
      <select
        v-model="status"
        class="!w-36 !text-xs"
        @change="emit('filter')"
      >
        <option value="">All statuses</option>
        <option
          v-for="s in TASK_STATUSES"
          :key="s"
          :value="s"
        >
          {{ formatLabel(s) }}
        </option>
      </select>
      <select
        v-model="projectFilter"
        class="!w-44 !text-xs"
        @change="emit('filter')"
      >
        <option value="">All projects</option>
        <option
          v-for="p in projects"
          :key="p.id"
          :value="p.id"
        >
          {{ p.name }}
        </option>
      </select>
    </div>
    <div class="flex bg-[#ecefeb] p-1 rounded-lg">
      <button
        v-for="mode in modes"
        :key="mode"
        :class="['px-3 text-xs capitalize rounded-md', { 'bg-white shadow-sm': viewMode === mode }]"
        @click="setMode(mode)"
      >
        {{ mode }}
      </button>
    </div>
  </div>
</template>
