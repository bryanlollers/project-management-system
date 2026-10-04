<script setup>
import { WORKSPACE_PATHS } from "~/constants/routes";
import { ChevronDown, ArrowRight, LogOut, Building2, Layers } from "lucide-vue-next";
import { getInitials as initials } from "~/utils/format";
import { NAVIGATION as nav } from "~/constants/workspace";
defineProps({
  section: { type: String, required: true },
  user: { type: Object, required: true },
  projectCount: { type: [Number, null], required: true },
});
const emit = defineEmits(["logout"]);
function logout() {
  emit("logout");
}
</script>
<template>
  <aside
    class="desktop-sidebar w-[224px] shrink-0 bg-white border-r border-[#e4e8e5] flex flex-col fixed inset-y-0 z-20 px-4"
  >
    <NuxtLink
      :to="WORKSPACE_PATHS.dashboard"
      class="brand flex items-center gap-2.5 text-[27px] font-extrabold text-[#27784f] px-4 h-[85px]"
    >
      <Layers
        :size="27"
        :stroke-width="2.4"
      />
      orbit
      <span class="text-[9px] self-end mb-[26px] text-[#a5b8ac] tracking-widest font-medium">
        WORK
      </span>
    </NuxtLink>
    <div class="border rounded-lg p-3 flex items-center gap-2.5 mt-2 mb-8">
      <span class="w-8 h-8 rounded-md bg-[#eef3ee] grid place-items-center text-[#78927c]">
        <Building2 :size="17" />
      </span>
      <div class="flex-1">
        <p class="text-xs font-bold">Orbit workspace</p>
        <p class="text-[10px] muted mt-0.5">Team workspace</p>
      </div>
      <ChevronDown
        :size="13"
        class="muted"
      />
    </div>
    <p class="uppercase tracking-[.12em] text-[9px] text-[#a5ada8] px-4 mb-3">Workspace</p>
    <nav class="space-y-1">
      <NuxtLink
        v-for="item in nav"
        :key="item.id"
        :class="['side-link', { active: section === item.id }]"
        :to="item.to"
        :aria-current="section === item.id ? 'page' : undefined"
      >
        <component
          :is="item.icon"
          :size="17"
          :stroke-width="1.7"
        />
        {{ item.label }}
        <span
          v-if="item.id === 'projects' && projectCount !== null"
          class="ml-auto text-[10px] bg-[#eef1ef] px-1.5 rounded"
        >
          {{ projectCount }}
        </span>
      </NuxtLink>
    </nav>
    <div class="mt-9 px-4">
      <p class="uppercase tracking-[.12em] text-[9px] text-[#a5ada8] mb-4">Your workspace</p>
      <div class="text-xs text-[#859089] flex items-center gap-2 mb-4">
        <span class="w-1.5 h-1.5 rounded-full bg-[#66a280]" />
        Client projects
      </div>
      <div class="text-xs text-[#859089] flex items-center gap-2">
        <span class="w-1.5 h-1.5 rounded-full bg-[#9cafd2]" />
        Internal projects
      </div>
    </div>
    <div class="mt-auto">
      <div class="bg-[#f6f8f4] border border-[#e8eddf] rounded-xl p-4 mb-5">
        <span class="text-[#7a9762]"><Layers :size="20" /></span>
        <p class="font-semibold text-xs mt-3">A shared space for great work</p>
        <p class="text-[10px] leading-relaxed muted mt-2">
          Keep your team aligned, one project at a time.
        </p>
        <NuxtLink
          :to="WORKSPACE_PATHS.projects"
          class="text-[10px] text-[#397c54] font-bold flex items-center gap-2 mt-3"
        >
          Explore projects
          <ArrowRight :size="12" />
        </NuxtLink>
      </div>
      <button
        class="side-link mb-4"
        @click="logout"
      >
        <LogOut :size="16" />
        Sign out
      </button>
      <div class="border-t py-5 flex items-center gap-2.5">
        <span class="avatar !w-9 !h-9">{{ initials(user.name) }}</span>
        <div class="flex-1">
          <p class="font-semibold text-xs">{{ user.name }}</p>
          <p class="text-[10px] muted capitalize">{{ user.role }}</p>
        </div>
        <ChevronDown
          :size="14"
          class="muted"
        />
      </div>
    </div>
  </aside>
</template>
