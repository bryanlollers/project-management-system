import { DISPLAY_LOCALE } from "../constants/app.ts";
export function formatDate(value?: string | null): string {
  return value
    ? new Date(value.slice(0, 10) + "T12:00:00").toLocaleDateString(DISPLAY_LOCALE, {
        month: "short",
        day: "numeric",
      })
    : "—";
}
export function getInitials(name?: string): string {
  return (name?.trim() || "?")
    .split(/\s+/)
    .map((part) => part[0])
    .slice(0, 2)
    .join("");
}
export function formatLabel(value?: string): string {
  return (value || "").replaceAll("_", " ");
}
