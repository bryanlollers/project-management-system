export function useReportsPage() {
  const state = useReportsStore();
  onMounted(() => state.load());
  return { state };
}
