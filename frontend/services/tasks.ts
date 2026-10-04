import { API_ENDPOINTS } from "~/constants/api";
import { createResourceService } from "./shared/resource";
import type {
  Activity,
  ApiClient,
  Comment,
  PageResult,
  ResourceResult,
  Task,
  TaskStatus,
} from "~/types/workspace";
export function createTasksService(api: ApiClient) {
  return {
    ...createResourceService<Task>(api, API_ENDPOINTS.TASKS),
    activity: (id: number) =>
      api<PageResult<Activity>>(API_ENDPOINTS.ACTIVITY, { query: { task_id: id } }),
    comment: (id: number, body: string) =>
      api<ResourceResult<Comment>>(`${API_ENDPOINTS.TASKS}/${id}/comments`, {
        method: "POST",
        body: { body },
      }),
    move: (id: number, status: TaskStatus) =>
      api<ResourceResult<Task>>(`${API_ENDPOINTS.TASKS}/${id}`, {
        method: "PATCH",
        body: { status },
      }),
  };
}
