import { http } from "@/services/http.config";

export const getCompanySubscribers = async <T>(params: any = {}): Promise<T> => {
  const response = await http.get<T>(`/company/subscribers`, { params });
  return response.data;
};

export const getCompanySubscriptionDetail = async <T>(id: number | string): Promise<T> => {
  const response = await http.get<T>(`/company/subscribers/${id}`);
  return response.data;
};
