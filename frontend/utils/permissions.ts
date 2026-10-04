import { USER_ROLE } from "../constants/domain.ts";
import type { Person, ResourceKind, WorkspaceRecord } from "~/types/workspace";
export function canManageResources(user: Person | null): boolean {
  return user?.role === USER_ROLE.ADMIN || user?.role === USER_ROLE.MANAGER;
}
export function canEditResource(user: Person | null, kind: ResourceKind): boolean {
  return kind === "users" ? user?.role === USER_ROLE.ADMIN : canManageResources(user);
}
export function canUpdateTask(user: Person | null, task: WorkspaceRecord): boolean {
  return !!user && (canManageResources(user) || task.assignee_id === user.id);
}
