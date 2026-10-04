import type { Priority, ProjectStatus, TaskStatus, UserRole } from "~/types/entities";
export const USER_ROLE = { ADMIN: "admin", MANAGER: "manager", STAFF: "staff" } as const;
export const PROJECT_STATUS = {
  PLANNING: "planning",
  ACTIVE: "active",
  ON_HOLD: "on_hold",
  COMPLETED: "completed",
} as const;
export const TASK_STATUS = {
  TODO: "todo",
  IN_PROGRESS: "in_progress",
  REVIEW: "review",
  DONE: "done",
} as const;
export const PRIORITY = { LOW: "low", MEDIUM: "medium", HIGH: "high", URGENT: "urgent" } as const;
export const TASK_VIEW = { BOARD: "board", LIST: "list" } as const;
export const TASK_VIEW_MODES = Object.values(TASK_VIEW);
export const PROJECT_STATUSES: ProjectStatus[] = Object.values(PROJECT_STATUS);
export const TASK_STATUSES: TaskStatus[] = Object.values(TASK_STATUS);
export const PRIORITIES: Priority[] = Object.values(PRIORITY);
export const USER_ROLES: UserRole[] = Object.values(USER_ROLE);
export const TASK_COLUMNS: { id: TaskStatus; label: string; color: string }[] = [
  { id: "todo", label: "To do", color: "#a1aaa5" },
  { id: "in_progress", label: "In progress", color: "#6792cf" },
  { id: "review", label: "In review", color: "#c9aa54" },
  { id: "done", label: "Done", color: "#57a07b" },
];
export const TASK_CHART_COLORS: Record<TaskStatus, string> = {
  todo: "#e1e8e3",
  in_progress: "#74a58b",
  review: "#c9d9a5",
  done: "#2b7b52",
};
