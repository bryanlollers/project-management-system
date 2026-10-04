<script setup>
import { TASK_STATUSES } from "~/constants/domain";
const props = defineProps({ projects: { type: Array, required: true } });
const form = defineModel({ type: Object, required: true });
const assignees = computed(
  () => props.projects.find((p) => p.id === Number(form.value.project_id))?.members || [],
);
</script>
<template>
  <div class="col-span-full">
    <label>Project</label>
    <select
      v-model="form.project_id"
      required
      @change="form.assignee_id = ''"
    >
      <option
        value=""
        disabled
      >
        Select project
      </option>
      <option
        v-for="p in projects"
        :key="p.id"
        :value="p.id"
      >
        {{ p.name }}
      </option>
    </select>
  </div>
  <SharedWorkItemFields
    v-model="form"
    :statuses="TASK_STATUSES"
  />
  <div>
    <label>Assigned to</label>
    <select v-model="form.assignee_id">
      <option value="">Unassigned</option>
      <option
        v-for="p in assignees"
        :key="p.id"
        :value="p.id"
      >
        {{ p.name }}
      </option>
    </select>
  </div>
  <div>
    <label>Due date</label>
    <input
      v-model="form.due_date"
      type="date"
    />
  </div>
</template>
