import { http } from "@/services/http.config";

export const getCompanyPlans = async <T>(params: any = {}): Promise<T> => {
  const response = await http.get<T>(`/company/plans`, { params });
  return response.data;
};

export const getCompanyPlanDetail = async <T>(id: number | string): Promise<T> => {
  const response = await http.get<T>(`/company/plans/${id}`);
  return response.data;
};

export const connectStripe = async <T>(id: number | string): Promise<T> => {
  const response = await http.post<T>(`/company/plans/${id}/connect-stripe`);
  return response.data;
};

export const createCompanyPlan = async <T>(payload: any): Promise<T> => {
  const response = await http.post<T>(`/company/plans`, payload);
  return response.data;
};

export const updateCompanyPlan = async <T>(id: number | string, payload: any): Promise<T> => {
  const response = await http.put<T>(`/company/plans/${id}`, payload);
  return response.data;
};

export const deleteCompanyPlan = async <T>(id: number | string): Promise<T> => {
  const response = await http.delete<T>(`/company/plans/${id}`);
  return response.data;
};
