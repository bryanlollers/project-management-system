<script setup>
import { WORKSPACE_PATHS } from "~/constants/routes";
const props = defineProps({
  kind: { type: String, required: true },
  actions: { type: Object, required: true },
  error: { type: String, required: true },
  clients: { type: Array, required: false, default: () => [] },
  projects: { type: Array, required: false, default: () => [] },
  people: { type: Array, required: false, default: () => [] },
  detailActivity: { type: Array, required: false, default: () => [] },
});
const {
  editor,
  details,
  deleting,
  saving,
  editingId,
  form,
  selected,
  deleteTarget,
  openEditor,
  save,
  showDetails,
  askDelete,
  remove,
} = props.actions;
const comment = defineModel("comment", { type: String, default: "" });
const emit = defineEmits(["clearError", "comment", "move"]);
function openRelated(kind, item) {
  if (kind === props.kind) void showDetails(item);
  else
    void navigateTo({
      path: WORKSPACE_PATHS[kind],
      query: { record: item.id },
    });
}
</script>
<template>
  <SharedResourceEditor
    ref="editor"
    v-model:form="form"
    :editing-kind="kind"
    :editing-id="editingId"
    :clients="clients"
    :projects="projects"
    :people="people"
    :saving="saving"
    :error="error"
    @save="save"
    @clear-error="emit('clearError')"
  />
  <SharedResourceDetails
    ref="details"
    :selected="selected"
    :selected-kind="kind"
    :detail-activity="detailActivity"
    :saving="saving"
    :error="error"
    v-model:comment="comment"
    @show="openRelated"
    @edit="(_kind, item) => openEditor(item)"
    @delete="(_kind, item) => askDelete(item)"
    @comment="emit('comment')"
    @move="(task, status) => emit('move', task, status)"
  />
  <SharedDeleteConfirmation
    ref="deleting"
    :delete-target="deleteTarget"
    :delete-kind="kind"
    :saving="saving"
    :error="error"
    @confirm="remove"
  />
</template>
