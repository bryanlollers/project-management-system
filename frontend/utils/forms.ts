import { FORM_FIELDS } from "../constants/forms.ts";
import { USER_ROLE, PROJECT_STATUS, TASK_STATUS, PRIORITY } from "../constants/domain.ts";
import type { ResourceKind, WorkspaceForm, WorkspaceRecord } from "~/types/workspace";

export function createWorkspaceForm(
  kind: ResourceKind,
  item?: WorkspaceRecord,
  defaults: { clientId?: number; projectId?: number } = {},
): WorkspaceForm {
  return {
    name: item?.name ?? "",
    title: item?.title ?? "",
    company: item?.company ?? "",
    email: item?.email ?? "",
    password: "",
    phone: item?.phone ?? "",
    notes: item?.notes ?? "",
    contacts: item?.contacts?.map((contact) => ({ ...contact })) ?? [],
    client_id: item?.client_id ?? defaults.clientId ?? "",
    project_id: item?.project_id ?? defaults.projectId ?? "",
    assignee_id: item?.assignee_id ?? "",
    description: item?.description ?? "",
    status: item?.status ?? (kind === "projects" ? PROJECT_STATUS.PLANNING : TASK_STATUS.TODO),
    priority: item?.priority ?? PRIORITY.MEDIUM,
    start_date: item?.start_date ?? "",
    end_date: item?.end_date ?? "",
    due_date: item?.due_date ?? "",
    member_ids: item?.members?.map((member) => member.id) ?? [],
    role: item?.role ?? USER_ROLE.STAFF,
  };
}
export function buildResourcePayload(
  kind: ResourceKind,
  form: WorkspaceForm,
  isEditing: boolean,
): Record<string, unknown> {
  const payload: Record<string, unknown> = {};
  for (const key of FORM_FIELDS[kind]) {
    if (kind === "users" && key === "password" && isEditing && !form.password) continue;
    payload[key] = form[key] === "" ? null : form[key];
  }
  return payload;
}
