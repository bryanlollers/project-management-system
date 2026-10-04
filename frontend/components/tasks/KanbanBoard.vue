<script setup>
import { CalendarDays, GripVertical } from "lucide-vue-next";
import { formatDate as date, getInitials as initials } from "~/utils/format";
import { TASK_COLUMNS as columns } from "~/constants/domain";
import { canUpdateTask } from "~/utils/permissions";
const props = defineProps({
  list: { type: Array, required: true },
  pagination: { type: Object, required: true },
  page: { type: Number, required: true },
  user: { type: Object, required: true },
});
const emit = defineEmits(["show", "move", "page"]);
const dragged = ref(null);
function drop(status) {
  if (dragged.value) {
    emit("move", dragged.value, status);
  }
  dragged.value = null;
}
function showDetails(kind, item) {
  emit("show", kind, item);
}
</script>
<template>
  <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4">
    <section
      v-for="col in columns"
      :key="col.id"
      class="rounded-xl bg-[#eef1ef] p-3 min-h-[400px]"
      @dragover.prevent
      @drop.prevent="drop(col.id)"
    >
      <div class="flex items-center gap-2 mb-4 px-1">
        <span
          class="w-2 h-2 rounded-full"
          :style="{ background: col.color }"
        />
        <h2 class="font-semibold text-xs">{{ col.label }}</h2>
        <span class="text-[10px] muted ml-1">
          {{ list.filter((t) => t.status === col.id).length }}
        </span>
      </div>
      <article
        v-for="task in list.filter((t) => t.status === col.id)"
        :key="task.id"
        class="panel p-4 mb-3 cursor-pointer hover:shadow-md transition-shadow"
        :draggable="canUpdateTask(user, task)"
        @dragstart="dragged = task"
        @dragend="dragged = null"
        @click="showDetails('tasks', task)"
      >
        <div class="flex justify-between">
          <span
            class="badge"
            :class="task.priority"
          >
            {{ task.priority }}
          </span>
          <GripVertical
            :size="13"
            class="muted"
          />
        </div>
        <h3 class="font-bold text-xs mt-3 leading-relaxed">
          {{ task.title }}
        </h3>
        <p class="text-[10px] muted mt-2">{{ task.project?.name }}</p>
        <div class="border-t mt-4 pt-3 flex justify-between items-center">
          <span class="text-[10px] muted flex items-center gap-1">
            <CalendarDays :size="12" />
            {{ date(task.due_date) }}
          </span>
          <span
            class="avatar"
            :title="task.assignee?.name"
          >
            {{ initials(task.assignee?.name) }}
          </span>
        </div>
      </article>
      <p
        v-if="!list.some((t) => t.status === col.id)"
        class="text-xs muted text-center mt-10"
      >
        No tasks here yet
      </p>
    </section>
    <UiPagination
      v-if="pagination.last_page > 1"
      class="col-span-full"
      :meta="pagination"
      :page="page"
      @change="emit('page', $event)"
    />
  </div>
</template>
