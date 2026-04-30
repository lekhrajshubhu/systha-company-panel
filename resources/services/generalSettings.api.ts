import { http } from "@/services/http.config";

export type CompanyGeneralSettingsPayload = {
  company: {
    name: string;
    email?: string | null;
    phone?: string | null;
    address: {
      line1: string;
      line2?: string | null;
      city: string;
      state: string;
      zip: string;
      lat?: number | null;
      lng?: number | null;
    };
  };
};

export type CompanyGeneralSettingsResponse = {
  data: {
    company_name?: string;
    company_email?: string;
    company_phone?: string;
    logo_url?: string | null;
    address?: {
      line_1?: string;
      line_2?: string;
      city?: string;
      state?: string;
      zip?: string;
    };
    company?: {
      name?: string;
      email?: string | null;
      phone?: string | null;
      address?: {
        line1?: string;
        line2?: string | null;
        city?: string;
        state?: string;
        zip?: string;
        lat?: number | null;
        lng?: number | null;
      };
    };
  };
};

export const getCompanyGeneralSettings = async (): Promise<CompanyGeneralSettingsResponse> => {
  const response = await http.get<CompanyGeneralSettingsResponse>(`/company/info`);
  return response.data;
};

export const updateCompanyGeneralSettings = async (
  payload: CompanyGeneralSettingsPayload,
): Promise<CompanyGeneralSettingsResponse> => {
  const response = await http.put<CompanyGeneralSettingsResponse>(`/company/settings`, payload);

  return response.data;
};

export const updateCompanygeneralSettings = updateCompanyGeneralSettings;

export const updateCompanyLogo = async (
  payload: { logo?: File | null; remove_logo?: boolean },
): Promise<CompanyGeneralSettingsResponse> => {
  const formData = new FormData();

  formData.append("_method", "PUT");

  if (payload.logo instanceof File) {
    formData.append("logo", payload.logo);
  }

  if (payload.remove_logo) {
    formData.append("remove_logo", "1");
  }

  const response = await http.post<CompanyGeneralSettingsResponse>(
    `/company/update-logo`,
    formData,
  );

  return response.data;
};
