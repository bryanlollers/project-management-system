import { RECENT_PROJECTS_LIMIT } from "~/constants/pagination";
import { API_ENDPOINTS } from "~/constants/api";
import type { Activity, ApiClient, Dashboard, PageResult, Project } from "~/types/workspace";
export function createDashboardService(api: ApiClient) {
  return {
    summary: () => api<Dashboard>(API_ENDPOINTS.DASHBOARD),
    recentProjects: () =>
      api<PageResult<Project>>(API_ENDPOINTS.PROJECTS, {
        query: { per_page: RECENT_PROJECTS_LIMIT },
      }),
    activity: () => api<PageResult<Activity>>(API_ENDPOINTS.ACTIVITY),
  };
}
