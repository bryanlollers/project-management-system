import { createReportsService } from "~/services/reports";
import { useSessionReset } from "~/composables/shared/useSessionReset";
import { EMPTY_DASHBOARD } from "~/constants/reporting";
import { getErrorMessage } from "~/utils/errors";
import type { Activity, Dashboard } from "~/types/workspace";
export const useReportsStore = defineStore("reports", () => {
  const service = createReportsService(useApi()),
    stats = ref<Dashboard>({
      ...EMPTY_DASHBOARD,
      task_status: [],
      workload: [],
    }),
    activities = ref<Activity[]>([]),
    loading = ref(false),
    error = ref("");
  let sequence = 0;
  async function load() {
    const current = ++sequence;
    loading.value = true;
    error.value = "";
    try {
      const [summary, activity] = await Promise.all([service.summary(), service.activity()]);
      if (current === sequence) {
        stats.value = summary;
        activities.value = activity.data;
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
  }
  useSessionReset(reset);
  return { stats, activities, loading, error, load, reset };
});
