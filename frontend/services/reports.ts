import { API_ENDPOINTS } from "~/constants/api";
import type { Activity, ApiClient, Dashboard, PageResult } from "~/types/workspace";
export function createReportsService(api: ApiClient) {
  return {
    summary: () => api<Dashboard>(API_ENDPOINTS.DASHBOARD),
    activity: () => api<PageResult<Activity>>(API_ENDPOINTS.ACTIVITY),
  };
}
