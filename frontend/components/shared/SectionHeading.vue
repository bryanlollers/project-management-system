<script setup>
import { Plus } from "lucide-vue-next";
import { SECTION_HEADINGS, SECTION_DESCRIPTIONS, RESOURCE_LABELS } from "~/constants/workspace";
import { canEditResource } from "~/utils/permissions";
const props = defineProps({
  section: { type: String, required: true },
  user: { type: Object, required: true },
});
const emit = defineEmits(["create"]);
const heading = computed(() => SECTION_HEADINGS[props.section]);
const description = computed(() =>
  props.section === "dashboard"
    ? `Welcome back, ${props.user.name.split(" ")[0]}. Here’s what’s happening with your team.`
    : SECTION_DESCRIPTIONS[props.section],
);
const createKind = computed(() =>
  props.section === "dashboard" ? "projects" : props.section === "reports" ? null : props.section,
);
const canCreate = computed(
  () => createKind.value !== null && canEditResource(props.user, createKind.value),
);
</script>
<template>
  <div class="flex items-start justify-between gap-4 mb-7">
    <div>
      <p
        v-if="section === 'dashboard'"
        class="text-[11px] muted mb-2"
      >
        A little progress, every day.
      </p>
      <h1 class="text-[25px] tracking-[-.7px] font-extrabold">
        {{ heading }}
        <span
          v-if="section === 'dashboard'"
          class="ml-2 text-xl"
        >
          ✦
        </span>
      </h1>
      <p class="text-xs muted mt-2">{{ description }}</p>
    </div>
    <button
      v-if="canCreate && createKind"
      class="btn primary mt-2"
      @click="emit('create', createKind)"
    >
      <Plus :size="14" />
      New {{ RESOURCE_LABELS[createKind] }}
    </button>
  </div>
</template>
