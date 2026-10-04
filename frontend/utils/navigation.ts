import { DEFAULT_WORKSPACE_PATH, WORKSPACE_PATHS } from "../constants/routes.ts";
export { WORKSPACE_PATHS } from "../constants/routes.ts";
export function getLoginRedirect(value: unknown): string {
  if (typeof value !== "string") return DEFAULT_WORKSPACE_PATH;
  const path = value.split(/[?#]/, 1)[0];
  return Object.values(WORKSPACE_PATHS).some(
    (route) => path === route || path.startsWith(route + "/"),
  )
    ? value
    : DEFAULT_WORKSPACE_PATH;
}
