import { WORKSPACE_PATHS } from "./routes";
import {
  LayoutDashboard,
  FolderKanban,
  Users,
  CheckSquare,
  BarChart3,
  Building2,
} from "lucide-vue-next";
import type { ResourceKind, WorkspaceSection } from "~/types/workspace";

export const NAVIGATION = [
  {
    id: "dashboard",
    to: WORKSPACE_PATHS.dashboard,
    label: "Overview",
    icon: LayoutDashboard,
  },
  {
    id: "projects",
    to: WORKSPACE_PATHS.projects,
    label: "Projects",
    icon: FolderKanban,
  },
  {
    id: "tasks",
    to: WORKSPACE_PATHS.tasks,
    label: "My tasks",
    icon: CheckSquare,
  },
  {
    id: "clients",
    to: WORKSPACE_PATHS.clients,
    label: "Clients",
    icon: Building2,
  },
  {
    id: "reports",
    to: WORKSPACE_PATHS.reports,
    label: "Reports",
    icon: BarChart3,
  },
  { id: "users", to: WORKSPACE_PATHS.users, label: "Team", icon: Users },
] satisfies {
  id: WorkspaceSection;
  to: string;
  label: string;
  icon: typeof LayoutDashboard;
}[];
export const SECTION_HEADINGS: Record<WorkspaceSection, string> = {
  dashboard: "Workspace overview",
  projects: "Projects",
  tasks: "Tasks",
  clients: "Clients",
  reports: "Reports & insights",
  users: "Your team",
};
export const SECTION_DESCRIPTIONS: Record<Exclude<WorkspaceSection, "dashboard">, string> = {
  projects: "From first idea to final delivery. Keep every project on track.",
  tasks: "Turn plans into progress, one task at a time.",
  clients: "Good relationships are the foundation of great work.",
  users: "The people who make it all happen.",
  reports: "A clear picture of your team’s progress and workload.",
};
export const RESOURCE_LABELS: Record<ResourceKind, string> = {
  clients: "client",
  projects: "project",
  tasks: "task",
  users: "user",
};
