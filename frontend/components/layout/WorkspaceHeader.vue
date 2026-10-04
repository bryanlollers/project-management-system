<script setup>
import { DISPLAY_LOCALE, DISPLAY_TIME_ZONE } from "~/constants/app";
import { ChevronRight } from "lucide-vue-next";
import { getInitials as initials } from "~/utils/format";
import { NAVIGATION as nav } from "~/constants/workspace";
defineProps({ section: { type: String, required: true }, user: { type: Object, required: true } });
const mobileNav = defineModel("mobileNav", { type: Boolean, required: true });
const emit = defineEmits(["logout"]);
function logout() {
  emit("logout");
}
</script>
<template>
  <header
    class="h-[68px] px-6 lg:px-9 bg-white border-b border-[#e5e9e6] flex items-center justify-between"
  >
    <div class="flex items-center gap-2 text-xs muted">
      <button
        class="md:hidden text-[#247a50] font-bold mr-3"
        @click="mobileNav = !mobileNav"
      >
        ☰ orbit
      </button>
      <span class="hidden sm:inline">Workspace</span>
      <ChevronRight
        :size="12"
        class="hidden sm:block"
      />
      <span class="text-[#526359]">{{ nav.find((n) => n.id === section)?.label }}</span>
    </div>
    <div class="flex items-center gap-5">
      <span class="text-[10px] muted hidden sm:block">
        {{
          new Date().toLocaleDateString(DISPLAY_LOCALE, {
            weekday: "short",
            month: "short",
            day: "numeric",
            year: "numeric",
            timeZone: DISPLAY_TIME_ZONE,
          })
        }}
      </span>
      <span class="w-px h-5 bg-[#e5e9e6]" />
      <span class="avatar">{{ initials(user.name) }}</span>
    </div>
  </header>
  <nav
    v-if="mobileNav"
    class="bg-white p-4 border-b md:hidden"
  >
    <NuxtLink
      v-for="item in nav"
      :key="item.id"
      class="side-link"
      :to="item.to"
      @click="mobileNav = false"
    >
      {{ item.label }}
    </NuxtLink>
    <button
      class="side-link"
      @click="logout"
    >
      Sign out
    </button>
  </nav>
</template>
