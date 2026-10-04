import { useSessionReset } from "~/composables/shared/useSessionReset";

/** Sidebar metadata reused from page responses; this store makes no requests. */
export const useNavigationStore = defineStore("navigation", () => {
  const projectCount = ref<number | null>(null);
  useSessionReset(() => {
    projectCount.value = null;
  });
  return { projectCount };
});
