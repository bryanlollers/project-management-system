import type { WorkloadItem, TaskStatus } from "./entities";

export interface PaginationMeta {
  current_page: number;
  last_page: number;
  total: number;
  from: number | null;
  to: number | null;
}
export interface PageResult<T> {
  data: T[];
  meta: PaginationMeta;
}
export interface ResourceResult<T> {
  data: T;
}
export interface Dashboard {
  active_projects: number;
  completed_projects: number;
  overdue_tasks: number;
  total_tasks: number;
  task_status: { status: TaskStatus; total: number }[];
  workload: WorkloadItem[];
}
export type ApiQuery = Record<string, string | number | boolean | undefined>;
export interface ApiOptions {
  method?: "GET" | "POST" | "PATCH" | "DELETE";
  body?: Record<string, unknown>;
  query?: ApiQuery;
}
export interface ApiClient {
  <T>(path: string, options?: ApiOptions): Promise<T>;
}
