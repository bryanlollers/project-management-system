<script setup>
import { MoreHorizontal } from "lucide-vue-next";
import { getInitials as initials } from "~/utils/format";
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
    empty-title="No team members found."
    :empty-hint="
      filtered ? 'Try changing your search or filters.' : 'Your next great project starts here.'
    "
    @page="emit('page', $event)"
  >
    <template #title><slot name="title" /></template>
    <template #head>
      <th>Team member</th>
      <th>Email</th>
      <th>Role</th>
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
          <span class="avatar">{{ initials(item.name) }}</span>
          <strong>{{ item.name }}</strong>
        </button>
      </td>
      <td>{{ item.email }}</td>
      <td>
        <span class="badge">{{ item.role }}</span>
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
