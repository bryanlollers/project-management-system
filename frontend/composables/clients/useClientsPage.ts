import { createClientsService } from "~/services/clients";
import { useResourceActions } from "~/composables/shared/useResourceActions";
import { useListPage } from "~/composables/shared/useListPage";
import { useResourceRoute } from "~/composables/shared/useResourceRoute";
export function useClientsPage() {
  const state = useClientsStore(),
    service = createClientsService(useApi());
  const actions = useResourceActions("clients", service, {
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
