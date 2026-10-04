<script setup>
import { CheckSquare, MoreHorizontal } from "lucide-vue-next";
import { formatDate as date, getInitials as initials, formatLabel as label } from "~/utils/format";
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
    empty-title="No tasks found."
    :empty-hint="
      filtered ? 'Try changing your search or filters.' : 'Your next great project starts here.'
    "
    @page="emit('page', $event)"
  >
    <template #title><slot name="title" /></template>
    <template #head>
      <th>Task name</th>
      <th>Project</th>
      <th>Status</th>
      <th>Assigned to</th>
      <th>Due date</th>
      <th>Priority</th>
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
            <CheckSquare :size="15" />
          </span>
          <strong class="font-semibold text-xs">{{ item.title }}</strong>
        </button>
      </td>
      <td class="text-[#859088]">{{ item.project?.name }}</td>
      <td>
        <span
          class="badge"
          :class="item.status"
        >
          {{ label(item.status) }}
        </span>
      </td>
      <td>
        <div class="flex items-center gap-2">
          <span class="avatar">{{ initials(item.assignee?.name) }}</span>
          <span class="text-[10px] muted">{{ item.assignee?.name || "Unassigned" }}</span>
        </div>
      </td>
      <td>{{ date(item.due_date) }}</td>
      <td>
        <span
          class="badge"
          :class="item.priority"
        >
          {{ item.priority }}
        </span>
      </td>
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
