import { http } from "@/services/http.config";
import { useAppContextStore } from "@/stores/appContext";

export const getCompanyUsers = async <T>(params: any = {}): Promise<T> => {
  const store = useAppContextStore();
  const companyCode = store.user?.company?.code;

  if (!companyCode) {
    throw new Error("Company code not found in session.");
  }

  const response = await http.get<T>(`/api/company/${companyCode}/users`, { params });
  return response.data;
};

export const getCompanyUserDetail = async <T>(id: number | string): Promise<T> => {
  const store = useAppContextStore();
  const companyCode = store.user?.company?.code;

  if (!companyCode) {
    throw new Error("Company code not found in session.");
  }

  const response = await http.get<T>(`/api/company/${companyCode}/users/${id}`);
  return response.data;
};

export const createCompanyUser = async <T>(payload: any): Promise<T> => {
  const store = useAppContextStore();
  const companyCode = store.user?.company?.code;

  if (!companyCode) {
    throw new Error("Company code not found in session.");
  }

  const response = await http.post<T>(`/api/company/${companyCode}/users`, payload);
  return response.data;
};

export const updateCompanyUser = async <T>(id: number | string, payload: any): Promise<T> => {
  const store = useAppContextStore();
  const companyCode = store.user?.company?.code;

  if (!companyCode) {
    throw new Error("Company code not found in session.");
  }

  const response = await http.put<T>(`/api/company/${companyCode}/users/${id}`, payload);
  return response.data;
};

export const deleteCompanyUser = async <T>(id: number | string): Promise<T> => {
  const store = useAppContextStore();
  const companyCode = store.user?.company?.code;

  if (!companyCode) {
    throw new Error("Company code not found in session.");
  }

  const response = await http.delete<T>(`/api/company/${companyCode}/users/${id}`);
  return response.data;
};
