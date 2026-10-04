import { HTTP_STATUS } from "~/constants/api";
import { LOGIN_PATH } from "~/constants/routes";
import { getLoginRedirect } from "~/utils/navigation";
export default defineNuxtRouteMiddleware(async (to) => {
  const auth = useAuthStore();
  if (auth.token && !auth.user) {
    try {
      await auth.restore();
    } catch {
      throw createError({
        statusCode: HTTP_STATUS.SERVICE_UNAVAILABLE,
        statusMessage: "Unable to connect to the workspace API. Please try again.",
      });
    }
  }
  if (to.path === LOGIN_PATH) {
    if (auth.user) return navigateTo(getLoginRedirect(to.query.redirect));
    return;
  }
  if (!auth.user) return navigateTo({ path: LOGIN_PATH, query: { redirect: to.fullPath } });
});
