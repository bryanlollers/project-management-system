<script setup>
import { TASK_COLUMNS as columns, TASK_CHART_COLORS } from "~/constants/domain";
const props = defineProps({ stats: { type: Object, required: true } });
const taskTotal = computed(() => props.stats.task_status.reduce((a, b) => a + b.total, 0));
const doneTotal = computed(
  () => props.stats.task_status.find((s) => s.status === "done")?.total || 0,
);
const chartGradient = computed(() => {
  let start = 0;
  const colors = TASK_CHART_COLORS;
  return (
    "conic-gradient(" +
    props.stats.task_status
      .map((s) => {
        const end = start + (s.total / (taskTotal.value || 1)) * 100;
        const part = `${colors[s.status] || "#ddd"} ${start}% ${end}%`;
        start = end;
        return part;
      })
      .join(",") +
    ")"
  );
});
</script>
<template>
  <div class="panel p-5">
    <h2 class="font-bold text-sm">Task overview</h2>
    <p class="text-[10px] muted mt-1">A snapshot of your progress</p>
    <div class="flex items-center justify-center gap-7 mt-7">
      <div
        class="w-[154px] h-[154px] rounded-full p-[19px] shrink-0"
        :style="{ background: taskTotal ? chartGradient : '#edf1ef' }"
      >
        <div class="rounded-full bg-white w-full h-full grid content-center text-center">
          <span class="text-[29px] font-bold">{{ taskTotal }}</span>
          <span class="text-[10px] muted">Total tasks</span>
        </div>
      </div>
      <div class="space-y-4">
        <div
          v-for="col in columns"
          :key="col.id"
          class="flex items-center gap-2 text-[10px]"
        >
          <span
            class="w-2 h-2 rounded-full"
            :style="{
              background: TASK_CHART_COLORS[col.id],
            }"
          />
          <span class="muted w-[60px]">{{ col.label }}</span>
          <strong>{{ stats.task_status.find((s) => s.status === col.id)?.total || 0 }}</strong>
        </div>
      </div>
    </div>
    <div class="text-[10px] muted text-center mt-5">
      {{ taskTotal ? Math.round((doneTotal / taskTotal) * 100) : 0 }}% of all tasks completed
    </div>
  </div>
</template>
