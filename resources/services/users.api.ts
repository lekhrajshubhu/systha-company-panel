import { http } from "@/services/http.config";

export const getCompanyUsers = async <T>(params: any = {}): Promise<T> => {
  const response = await http.get<T>(`/company/users`, { params });
  return response.data;
};

export const getCompanyUserDetail = async <T>(id: number | string): Promise<T> => {
  const response = await http.get<T>(`/company/users/${id}/detail`);
  return response.data;
};

export const createCompanyUser = async <T>(payload: any): Promise<T> => {
  const response = await http.post<T>(`/company/users`, payload);
  return response.data;
};

export const updateCompanyUser = async <T>(id: number | string, payload: any): Promise<T> => {
  const response = await http.put<T>(`/company/users/${id}`, payload);
  return response.data;
};

export const deleteCompanyUser = async <T>(id: number | string): Promise<T> => {
  const response = await http.delete<T>(`/company/users/${id}`);
  return response.data;
};
