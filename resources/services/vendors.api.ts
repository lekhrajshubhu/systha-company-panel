import { http } from "@/services/http.config";

export const getCompanyVendors = async <T>(params: any = {}): Promise<T> => {
  const response = await http.get<T>(`/company/vendors`, { params });
  return response.data;
};

export const getCompanyVendorDetail = async <T>(id: number | string): Promise<T> => {
  const response = await http.get<T>(`/company/vendors/${id}/details`);
  return response.data;
};

export const createCompanyVendor = async <T>(payload: any): Promise<T> => {
  const response = await http.post<T>(`/company/vendors`, payload);
  return response.data;
};
