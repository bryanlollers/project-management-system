<script setup>
import { canEditResource } from "~/utils/permissions";
import { X } from "lucide-vue-next";
import { formatDate as date, getInitials as initials, formatLabel as label } from "~/utils/format";
defineProps({
  selected: { type: [Object, null], required: true },
  selectedKind: { type: String, required: true },
  detailActivity: { type: Array, required: true },
  saving: { type: Boolean, required: true },
  error: { type: String, required: true },
});
const auth = useAuthStore();
const comment = defineModel("comment", { type: String, required: true });
const emit = defineEmits(["show", "edit", "delete", "comment", "move"]);
function showDetails(kind, item) {
  emit("show", kind, item);
}
function openEditor(kind, item) {
  emit("edit", kind, item);
}
function askDelete(kind, item) {
  emit("delete", kind, item);
}
const modal = ref();
function showModal() {
  modal.value?.showModal();
}
function close() {
  modal.value?.close();
}
defineExpose({ showModal, close });
</script>
<template>
  <UiModal ref="modal">
    <div
      v-if="selected"
      class="p-7"
    >
      <div class="flex justify-between gap-3">
        <div>
          <p class="text-xs muted capitalize mb-1">{{ selectedKind.slice(0, -1) }} details</p>
          <h2 class="font-bold text-xl">
            {{ selected.name || selected.title }}
          </h2>
        </div>
        <button
          @click="close()"
          aria-label="Close"
        >
          <X :size="20" />
        </button>
      </div>
      <div class="flex gap-2 mt-4">
        <span
          v-if="selected.status"
          class="badge"
          :class="selected.status"
        >
          {{ label(selected.status) }}
        </span>
        <span
          v-if="selected.priority"
          class="badge"
          :class="selected.priority"
        >
          {{ selected.priority }}
        </span>
      </div>
      <p
        v-if="selected.description || selected.notes"
        class="text-sm text-[#7b8980] leading-relaxed whitespace-pre-wrap mt-4"
      >
        {{ selected.description || selected.notes }}
      </p>
      <div class="grid grid-cols-2 gap-4 mt-5 text-xs">
        <div v-if="selected.email">
          <label>Email</label>
          {{ selected.email }}
        </div>
        <div v-if="selected.phone">
          <label>Phone</label>
          {{ selected.phone }}
        </div>
        <div v-if="selected.role">
          <label>Role</label>
          {{ selected.role }}
        </div>
        <div v-if="selected.client">
          <label>Client</label>
          {{ selected.client.name }}
        </div>
        <div v-if="selected.project">
          <label>Project</label>
          {{ selected.project.name }}
        </div>
        <div v-if="selected.start_date">
          <label>Start date</label>
          {{ date(selected.start_date) }}
        </div>
        <div v-if="selected.end_date || selected.due_date">
          <label>Due date</label>
          {{ date(selected.end_date || selected.due_date) }}
        </div>
        <div v-if="selectedKind === 'tasks'">
          <label>Assigned to</label>
          {{ selected.assignee?.name || "Unassigned" }}
        </div>
      </div>
      <div
        v-if="selected.members"
        class="mt-5"
      >
        <label>Team</label>
        <div class="flex gap-3 flex-wrap">
          <span
            v-for="p in selected.members"
            :key="p.id"
            class="text-xs flex items-center gap-2"
          >
            <span class="avatar">{{ initials(p.name) }}</span>
            {{ p.name }}
          </span>
        </div>
      </div>
      <div
        v-if="selected.contacts?.length"
        class="mt-6"
      >
        <label>Contacts</label>
        <p
          v-for="c in selected.contacts"
          :key="c.email"
          class="text-xs py-2"
        >
          {{ c.name }} · {{ c.email }} {{ c.phone ? "· " + c.phone : "" }}
        </p>
      </div>
      <div
        v-if="selected.projects"
        class="mt-6"
      >
        <label>Associated projects</label>
        <button
          v-for="p in selected.projects"
          :key="p.id"
          class="w-full text-left border-b last:border-b-0 py-3 text-xs"
          @click="showDetails('projects', p)"
        >
          {{ p.name }}
          <span
            class="badge float-right"
            :class="p.status"
          >
            {{ label(p.status) }}
          </span>
        </button>
        <p
          v-if="!selected.projects.length"
          class="text-xs muted"
        >
          No projects yet.
        </p>
      </div>
      <div
        v-if="selected.tasks"
        class="mt-6"
      >
        <label>Project tasks · {{ selected.progress }}% complete</label>
        <div class="progress my-3">
          <span :style="{ width: selected.progress + '%' }" />
        </div>
        <button
          v-for="t in selected.tasks"
          :key="t.id"
          class="w-full text-left border-b last:border-b-0 py-3 text-xs"
          @click="showDetails('tasks', t)"
        >
          {{ t.title }}
          <span
            class="badge float-right"
            :class="t.status"
          >
            {{ label(t.status) }}
          </span>
        </button>
      </div>
      <TasksTaskDiscussion
        v-if="selectedKind === 'tasks' && selected"
        :selected="selected"
        :detail-activity="detailActivity"
        :saving="saving"
        v-model:comment="comment"
        @comment="emit('comment')"
        @move="(task, status) => emit('move', task, status)"
      />
      <p
        v-if="error"
        class="text-red-600 text-xs mt-4"
        role="alert"
      >
        {{ error }}
      </p>
      <div
        v-if="canEditResource(auth.user, selectedKind)"
        class="flex justify-end gap-3 border-t mt-7 pt-5"
      >
        <button
          class="btn text-red-600"
          @click="askDelete(selectedKind, selected)"
        >
          Delete
        </button>
        <button
          class="btn primary"
          @click="openEditor(selectedKind, selected)"
        >
          Edit
          {{ selectedKind === "users" ? "user" : selectedKind.slice(0, -1) }}
        </button>
      </div>
    </div>
  </UiModal>
</template>
