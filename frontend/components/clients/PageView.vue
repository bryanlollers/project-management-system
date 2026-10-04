<script setup>
import { useClientsPage } from "~/composables/clients/useClientsPage";
const auth = useAuthStore();
const { state, actions, changePage, applyFilters } = useClientsPage();
const { items, search, page, loading, error, pagination } = storeToRefs(state);
</script>
<template>
  <div>
    <SharedSectionHeading
      v-if="auth.user"
      section="clients"
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
      Refreshing your clients…
    </div>
    <ClientsClientFilters
      v-model:search="search"
      :total="pagination.total"
    />
    <ClientsClientTable
      :items="items"
      :pagination="pagination"
      :page="page"
      :filtered="!!search"
      @select="actions.showDetails"
      @page="changePage"
    />
    <SharedResourceDialogs
      kind="clients"
      :actions="actions"
      :error="error"
      @clear-error="error = ''"
    />
  </div>
</template>
