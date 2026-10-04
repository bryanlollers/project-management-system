<script setup>
import { useReportsPage } from "~/composables/reports/useReportsPage";
import { WORKSPACE_PATHS } from "~/constants/routes";
function openSection(section) {
  return navigateTo(WORKSPACE_PATHS[section]);
}
const auth = useAuthStore();
const { state } = useReportsPage();
const { stats, activities, loading, error } = storeToRefs(state);
</script>
<template>
  <div>
    <SharedSectionHeading
      v-if="auth.user"
      section="reports"
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
      Refreshing your reports…
    </div>
    <ReportsReportCharts :stats="stats" />
    <SharedReportingActivityFeed
      :activities="activities"
      @navigate="openSection"
    />
  </div>
</template>
