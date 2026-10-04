import { EMPTY_PAGINATION, PAGE_SIZE } from "~/constants/pagination";
import { getErrorMessage } from "~/utils/errors";
import type { ApiQuery, PageResult, PaginationMeta } from "~/types/workspace";
/** Each feature store owns a separate instance of these list mechanics. */
export function usePagedState<T>(
  fetchPage: (query: ApiQuery) => Promise<PageResult<T>>,
  query: () => ApiQuery = () => ({}),
  onLoaded?: (result: PageResult<T>) => void,
) {
  const items = shallowRef<T[]>([]),
    search = ref(""),
    page = ref(1),
    loading = ref(false),
    error = ref(""),
    pagination = ref<PaginationMeta>({ ...EMPTY_PAGINATION });
  let sequence = 0;
  function fail(cause: unknown) {
    error.value = getErrorMessage(cause);
  }
  async function load() {
    const current = ++sequence;
    loading.value = true;
    error.value = "";
    try {
      const result = await fetchPage({
        search: search.value,
        page: page.value,
        per_page: PAGE_SIZE,
        ...query(),
      });
      if (current === sequence) {
        items.value = result.data;
        pagination.value = result.meta;
        onLoaded?.(result);
      }
    } catch (cause: unknown) {
      if (current === sequence) fail(cause);
    } finally {
      if (current === sequence) loading.value = false;
    }
  }
  function reset() {
    sequence++;
    items.value = [];
    search.value = "";
    page.value = 1;
    pagination.value = { ...EMPTY_PAGINATION };
    loading.value = false;
    error.value = "";
  }
  return { items, search, page, loading, error, pagination, load, reset, fail };
}
