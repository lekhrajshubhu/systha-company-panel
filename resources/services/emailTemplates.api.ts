import { http } from "./http.config";
import { useAppContextStore } from "../stores/appContext";

export const getCompanyEmailTemplates = async <T>(params: any = {}): Promise<T> => {
  const store = useAppContextStore();
  const companyCode = store.user?.company?.code;

  if (!companyCode) {
    throw new Error("Company code not found in session.");
  }

  const response = await http.get<T>(`/api/company/${companyCode}/email-templates`, { params });
  return response.data;
};

export const getCompanyEmailTemplateDetail = async <T>(templateId: number): Promise<T> => {
  const store = useAppContextStore();
  const companyCode = store.user?.company?.code;

  if (!companyCode) {
    throw new Error("Company code not found in session.");
  }

  const response = await http.get<T>(`/api/company/${companyCode}/email-templates/${templateId}`);
  return response.data;
};

export const updateCompanyEmailTemplate = async <T>(
  templateId: number,
  payload: Record<string, unknown>
): Promise<T> => {
  const store = useAppContextStore();
  const companyCode = store.user?.company?.code;

  if (!companyCode) {
    throw new Error("Company code not found in session.");
  }

  const response = await http.put<T>(
    `/api/company/${companyCode}/email-templates/${templateId}`,
    payload
  );
  return response.data;
};
