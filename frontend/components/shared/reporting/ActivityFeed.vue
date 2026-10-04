<script setup>
import { ArrowRight, Layers } from "lucide-vue-next";
import { formatDate as date, getInitials as initials } from "~/utils/format";
defineProps({ activities: { type: Array, required: true } });
const emit = defineEmits(["navigate"]);
function navigate(section) {
  emit("navigate", section);
}
</script>
<template>
  <div class="grid lg:grid-cols-[1.6fr_1fr] gap-5 mt-6">
    <div class="panel p-5">
      <h2 class="font-bold text-sm">Recent activity</h2>
      <p class="text-[10px] muted mt-1 mb-5">Keeping everyone in the loop</p>
      <div
        v-for="a in activities.slice(0, 4)"
        :key="a.id"
        class="flex gap-3 py-3 border-b last:border-0 border-[#f0f2ef]"
      >
        <span class="avatar !w-7 !h-7">{{ initials(a.user?.name) }}</span>
        <div class="flex-1">
          <p class="text-[11px]">
            <strong class="font-semibold">{{ a.user?.name || "Team member" }}</strong>
            <span class="muted">· {{ a.description }}</span>
          </p>
          <p class="text-[9px] muted mt-1">{{ date(a.created_at) }}</p>
        </div>
        <span class="w-1 h-1 rounded-full bg-[#9bb8a3] mt-3" />
      </div>
      <p
        v-if="!activities.length"
        class="muted text-xs"
      >
        Activity will appear as your team works.
      </p>
    </div>
    <div
      class="bg-[#edf3e9] border border-[#e0e8d9] rounded-xl p-6 flex flex-col justify-center relative overflow-hidden"
    >
      <div
        class="absolute -right-8 -bottom-12 w-40 h-40 rounded-full border-[20px] border-[#e2ebdb]"
      />
      <span class="text-[#71915c] mb-4"><Layers :size="27" /></span>
      <h2 class="font-bold text-lg relative">Small steps. Big progress.</h2>
      <p class="text-xs text-[#8b9a80] leading-relaxed mt-3 max-w-[230px] relative">
        Every completed task brings your team closer to something great.
      </p>
      <button
        class="text-[11px] font-bold text-[#567b40] flex items-center gap-2 mt-6 relative"
        @click="navigate('tasks')"
      >
        See your tasks
        <ArrowRight :size="13" />
      </button>
    </div>
  </div>
</template>
