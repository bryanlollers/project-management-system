import { LOOKUP_PAGE_SIZE } from "~/constants/pagination";
import type {
  ApiClient,
  ApiQuery,
  PageResult,
  ResourceResult,
  WorkspaceRecord,
} from "~/types/workspace";
export function createResourceService<T extends WorkspaceRecord>(api: ApiClient, endpoint: string) {
  const list = (query: ApiQuery = {}) => api<PageResult<T>>(endpoint, { query });
  async function all(): Promise<T[]> {
    const first = await list({ per_page: LOOKUP_PAGE_SIZE });
    const records = [...first.data];
    for (let page = 2; page <= first.meta.last_page; page++)
      records.push(...(await list({ per_page: LOOKUP_PAGE_SIZE, page })).data);
    return records;
  }
  return {
    list,
    all,
    detail: (id: number) => api<ResourceResult<T>>(`${endpoint}/${id}`),
    save: (id: number | null, body: Record<string, unknown>) =>
      api<ResourceResult<T>>(`${endpoint}${id ? "/" + id : ""}`, {
        method: id ? "PATCH" : "POST",
        body,
      }),
    remove: (id: number) => api<void>(`${endpoint}/${id}`, { method: "DELETE" }),
  };
}
export type ResourceService<T extends WorkspaceRecord = WorkspaceRecord> = ReturnType<
  typeof createResourceService<T>
>;
