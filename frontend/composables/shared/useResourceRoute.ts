import type { ResourceActions } from "./useResourceActions";
export function useResourceRoute(actions: ResourceActions, initialize: () => Promise<void>) {
  const route = useRoute();
  let alive = true;
  onScopeDispose(() => {
    alive = false;
  });
  function openRecord() {
    const id = Number(route.query.record);
    if (Number.isInteger(id) && id > 0) void actions.showDetails({ id });
  }
  onMounted(async () => {
    await initialize();
    if (!alive) return;
    openRecord();
    if (route.query.create === "1") actions.openEditor();
  });
  watch(() => route.query.record, openRecord);
}
