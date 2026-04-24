import { http } from "@/services/http.config";
import { useAppContextStore } from "@/stores/appContext";

export type CompanyGeneralSettingsPayload = {
  name: string;
  email: string;
  phone: string;
  address: {
    line_1: string;
    line_2: string;
    city: string;
    state: string;
    zip: string;
  };
};

export type CompanyGeneralSettingsResponse = {
  data: {
    company_name: string;
    company_email: string;
    company_phone: string;
    logo_url: string | null;
    address: {
      line_1: string;
      line_2: string;
      city: string;
      state: string;
      zip: string;
    };
  };
};

const getCompanyCode = (): string => {
  const store = useAppContextStore();
  const companyCode = store.user?.company?.code;

  if (!companyCode) {
    throw new Error("Company code not found in session.");
  }

  return companyCode;
};

export const getCompanyGeneralSettings = async (): Promise<CompanyGeneralSettingsResponse> => {
  const companyCode = getCompanyCode();
  const response = await http.get<CompanyGeneralSettingsResponse>(`/api/company/${companyCode}/general-settings`);
  return response.data;
};

export const updateCompanyGeneralSettings = async (
  payload: CompanyGeneralSettingsPayload,
): Promise<CompanyGeneralSettingsResponse> => {
  const companyCode = getCompanyCode();
  const response = await http.put<CompanyGeneralSettingsResponse>(`/api/company/${companyCode}/general-settings`, {
    name: payload.name ?? "",
    email: payload.email ?? "",
    phone: payload.phone ?? "",
    address: {
      line_1: payload.address?.line_1 ?? "",
      line_2: payload.address?.line_2 ?? "",
      city: payload.address?.city ?? "",
      state: payload.address?.state ?? "",
      zip: payload.address?.zip ?? "",
    },
  });

  return response.data;
};

export const updateCompanyLogo = async (
  payload: { logo?: File | null; remove_logo?: boolean },
): Promise<CompanyGeneralSettingsResponse> => {
  const companyCode = getCompanyCode();
  const formData = new FormData();

  formData.append("_method", "PUT");

  if (payload.logo instanceof File) {
    formData.append("logo", payload.logo);
  }

  if (payload.remove_logo) {
    formData.append("remove_logo", "1");
  }

  const response = await http.post<CompanyGeneralSettingsResponse>(
    `/api/company/${companyCode}/general-settings/logo`,
    formData,
  );

  return response.data;
};
