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
  <div class="mt-5">
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
    <h3 class="font-bold text-sm mt-7 mb-4">Comments</h3>
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
    <form @submit.prevent="addComment">
      <textarea
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
    <h3 class="font-bold text-sm mt-6 mb-3">Activity history</h3>
    <p
      v-for="a in detailActivity"
      :key="a.id"
      class="text-xs muted py-2 border-b last:border-b-0"
    >
      {{ a.user?.name }} · {{ a.description }} · {{ date(a.created_at) }}
    </p>
  </div>
</template>
