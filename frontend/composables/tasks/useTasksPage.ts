import { createTasksService } from "~/services/tasks";
import { useResourceActions } from "~/composables/shared/useResourceActions";
import { useListPage } from "~/composables/shared/useListPage";
import { useResourceRoute } from "~/composables/shared/useResourceRoute";
import type { Activity, TaskStatus, WorkspaceRecord } from "~/types/workspace";
export function useTasksPage() {
  const state = useTasksStore(),
    service = createTasksService(useApi()),
    comment = ref(""),
    detailActivity = ref<Activity[]>([]);
  const actions = useResourceActions("tasks", service, {
    error: toRef(state, "error"),
    refresh: state.load,
    defaults: () => ({
      projectId: Number(state.projectFilter) || state.projects[0]?.id,
    }),
    onDetail: async (record) => {
      detailActivity.value = (await service.activity(record.id)).data;
      comment.value = "";
    },
  });
  async function addComment() {
    if (!actions.selected.value) return;
    actions.saving.value = true;
    try {
      await service.comment(actions.selected.value.id, comment.value);
      await actions.showDetails(actions.selected.value);
      await state.load();
    } catch (e: unknown) {
      actions.fail(e);
    } finally {
      actions.saving.value = false;
    }
  }
  async function moveTask(task: WorkspaceRecord, status: TaskStatus) {
    try {
      await service.move(task.id, status);
      await state.load();
      if (actions.selected.value?.id === task.id) await actions.showDetails(task);
    } catch (e: unknown) {
      actions.fail(e);
    }
  }
  useResourceRoute(actions, async () => {
    await Promise.all([state.load(), state.loadLookups()]);
  });
  return {
    state,
    actions,
    comment,
    detailActivity,
    addComment,
    moveTask,
    ...useListPage(state),
  };
}
