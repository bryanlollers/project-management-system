<script setup>
import { PRIORITIES } from "~/constants/domain";
import { formatLabel } from "~/utils/format";
defineProps({ statuses: { type: Array, required: true } });
const form = defineModel({ type: Object, required: true });
</script>
<template>
  <div class="col-span-full">
    <label>Description</label>
    <textarea
      class="resize-y max-h-[min(30dvh,240px)] overflow-y-auto"
      v-model="form.description"
      rows="3"
    />
  </div>
  <div>
    <label>Status</label>
    <select v-model="form.status">
      <option
        v-for="s in statuses"
        :key="s"
        :value="s"
      >
        {{ formatLabel(s) }}
      </option>
    </select>
  </div>
  <div>
    <label>Priority</label>
    <select v-model="form.priority">
      <option
        v-for="p in PRIORITIES"
        :key="p"
      >
        {{ p }}
      </option>
    </select>
  </div>
</template>
