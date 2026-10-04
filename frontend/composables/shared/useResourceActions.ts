import type { Ref } from "vue";
import type { ResourceService } from "~/services/shared/resource";
import type { ModalHandle, ResourceKind, WorkspaceRecord } from "~/types/workspace";
import { buildResourcePayload, createWorkspaceForm } from "~/utils/forms";
import { getErrorMessage } from "~/utils/errors";
interface ActionOptions {
  error: Ref<string>;
  refresh(): Promise<void>;
  defaults?(): { clientId?: number; projectId?: number };
  beforeEdit?(): Promise<boolean>;
  onDetail?(record: WorkspaceRecord): Promise<void>;
}
export function useResourceActions(
  kind: ResourceKind,
  service: ResourceService,
  options: ActionOptions,
) {
  const editor = ref<ModalHandle>(),
    details = ref<ModalHandle>(),
    deleting = ref<ModalHandle>(),
    saving = ref(false),
    editingId = ref<number | null>(null),
    form = ref(createWorkspaceForm(kind));
  const selected = shallowRef<WorkspaceRecord | null>(null),
    deleteTarget = shallowRef<WorkspaceRecord | null>(null);
  let sequence = 0,
    active = true;
  function fail(cause: unknown) {
    if (active) options.error.value = getErrorMessage(cause);
  }
  async function openEditor(item?: WorkspaceRecord) {
    options.error.value = "";
    try {
      if (options.beforeEdit && !(await options.beforeEdit())) return;
    } catch (cause: unknown) {
      fail(cause);
      return;
    }
    if (!active) return;
    editingId.value = item?.id ?? null;
    options.error.value = "";
    form.value = createWorkspaceForm(kind, item, options.defaults?.());
    editor.value?.showModal();
  }
  async function showDetails(item: Pick<WorkspaceRecord, "id">) {
    const current = ++sequence;
    options.error.value = "";
    try {
      const record = (await service.detail(item.id)).data;
      if (!active || current !== sequence) return;
      await options.onDetail?.(record);
      if (!active || current !== sequence) return;
      selected.value = record;
      details.value?.showModal();
    } catch (cause: unknown) {
      if (current === sequence) fail(cause);
    }
  }
  async function save() {
    saving.value = true;
    options.error.value = "";
    try {
      await service.save(
        editingId.value,
        buildResourcePayload(kind, form.value, editingId.value !== null),
      );
      if (!active) return;
      editor.value?.close();
      await options.refresh();
      if (active && selected.value?.id === editingId.value) await showDetails(selected.value);
    } catch (cause: unknown) {
      fail(cause);
    } finally {
      saving.value = false;
    }
  }
  function askDelete(item: WorkspaceRecord) {
    options.error.value = "";
    deleteTarget.value = item;
    deleting.value?.showModal();
  }
  async function remove() {
    if (!deleteTarget.value) return;
    saving.value = true;
    options.error.value = "";
    try {
      await service.remove(deleteTarget.value.id);
      if (!active) return;
      deleting.value?.close();
      details.value?.close();
      selected.value = null;
      await options.refresh();
    } catch (cause: unknown) {
      fail(cause);
    } finally {
      saving.value = false;
    }
  }
  onScopeDispose(() => {
    active = false;
    sequence++;
  });
  return {
    editor,
    details,
    deleting,
    saving,
    editingId,
    form,
    selected,
    deleteTarget,
    openEditor,
    showDetails,
    save,
    askDelete,
    remove,
    fail,
  };
}
export type ResourceActions = ReturnType<typeof useResourceActions>;
