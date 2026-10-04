<script setup>
import { useTeamPage } from "~/composables/team/useTeamPage";
const auth = useAuthStore();
const { state, actions, changePage, applyFilters } = useTeamPage();
const { items, search, page, loading, error, pagination } = storeToRefs(state);
</script>
<template>
  <div>
    <SharedSectionHeading
      v-if="auth.user"
      section="users"
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
      Refreshing your team…
    </div>
    <TeamFilters
      v-model:search="search"
      :total="pagination.total"
    />
    <TeamTable
      :items="items"
      :pagination="pagination"
      :page="page"
      :filtered="!!search"
      @select="actions.showDetails"
      @page="changePage"
    />
    <SharedResourceDialogs
      kind="users"
      :actions="actions"
      :error="error"
      @clear-error="error = ''"
    />
  </div>
</template>
