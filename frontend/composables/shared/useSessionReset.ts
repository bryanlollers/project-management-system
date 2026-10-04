export function useSessionReset(reset: () => void) {
  const auth = useAuthStore();
  watch(
    () => auth.user?.id,
    () => reset(),
  );
}
