<script setup>
import { FolderKanban, MoreHorizontal } from "lucide-vue-next";
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
    empty-title="No projects found."
    :empty-hint="
      filtered ? 'Try changing your search or filters.' : 'Your next great project starts here.'
    "
    @page="emit('page', $event)"
  >
    <template #title><slot name="title" /></template>
    <template #head>
      <th>Project name</th>
      <th>Client</th>
      <th>Status</th>
      <th>Progress</th>
      <th>Due date</th>
      <th>Team</th>
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
            <FolderKanban :size="15" />
          </span>
          <strong class="font-semibold text-xs">{{ item.name }}</strong>
        </button>
      </td>
      <td class="text-[#859088]">{{ item.client?.name }}</td>
      <td>
        <span
          class="badge"
          :class="item.status"
        >
          {{ label(item.status) }}
        </span>
      </td>
      <td>
        <div class="flex gap-3 items-center min-w-[100px]">
          <div class="progress flex-1">
            <span :style="{ width: (item.progress || 0) + '%' }" />
          </div>
          <span class="text-[10px] muted">{{ item.progress || 0 }}%</span>
        </div>
      </td>
      <td class="text-[#859088] whitespace-nowrap">
        {{ date(item.end_date) }}
      </td>
      <td>
        <div class="flex -space-x-2">
          <span
            v-for="person in item.members?.slice(0, 3)"
            :key="person.id"
            class="avatar !w-7 !h-7 !text-[8px]"
            :title="person.name"
          >
            {{ initials(person.name) }}
          </span>
          <span
            v-if="!item.members?.length"
            class="text-[10px] muted"
          >
            No team
          </span>
        </div>
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
