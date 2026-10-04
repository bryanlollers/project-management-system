<script setup>
import { useProjectsPage } from "~/composables/projects/useProjectsPage";
const auth = useAuthStore();
const { state, actions, changePage, applyFilters } = useProjectsPage();
const { items, search, page, loading, error, pagination, status, clients, people } =
  storeToRefs(state);
</script>
<template>
  <div>
    <SharedSectionHeading
      v-if="auth.user"
      section="projects"
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
      Refreshing your projects…
    </div>
    <ProjectsProjectFilters
      v-model:search="search"
      v-model:status="status"
      @filter="applyFilters"
      :total="pagination.total"
    />
    <ProjectsProjectTable
      :items="items"
      :pagination="pagination"
      :page="page"
      :filtered="!!search || !!status"
      @select="actions.showDetails"
      @page="changePage"
    />
    <SharedResourceDialogs
      kind="projects"
      :actions="actions"
      :error="error"
      :clients="clients"
      :people="people"
      @clear-error="error = ''"
    />
  </div>
</template>
