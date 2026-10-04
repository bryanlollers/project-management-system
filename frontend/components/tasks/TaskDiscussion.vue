<script setup>
import { FORM_LIMITS } from "~/constants/forms";
import { formatDate as date } from "~/utils/format";
import { TASK_COLUMNS as columns } from "~/constants/domain";
import { canUpdateTask } from "~/utils/permissions";
defineProps({
  selected: { type: Object, required: true },
  detailActivity: { type: Array, required: true },
  saving: { type: Boolean, required: true },
});
const auth = useAuthStore();
const comment = defineModel("comment", { type: String, required: true });
const emit = defineEmits(["comment", "move"]);
function addComment() {
  emit("comment");
}
function moveTask(task, status) {
  if (columns.some((col) => col.id === status)) emit("move", task, status);
}
</script>
<template>
  <div
    class="task-discussion mt-3 flex flex-auto flex-col min-h-0 max-md:grid max-md:grid-cols-2 max-md:grid-rows-[auto_auto_minmax(0,auto)_auto] max-md:gap-x-3"
  >
    <div class="shrink-0 max-md:col-span-full max-md:row-start-1">
      <label v-if="canUpdateTask(auth.user, selected)">Update status</label>
      <select
        v-if="canUpdateTask(auth.user, selected)"
        :value="selected.status"
        @change="moveTask(selected, $event.target.value)"
      >
        <option
          v-for="c in columns"
          :key="c.id"
          :value="c.id"
        >
          {{ c.label }}
        </option>
      </select>
    </div>
    <h3 class="shrink-0 font-bold text-sm mt-3 mb-2 max-md:col-start-1 max-md:row-start-2">
      Comments
    </h3>
    <UiScrollRegion
      label="Task comments"
      class="max-md:col-start-1 max-md:row-start-3"
      fill
    >
      <div
        v-for="c in selected.comments"
        :key="c.id"
        class="bg-[#f7f9f7] rounded-lg p-3 mb-3"
      >
        <p class="font-semibold text-xs">
          {{ c.user?.name }}
          <span class="float-right muted font-normal">{{ date(c.created_at) }}</span>
        </p>
        <p class="text-xs mt-2 whitespace-pre-wrap">{{ c.body }}</p>
      </div>
      <p
        v-if="!selected.comments?.length"
        class="text-xs muted mb-3"
      >
        No comments yet.
      </p>
    </UiScrollRegion>
    <form
      class="shrink-0 mt-2 max-md:col-span-full max-md:row-start-4"
      @submit.prevent="addComment"
    >
      <textarea
        class="min-h-16 max-h-[max(64px,min(15dvh,160px))] resize-y overflow-y-auto"
        v-model="comment"
        placeholder="Share an update…"
        rows="2"
        required
        :maxlength="FORM_LIMITS.COMMENT"
        aria-label="Comment"
      />
      <button
        class="btn mt-2"
        :disabled="saving"
      >
        Add comment
      </button>
    </form>
    <h3 class="shrink-0 font-bold text-sm mt-3 mb-2 max-md:col-start-2 max-md:row-start-2">
      Activity history
    </h3>
    <UiScrollRegion
      label="Task activity history"
      class="max-md:col-start-2 max-md:row-start-3"
      fill
    >
      <p
        v-for="a in detailActivity"
        :key="a.id"
        class="text-xs muted py-2 border-b last:border-b-0"
      >
        {{ a.user?.name }} · {{ a.description }} · {{ date(a.created_at) }}
      </p>
      <p
        v-if="!detailActivity.length"
        class="text-xs muted"
      >
        No activity yet.
      </p>
    </UiScrollRegion>
  </div>
</template>
