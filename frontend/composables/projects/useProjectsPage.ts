import { createProjectsService } from "~/services/projects";
import { useResourceActions } from "~/composables/shared/useResourceActions";
import { useListPage } from "~/composables/shared/useListPage";
import { useResourceRoute } from "~/composables/shared/useResourceRoute";
export function useProjectsPage() {
  const state = useProjectsStore(),
    service = createProjectsService(useApi());
  const actions = useResourceActions("projects", service, {
    error: toRef(state, "error"),
    beforeEdit: state.loadLookups,
    refresh: async () => {
      useNavigationStore().projectCount = null;
      await state.load();
    },
    defaults: () => ({ clientId: state.clients[0]?.id }),
  });
  useResourceRoute(actions, async () => {
    await state.load();
  });
  return { state, actions, ...useListPage(state) };
}
