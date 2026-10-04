import { API_ENDPOINTS } from "~/constants/api";
import type { Person } from "~/types/workspace";
import { isUnauthorized } from "~/utils/errors";
import { canManageResources } from "~/utils/permissions";
import { SESSION_COOKIE_NAME, SESSION_DURATION_SECONDS } from "~/constants/auth";
export const useAuthStore = defineStore("auth", () => {
  const user = ref<Person | null>(null);
  const token = useCookie<string | null>(SESSION_COOKIE_NAME, {
    sameSite: "strict",
    maxAge: SESSION_DURATION_SECONDS,
    secure: !import.meta.dev,
  });
  const canManage = computed(() => canManageResources(user.value));
  async function login(email: string, password: string) {
    const result = await $fetch<{ user: Person; token: string }>(
      `${useRuntimeConfig().public.apiBase}/${API_ENDPOINTS.LOGIN}`,
      { method: "POST", body: { email, password } },
    );
    token.value = result.token;
    user.value = result.user;
  }
  async function restore() {
    if (!token.value) return;
    try {
      user.value = (
        await $fetch<{ user: Person }>(`${useRuntimeConfig().public.apiBase}/${API_ENDPOINTS.ME}`, {
          headers: {
            Authorization: `Bearer ${token.value}`,
            Accept: "application/json",
          },
        })
      ).user;
    } catch (error: unknown) {
      if (isUnauthorized(error)) token.value = null;
      else throw error;
    }
  }
  async function logout() {
    try {
      await $fetch(`${useRuntimeConfig().public.apiBase}/${API_ENDPOINTS.LOGOUT}`, {
        method: "POST",
        headers: { Authorization: `Bearer ${token.value}` },
      });
    } finally {
      token.value = null;
      user.value = null;
    }
  }
  return { user, token, canManage, login, restore, logout };
});
