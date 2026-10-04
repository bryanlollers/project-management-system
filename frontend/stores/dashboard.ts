import { createDashboardService } from "~/services/dashboard";
import { useSessionReset } from "~/composables/shared/useSessionReset";
import { EMPTY_DASHBOARD } from "~/constants/reporting";
import { getErrorMessage } from "~/utils/errors";
import type { Activity, Dashboard, Project } from "~/types/workspace";
export const useDashboardStore = defineStore("dashboard", () => {
  const navigation = useNavigationStore();
  const service = createDashboardService(useApi()),
    stats = ref<Dashboard>({
      ...EMPTY_DASHBOARD,
      task_status: [],
      workload: [],
    }),
    activities = ref<Activity[]>([]),
    loading = ref(false),
    error = ref("");
  const projects = ref<Project[]>([]);
  let sequence = 0;
  async function load() {
    const current = ++sequence;
    loading.value = true;
    error.value = "";
    try {
      const [summary, activity, recent] = await Promise.all([
        service.summary(),
        service.activity(),
        service.recentProjects(),
      ]);
      if (current === sequence) {
        stats.value = summary;
        activities.value = activity.data;
        projects.value = recent.data;
        navigation.projectCount = recent.meta.total;
      }
    } catch (e: unknown) {
      if (current === sequence) error.value = getErrorMessage(e);
    } finally {
      if (current === sequence) loading.value = false;
    }
  }
  function reset() {
    sequence++;
    stats.value = { ...EMPTY_DASHBOARD, task_status: [], workload: [] };
    activities.value = [];
    loading.value = false;
    error.value = "";
    projects.value = [];
  }
  useSessionReset(reset);
  return { stats, activities, loading, error, load, reset, projects };
});
