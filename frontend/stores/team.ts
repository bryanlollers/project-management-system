import { createTeamService } from "~/services/team";
import { usePagedState } from "~/composables/shared/usePagedState";
import { useSessionReset } from "~/composables/shared/useSessionReset";
import type { Person } from "~/types/workspace";
export const useTeamStore = defineStore("team", () => {
  const state = usePagedState<Person>(createTeamService(useApi()).list);
  useSessionReset(state.reset);
  return state;
});
