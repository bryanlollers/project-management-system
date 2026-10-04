import type { TASK_VIEW } from "~/constants/domain";
import type {
  Contact,
  Priority,
  ProjectStatus,
  ResourceKind,
  TaskStatus,
  UserRole,
} from "./entities";

export type WorkspaceSection = ResourceKind | "dashboard" | "reports";
export type TaskViewMode = (typeof TASK_VIEW)[keyof typeof TASK_VIEW];
export interface ModalHandle {
  showModal(): void;
  close(): void;
}
/** Editable values stay strings until the payload helper normalizes empty values. */
export interface WorkspaceForm {
  name: string;
  title: string;
  company: string;
  email: string;
  password: string;
  phone: string;
  notes: string;
  contacts: Contact[];
  client_id: number | "";
  project_id: number | "";
  assignee_id: number | "";
  description: string;
  status: ProjectStatus | TaskStatus;
  priority: Priority;
  start_date: string;
  end_date: string;
  due_date: string;
  member_ids: number[];
  role: UserRole;
}
