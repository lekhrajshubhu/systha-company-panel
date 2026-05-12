import { http } from "@/services/http.config";

export const getEmailTemplates = async <T>(): Promise<T> => {
  const response = await http.get<T>(`/company/email-templates`);
  return response.data;
};

export const getEmailTemplateDetail = async <T>(id: number | string): Promise<T> => {
  const response = await http.get<T>(`/company/email-templates/${id}/details`);
  return response.data;
};

export const storeEmailTemplate = async <T>(data: any): Promise<T> => {
  const response = await http.post<T>(`/company/email-templates`, data);
  return response.data;
};

export const updateEmailTemplate = async <T>(id: number | string, data: any): Promise<T> => {
  const response = await http.put<T>(`/company/email-templates/${id}`, data);
  return response.data;
};
