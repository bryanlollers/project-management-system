import { API_ENDPOINTS } from "~/constants/api";
import { createResourceService } from "./shared/resource";
import type { ApiClient, Person } from "~/types/workspace";
export const createTeamService = (api: ApiClient) =>
  createResourceService<Person>(api, API_ENDPOINTS.USERS);
