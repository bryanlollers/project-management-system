import { createClientsService } from "~/services/clients";
import { usePagedState } from "~/composables/shared/usePagedState";
import { useSessionReset } from "~/composables/shared/useSessionReset";
import type { Client } from "~/types/workspace";
export const useClientsStore = defineStore("clients", () => {
  const state = usePagedState<Client>(createClientsService(useApi()).list);
  useSessionReset(state.reset);
  return state;
});
