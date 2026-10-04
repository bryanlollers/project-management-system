<script setup>
import { WORKSPACE_PATHS } from "~/constants/routes";
import { ArrowUpRight } from "lucide-vue-next";
defineProps({ projects: { type: Array, required: true } });
function open(project) {
  void navigateTo({ path: WORKSPACE_PATHS.projects, query: { record: project.id } });
}
</script>
<template>
  <ProjectsProjectTable
    :items="projects"
    @select="open"
  >
    <template #title>
      <div class="p-5 flex justify-between items-center">
        <div>
          <h2 class="font-bold text-sm">Recent projects</h2>
          <p class="text-[10px] muted mt-1">The latest work on your radar</p>
        </div>
        <NuxtLink
          :to="WORKSPACE_PATHS.projects"
          class="text-[11px] text-[#568869] flex gap-2 items-center"
        >
          View all projects
          <ArrowUpRight :size="14" />
        </NuxtLink>
      </div>
    </template>
  </ProjectsProjectTable>
</template>
