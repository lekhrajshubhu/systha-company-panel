import { http } from "@/services/http.config";

export const fetchVendorApplications = async <T>(params: any = {}): Promise<T> => {
  const response = await http.get<T>(`/company/vendor-applications`, { params });
  return response.data;
};

export const getVendorApplicationDetail = async <T>(id: number | string): Promise<T> => {
  const response = await http.get<T>(`/company/vendor-applications/${id}/details`);
  return response.data;
};

export const applicationApproval = async <T>(id: number | string, payload: any): Promise<T> => {
  const response = await http.post<T>(`/vendor-applications/${id}/approve`, payload);
  return response.data;
};
