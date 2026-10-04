<script setup>
import { TASK_VIEW } from "~/constants/domain";
import { useTasksPage } from "~/composables/tasks/useTasksPage";
const auth = useAuthStore();
const { state, actions, changePage, applyFilters, comment, detailActivity, addComment, moveTask } =
  useTasksPage();
const {
  items,
  search,
  page,
  loading,
  error,
  pagination,
  status,
  viewMode,
  projectFilter,
  projects,
} = storeToRefs(state);
</script>
<template>
  <div>
    <SharedSectionHeading
      v-if="auth.user"
      section="tasks"
      :user="auth.user"
      @create="actions.openEditor()"
    />
    <UiErrorAlert
      :message="error"
      @dismiss="error = ''"
    />
    <div
      v-if="loading"
      class="text-xs muted mb-4"
      role="status"
    >
      Refreshing your tasks…
    </div>
    <TasksTaskFilters
      v-model:search="search"
      v-model:status="status"
      @filter="applyFilters"
      v-model:project-filter="projectFilter"
      v-model:view-mode="viewMode"
      :projects="projects"
    />
    <TasksKanbanBoard
      v-if="viewMode === TASK_VIEW.BOARD && auth.user"
      :list="items"
      :pagination="pagination"
      :page="page"
      :user="auth.user"
      @show="(_kind, item) => actions.showDetails(item)"
      @move="moveTask"
      @page="changePage"
    />
    <TasksTaskTable
      v-else
      :items="items"
      :pagination="pagination"
      :page="page"
      :filtered="!!search || !!status"
      @select="actions.showDetails"
      @page="changePage"
    />
    <SharedResourceDialogs
      kind="tasks"
      :actions="actions"
      :error="error"
      :projects="projects"
      v-model:comment="comment"
      :detail-activity="detailActivity"
      @comment="addComment"
      @move="moveTask"
      @clear-error="error = ''"
    />
  </div>
</template>
