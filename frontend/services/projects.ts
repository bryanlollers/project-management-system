import { API_ENDPOINTS } from "~/constants/api";
import { createResourceService } from "./shared/resource";
import type { ApiClient, Project } from "~/types/workspace";
export const createProjectsService = (api: ApiClient) =>
  createResourceService<Project>(api, API_ENDPOINTS.PROJECTS);
