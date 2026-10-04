import { createProjectsService } from "~/services/projects";
import { createClientsService } from "~/services/clients";
import { createTeamService } from "~/services/team";
import { usePagedState } from "~/composables/shared/usePagedState";
import { useSessionReset } from "~/composables/shared/useSessionReset";
import type { Client, Person, Project } from "~/types/workspace";
export const useProjectsStore = defineStore("projects", () => {
  const navigation = useNavigationStore();
  const api = useApi(),
    status = ref(""),
    clients = ref<Client[]>([]),
    people = ref<Person[]>([]);
  const state = usePagedState<Project>(
    createProjectsService(api).list,
    () => ({
      status: status.value || undefined,
    }),
    (result) => {
      if (!state.search.value && !status.value) navigation.projectCount = result.meta.total;
    },
  );
  const clientService = createClientsService(api),
    teamService = createTeamService(api);
  let sequence = 0;
  async function loadLookups() {
    const current = ++sequence;
    try {
      const [c, u] = await Promise.all([clientService.all(), teamService.all()]);
      if (current === sequence) {
        clients.value = c;
        people.value = u;
        return true;
      }
    } catch (e: unknown) {
      if (current === sequence) state.fail(e);
    }
    return false;
  }
  function reset() {
    sequence++;
    state.reset();
    status.value = "";
    clients.value = [];
    people.value = [];
  }
  useSessionReset(reset);
  return { ...state, status, clients, people, loadLookups, reset };
});
