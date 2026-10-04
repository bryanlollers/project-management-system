import { API_ENDPOINTS } from "~/constants/api";
import { createResourceService } from "./shared/resource";
import type { ApiClient, Client } from "~/types/workspace";
export const createClientsService = (api: ApiClient) =>
  createResourceService<Client>(api, API_ENDPOINTS.CLIENTS);
