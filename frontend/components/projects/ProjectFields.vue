<script setup>
import { PROJECT_STATUSES } from "~/constants/domain";
defineProps({ clients: { type: Array, required: true }, people: { type: Array, required: true } });
const form = defineModel({ type: Object, required: true });
</script>
<template>
  <div class="full">
    <label>Client</label>
    <select
      v-model="form.client_id"
      required
    >
      <option
        value=""
        disabled
      >
        Select client
      </option>
      <option
        v-for="c in clients"
        :key="c.id"
        :value="c.id"
      >
        {{ c.name }}
      </option>
    </select>
  </div>
  <SharedWorkItemFields
    v-model="form"
    :statuses="PROJECT_STATUSES"
  />
  <div>
    <label>Start date</label>
    <input
      v-model="form.start_date"
      type="date"
    />
  </div>
  <div>
    <label>End date</label>
    <input
      v-model="form.end_date"
      type="date"
      :min="form.start_date || undefined"
    />
  </div>
  <div class="full">
    <label>Team members</label>
    <div class="flex flex-wrap gap-3">
      <label
        v-for="p in people"
        :key="p.id"
        class="flex gap-2 items-center"
      >
        <input
          v-model="form.member_ids"
          type="checkbox"
          :value="p.id"
          class="!w-auto"
        />
        {{ p.name }}
      </label>
    </div>
  </div>
</template>
