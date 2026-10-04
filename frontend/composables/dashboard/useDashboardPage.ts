export function useDashboardPage() {
  const state = useDashboardStore();
  onMounted(() => state.load());
  return { state };
}
