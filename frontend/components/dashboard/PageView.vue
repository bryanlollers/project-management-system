<script setup>
import { useDashboardPage } from "~/composables/dashboard/useDashboardPage";
import { WORKSPACE_PATHS } from "~/constants/routes";
function openSection(section) {
  return navigateTo(WORKSPACE_PATHS[section]);
}
const auth = useAuthStore();
const { state } = useDashboardPage();
const { stats, activities, loading, error, projects } = storeToRefs(state);
</script>
<template>
  <div>
    <SharedSectionHeading
      v-if="auth.user"
      section="dashboard"
      :user="auth.user"
      @create="navigateTo({ path: WORKSPACE_PATHS.projects, query: { create: '1' } })"
    />
    <UiErrorAlert
      :message="error"
      @dismiss="error = ''"
    />
    <div
      v-if="loading"
      class="text-xs muted mb-4"
      role="status"
    >
      Refreshing your dashboard…
    </div>
    <DashboardOverview :stats="stats" />
    <DashboardRecentProjects :projects="projects" />
    <SharedReportingActivityFeed
      :activities="activities"
      @navigate="openSection"
    />
  </div>
</template>
