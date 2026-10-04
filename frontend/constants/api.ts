export const API_ENDPOINTS = {
  CLIENTS: "clients",
  PROJECTS: "projects",
  TASKS: "tasks",
  USERS: "users",
  DASHBOARD: "dashboard",
  ACTIVITY: "activity",
  LOGIN: "login",
  LOGOUT: "logout",
  ME: "me",
} as const;
export const HTTP_STATUS = { UNAUTHORIZED: 401, FOUND: 302, SERVICE_UNAVAILABLE: 503 } as const;
