import { http } from "@/services/http.config";
import { useAppContextStore } from "@/stores/appContext";

export const getCompanyPackages = async <T>(params: any = {}): Promise<T> => {
  const store = useAppContextStore();
  const companyCode = store.user?.company?.code;
  if (!companyCode) throw new Error("Company code not found in session.");
  const response = await http.get<T>(`/api/company/${companyCode}/packages`, { params });
  return response.data;
};

export const getCompanyPackageDetail = async <T>(id: number | string): Promise<T> => {
  const store = useAppContextStore();
  const companyCode = store.user?.company?.code;
  if (!companyCode) throw new Error("Company code not found in session.");
  const response = await http.get<T>(`/api/company/${companyCode}/packages/${id}`);
  return response.data;
};

export const createCompanyPackage = async <T>(payload: any): Promise<T> => {
  const store = useAppContextStore();
  const companyCode = store.user?.company?.code;
  if (!companyCode) throw new Error("Company code not found in session.");
  const response = await http.post<T>(`/api/company/${companyCode}/packages`, payload);
  return response.data;
};

export const updateCompanyPackage = async <T>(id: number | string, payload: any): Promise<T> => {
  const store = useAppContextStore();
  const companyCode = store.user?.company?.code;
  if (!companyCode) throw new Error("Company code not found in session.");
  const response = await http.put<T>(`/api/company/${companyCode}/packages/${id}`, payload);
  return response.data;
};

export const deleteCompanyPackage = async <T>(id: number | string): Promise<T> => {
  const store = useAppContextStore();
  const companyCode = store.user?.company?.code;
  if (!companyCode) throw new Error("Company code not found in session.");
  const response = await http.delete<T>(`/api/company/${companyCode}/packages/${id}`);
  return response.data;
};
