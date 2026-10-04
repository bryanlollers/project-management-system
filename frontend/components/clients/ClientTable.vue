<script setup>
import { Building2, MoreHorizontal } from "lucide-vue-next";
defineProps({
  items: { type: Array, required: true },
  pagination: { type: Object, required: false },
  page: { type: Number, required: false },
  filtered: { type: Boolean, required: false },
});
const emit = defineEmits(["select", "page"]);
</script>
<template>
  <UiDataTable
    :count="items.length"
    :pagination="pagination"
    :page="page"
    empty-title="No clients found."
    :empty-hint="
      filtered ? 'Try changing your search or filters.' : 'Your next great project starts here.'
    "
    @page="emit('page', $event)"
  >
    <template #title><slot name="title" /></template>
    <template #head>
      <th>Client</th>
      <th>Contact</th>
      <th>Projects</th>
      <th />
    </template>
    <tr
      v-for="item in items"
      :key="item.id"
    >
      <td>
        <button
          class="flex items-center gap-3 text-left"
          @click="emit('select', item)"
        >
          <span class="w-8 h-8 rounded-lg grid place-items-center bg-[#eaf3ee] text-[#85ab91]">
            <Building2 :size="15" />
          </span>
          <strong class="font-semibold text-xs">{{ item.name }}</strong>
        </button>
      </td>
      <td class="text-[#859088]">{{ item.email }}</td>
      <td>{{ item.projects_count || 0 }} projects</td>
      <td>
        <button
          class="muted hover:text-green-700"
          aria-label="Open details"
          @click="emit('select', item)"
        >
          <MoreHorizontal :size="17" />
        </button>
      </td>
    </tr>
  </UiDataTable>
</template>
