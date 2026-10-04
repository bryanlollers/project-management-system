import { createTeamService } from "~/services/team";
import { useResourceActions } from "~/composables/shared/useResourceActions";
import { useListPage } from "~/composables/shared/useListPage";
import { useResourceRoute } from "~/composables/shared/useResourceRoute";
export function useTeamPage() {
  const state = useTeamStore(),
    service = createTeamService(useApi());
  const actions = useResourceActions("users", service, {
    error: toRef(state, "error"),
    refresh: async () => {
      await state.load();
    },
  });
  useResourceRoute(actions, async () => {
    await state.load();
  });
  return { state, actions, ...useListPage(state) };
}
