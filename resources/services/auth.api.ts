import { http } from "@/services/http.config";
import { setAuthToken } from "@/services/authToken.storage";
export { AUTH_TOKEN_KEY } from "@/services/authToken.storage";

export type CompanyLoginPayload = {
  email: string;
  password: string;
};

export type ApiEnvelope<T> = {
  data: T;
  success: boolean;
  message?: string;
};

export type ContextRoleOption = {
  label: string;
  value: string;
};

export type CompanyClient = {
  id: number | string;
  avatar?: string | null;
  first_name?: string;
  last_name?: string;
  email?: string;
  phone?: string;
};

export type LoginContext = {
  key: string;
  type: string;
  roles: string[];
  permissions?: string[];
  company?: {
    code: string;
    name: string;
    logo?: string;
  };
  vendor?: {
    code: string;
    name: string;
    logo?: string | null;
  };
  client_id?: number | string;
  label?: string;
  [key: string]: any;
};

export type ActiveLoginContext = Omit<LoginContext, "roles" | "permissions"> & {
  roles: Array<string | ContextRoleOption>;
  permissions: string[];
};

export type CompanyLoginData = {
  token: string;
  token_type?: string;
  expires_in?: number;
  client?: CompanyClient;
  contexts?: LoginContext[];
  requires_context_selection?: boolean;
  active_context?: ActiveLoginContext | null;
};

export type CompanyRefreshData = {
  token?: string;
  token_type?: string;
  expires_in?: number;
};

export type CompanyLogoutData = {
  message?: string;
};

export type CompanySelectContextPayload = {
  key?: string;
  context_key?: string;
};

export type CompanySelectContextData = {
  token?: string;
  token_type?: string;
  expires_in?: number;
  active_context?: ActiveLoginContext | null;
  contexts?: LoginContext[];
  client?: CompanyClient;
};

export type CompanySwitchContextPayload = CompanySelectContextPayload;
export type CompanySwitchContextData = CompanySelectContextData;

export type CompanyProfileData = {
  client?: CompanyClient;
  contexts?: LoginContext[];
  active_context?: ActiveLoginContext | null;
};

const unwrap = <T>(payload: ApiEnvelope<T> | T): T => {
  if (
    payload &&
    typeof payload === "object" &&
    "data" in payload &&
    typeof (payload as ApiEnvelope<T>).data !== "undefined"
  ) {
    return (payload as ApiEnvelope<T>).data;
  }
  return payload as T;
};

export const loginCompany = async (payload: CompanyLoginPayload): Promise<CompanyLoginData> => {
  // NOTE: http.baseURL already includes `/api/v1/platform` in most envs.
  // Using a leading slash would drop the baseURL path portion in axios.
  const url = "company/login";

  const response = await http.post<ApiEnvelope<CompanyLoginData> | CompanyLoginData>(url, payload);
  return unwrap(response.data);
};

export const refreshCompanyToken = async (): Promise<CompanyRefreshData> => {
  const response = await http.post<ApiEnvelope<CompanyRefreshData> | CompanyRefreshData>(
    "/api/company/refresh",
  );
  return unwrap(response.data);
};

export const logoutCompany = async (): Promise<CompanyLogoutData> => {
  const response = await http.post<ApiEnvelope<CompanyLogoutData> | CompanyLogoutData>(
    "company/logout",
  );
  return unwrap(response.data);
};

export const selectCompanyContext = async (
  payload: CompanySelectContextPayload,
): Promise<CompanySelectContextData> => {
  const response = await http.post<ApiEnvelope<CompanySelectContextData> | CompanySelectContextData>(
    "company/select-context",
    payload,
  );
  return unwrap(response.data);
};

export const switchCompanyContext = async (
  payload: CompanySwitchContextPayload,
): Promise<CompanySwitchContextData> => {
  const response = await http.post<ApiEnvelope<CompanySwitchContextData> | CompanySwitchContextData>(
    "company/switch-context",
    payload,
  );
  return unwrap(response.data);
};

export const getCompanyProfile = async (): Promise<CompanyProfileData> => {
  const response = await http.get<ApiEnvelope<CompanyProfileData> | CompanyProfileData>(
    "company/profile",
  );
  return unwrap(response.data);
};

export const AUTH_PENDING_CONTEXT_SELECTION_KEY = 'auth_requires_context_selection'
export const AUTH_CONTEXTS_KEY = 'auth_contexts'
export const AUTH_ACTIVE_CONTEXT_KEY = 'auth_active_context'

export function saveAuthToken(token?: string) {
  if (!token) return
  setAuthToken(token)
  http.defaults.headers.common.Authorization = `Bearer ${token}`
  window.dispatchEvent(new Event('auth-changed'))
}


export function saveActiveContext(context?: Partial<ActiveLoginContext> | null) {
  if (!context || typeof context !== 'object') return
  localStorage.setItem(AUTH_ACTIVE_CONTEXT_KEY, JSON.stringify(context))
  window.dispatchEvent(new Event('auth-changed'))
}
