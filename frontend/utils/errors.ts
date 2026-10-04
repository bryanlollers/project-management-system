import { HTTP_STATUS } from "../constants/api.ts";
function isObject(value: unknown): value is Record<string, unknown> {
  return typeof value === "object" && value !== null;
}
export function isUnauthorized(error: unknown): boolean {
  return (
    isObject(error) &&
    (error.status === HTTP_STATUS.UNAUTHORIZED || error.statusCode === HTTP_STATUS.UNAUTHORIZED)
  );
}
export function getErrorMessage(error: unknown): string {
  if (isObject(error)) {
    const data = isObject(error.data) ? error.data : undefined;
    if (data && isObject(data.errors)) {
      const messages = Object.values(data.errors)
        .flat()
        .filter((value): value is string => typeof value === "string");
      if (messages.length) return messages.join(" ");
    }
    if (typeof data?.message === "string") return data.message;
    if (typeof error.message === "string") return error.message;
  }
  return "Something went wrong. Please try again.";
}
