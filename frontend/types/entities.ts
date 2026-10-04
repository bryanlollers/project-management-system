import type { USER_ROLE, PROJECT_STATUS, TASK_STATUS, PRIORITY } from "~/constants/domain";

export type UserRole = (typeof USER_ROLE)[keyof typeof USER_ROLE];
export type ProjectStatus = (typeof PROJECT_STATUS)[keyof typeof PROJECT_STATUS];
export type TaskStatus = (typeof TASK_STATUS)[keyof typeof TASK_STATUS];
export type Priority = (typeof PRIORITY)[keyof typeof PRIORITY];

export interface Contact {
  name: string;
  email: string;
  phone?: string | null;
}

/** Common read model used by the resource table and detail panel. */
export interface WorkspaceRecord {
  id: number;
  name?: string;
  title?: string;
  email?: string;
  phone?: string | null;
  company?: string | null;
  notes?: string | null;
  contacts?: Contact[] | null;
  description?: string | null;
  role?: UserRole;
  status?: ProjectStatus | TaskStatus;
  priority?: Priority;
  start_date?: string | null;
  end_date?: string | null;
  due_date?: string | null;
  client_id?: number;
  project_id?: number;
  assignee_id?: number | null;
  client?: Client;
  project?: Project;
  members?: Person[];
  assignee?: Person | null;
  user?: Person | null;
  progress?: number;
  tasks_count?: number;
  projects_count?: number;
  comments_count?: number;
  projects?: Project[];
  tasks?: Task[];
  comments?: Comment[];
  body?: string;
  created_at?: string;
}

export interface Person extends WorkspaceRecord {
  name: string;
  email: string;
  role: UserRole;
}
export interface Client extends WorkspaceRecord {
  name: string;
  email: string;
}
export interface Project extends WorkspaceRecord {
  name: string;
  client_id: number;
  status: ProjectStatus;
  priority: Priority;
}
export interface Task extends WorkspaceRecord {
  title: string;
  project_id: number;
  status: TaskStatus;
  priority: Priority;
}
export interface Comment extends WorkspaceRecord {
  task_id: number;
  body: string;
}
export interface Activity extends WorkspaceRecord {
  description: string;
}
export interface ResourceMap {
  clients: Client;
  projects: Project;
  tasks: Task;
  users: Person;
}
export type ResourceKind = keyof ResourceMap;
export interface WorkloadItem {
  id: number;
  name: string;
  tasks_count: number;
}
