import type { PaginationMeta } from "~/types/api";
export const PAGE_SIZE = 12;
export const BOARD_PAGE_SIZE = 100;
export const LOOKUP_PAGE_SIZE = 100;
export const SEARCH_DEBOUNCE_MS = 350;
export const EMPTY_PAGINATION: PaginationMeta = {
  current_page: 1,
  last_page: 1,
  total: 0,
  from: null,
  to: null,
};

export const RECENT_PROJECTS_LIMIT = 5;
