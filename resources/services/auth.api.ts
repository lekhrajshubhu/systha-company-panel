import { http } from "./http.config";

export type CompanyLoginPayload = {
  companyCode?: string;
  login_identifier: string;
  password: string;
};

export type CompanyLoginResponse = {
  access_token?: string;
  user?: {
    id: number | string;
    name: string;
    email: string;
    role: string;
    company: {
      name: string;
      code: string;
    };
  };
  message?: string;
};

export type CompanyRefreshResponse = {
  access_token?: string;
  expires_in?: number;
};

export const loginCompany = async (
  payload: CompanyLoginPayload,
): Promise<CompanyLoginResponse> => {
  const { companyCode, ...data } = payload;

  const url = companyCode
    ? `/api/company/${companyCode}/login/password`
    : "/api/company/login/password";

  const response = await http.post<CompanyLoginResponse>(url, data);
  return response.data;
};

export const refreshCompanyToken = async (): Promise<CompanyRefreshResponse> => {
  const response = await http.post<CompanyRefreshResponse>('/api/company/refresh');
  return response.data;
};
