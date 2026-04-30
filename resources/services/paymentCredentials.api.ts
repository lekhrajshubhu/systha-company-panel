import { http } from "@/services/http.config";

export const getCompanyPaymentCredentials = async <T>(params: any = {}): Promise<T> => {
  const response = await http.get<T>(`/company/payment-credentials`, { params });
  return response.data;
};

export const createCompanyPaymentCredential = async <T>(payload: any): Promise<T> => {
  const response = await http.post<T>(`/company/payment-credentials`, payload);
  return response.data;
};

export const getCompanyPaymentCredentialDetail = async <T>(id: number | string): Promise<T> => {
  const response = await http.get<T>(`/company/payment-credentials/${id}`);
  return response.data;
};

export const updateCompanyPaymentCredential = async <T>(id: number | string, payload: any): Promise<T> => {
  const response = await http.put<T>(`/company/payment-credentials/${id}`, payload);
  return response.data;
};

export const deleteCompanyPaymentCredential = async <T>(id: number | string): Promise<T> => {
  const response = await http.delete<T>(`/company/payment-credentials/${id}/delete`);
  return response.data;
};
