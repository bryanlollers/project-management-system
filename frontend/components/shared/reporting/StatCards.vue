<script setup>
import { FolderKanban, CheckSquare, Clock3, CircleCheck } from "lucide-vue-next";
defineProps({ stats: { type: Object, required: true } });
</script>
<template>
  <div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-7">
    <div
      v-for="(card, i) in [
        {
          title: 'Active projects',
          value: stats.active_projects,
          icon: FolderKanban,
          note: 'Moving things forward',
          color: '#eef4ee',
        },
        {
          title: 'Completed projects',
          value: stats.completed_projects,
          icon: CircleCheck,
          note: 'Delivered with care',
          color: '#eef4f9',
        },
        {
          title: 'Overdue tasks',
          value: stats.overdue_tasks,
          icon: Clock3,
          note: 'A little attention needed',
          color: '#fcf3e9',
        },
        {
          title: 'Total tasks',
          value: stats.total_tasks,
          icon: CheckSquare,
          note: 'Across your workspace',
          color: '#f3eff9',
        },
      ]"
      :key="i"
      class="panel p-5"
    >
      <div class="flex items-center justify-between">
        <span class="text-[11px] text-[#7c8881]">{{ card.title }}</span>
        <span
          class="w-8 h-8 rounded-lg grid place-items-center text-[#779382]"
          :style="{ background: card.color }"
        >
          <component
            :is="card.icon"
            :size="16"
          />
        </span>
      </div>
      <p class="text-[30px] font-bold tracking-tight mt-3">
        {{ card.value.toString().padStart(2, "0") }}
      </p>
      <p class="text-[10px] muted mt-2 flex items-center gap-1.5">
        <span class="w-1 h-1 bg-[#91b49b] rounded-full" />
        {{ card.note }}
      </p>
    </div>
  </div>
</template>
