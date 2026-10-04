import type { WorkspaceSection } from "~/types/ui";
export const WORKSPACE_PATHS: Record<WorkspaceSection, string> = {
  dashboard: "/dashboard",
  projects: "/projects",
  tasks: "/tasks",
  clients: "/clients",
  reports: "/reports",
  users: "/team",
};

export const LOGIN_PATH = "/login";
export const DEFAULT_WORKSPACE_PATH = WORKSPACE_PATHS.dashboard;
