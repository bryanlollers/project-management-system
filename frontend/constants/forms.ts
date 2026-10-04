import type { ResourceKind, WorkspaceForm } from "~/types/workspace";
export const FORM_FIELDS: Record<ResourceKind, (keyof WorkspaceForm)[]> = {
  clients: ["name", "company", "email", "phone", "notes", "contacts"],
  projects: [
    "name",
    "client_id",
    "description",
    "status",
    "priority",
    "start_date",
    "end_date",
    "member_ids",
  ],
  tasks: ["title", "project_id", "description", "assignee_id", "status", "priority", "due_date"],
  users: ["name", "email", "password", "role"],
};

export const FORM_LIMITS = { NAME: 160, COMMENT: 5000, PASSWORD_MIN: 12 } as const;
