import { SEARCH_DEBOUNCE_MS } from "~/constants/pagination";
interface ListState {
  search: string;
  page: number;
  load(): Promise<void>;
}
export function useListPage(state: ListState) {
  let timer: ReturnType<typeof setTimeout> | undefined;
  watch(
    () => state.search,
    () => {
      clearTimeout(timer);
      timer = setTimeout(() => {
        state.page = 1;
        void state.load();
      }, SEARCH_DEBOUNCE_MS);
    },
  );
  onScopeDispose(() => clearTimeout(timer));
  async function applyFilters() {
    clearTimeout(timer);
    state.page = 1;
    await state.load();
  }
  async function changePage(page: number) {
    clearTimeout(timer);
    state.page = page;
    await state.load();
  }
  return { applyFilters, changePage };
}
