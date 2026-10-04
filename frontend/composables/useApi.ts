import type { ApiClient, ApiOptions } from "~/types/api";
import { isUnauthorized } from "~/utils/errors";

export function useApi(): ApiClient {
  const auth = useAuthStore();
  const config = useRuntimeConfig();
  return async <T>(path: string, options: ApiOptions = {}): Promise<T> => {
    try {
      return (await $fetch<T>(`${config.public.apiBase}/${path}`, {
        ...options,
        headers: {
          Authorization: `Bearer ${auth.token}`,
          Accept: "application/json",
        },
      })) as T;
    } catch (error: unknown) {
      if (isUnauthorized(error)) {
        auth.token = null;
        auth.user = null;
      }
      throw error;
    }
  };
}
