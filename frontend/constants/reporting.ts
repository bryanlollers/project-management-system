import type { Dashboard } from "~/types/api";
export const EMPTY_DASHBOARD: Dashboard = {
  active_projects: 0,
  completed_projects: 0,
  overdue_tasks: 0,
  total_tasks: 0,
  task_status: [],
  workload: [],
};
