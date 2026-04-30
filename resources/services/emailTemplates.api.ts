import { http } from "@/services/http.config";

export const getEmailTemplates = async <T>(): Promise<T> => {
  const response = await http.get<T>(`/company/email-templates`);
  return response.data;
};

export const getEmailTemplateDetail = async <T>(id: number | string): Promise<T> => {
  const response = await http.get<T>(`/company/email-templates/${id}/details`);
  return response.data;
};
