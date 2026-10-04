import { TASK_VIEW } from "~/constants/domain";
import { createTasksService } from "~/services/tasks";
import { createProjectsService } from "~/services/projects";
import { usePagedState } from "~/composables/shared/usePagedState";
import { useSessionReset } from "~/composables/shared/useSessionReset";
import { BOARD_PAGE_SIZE, PAGE_SIZE } from "~/constants/pagination";
import type { Project, Task, TaskViewMode } from "~/types/workspace";
export const useTasksStore = defineStore("tasks", () => {
  const api = useApi(),
    status = ref(""),
    projectFilter = ref<string | number>(""),
    viewMode = ref<TaskViewMode>(TASK_VIEW.BOARD),
    projects = ref<Project[]>([]);
  const state = usePagedState<Task>(createTasksService(api).list, () => ({
    status: status.value || undefined,
    project_id: projectFilter.value || undefined,
    per_page: viewMode.value === TASK_VIEW.BOARD ? BOARD_PAGE_SIZE : PAGE_SIZE,
  }));
  const projectsService = createProjectsService(api);
  let sequence = 0;
  async function loadLookups() {
    const current = ++sequence;
    try {
      const data = await projectsService.all();
      if (current === sequence) projects.value = data;
    } catch (e: unknown) {
      if (current === sequence) state.fail(e);
    }
  }
  function reset() {
    sequence++;
    state.reset();
    status.value = "";
    projectFilter.value = "";
    viewMode.value = TASK_VIEW.BOARD;
    projects.value = [];
  }
  useSessionReset(reset);
  return {
    ...state,
    status,
    projectFilter,
    viewMode,
    projects,
    loadLookups,
    reset,
  };
});
