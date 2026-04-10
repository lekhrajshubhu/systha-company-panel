import { http } from "./http.config";
import { useAppContextStore } from "../stores/appContext";

export const getCompanyServices = async <T>(params: any = {}): Promise<T> => {
  const store = useAppContextStore();
  const companyCode = store.user?.company?.code;

  if (!companyCode) {
    throw new Error("Company code not found in session.");
  }

  const response = await http.get<T>(`/api/company/${companyCode}/services`, { params });
  return response.data;
};

export const getCompanyServiceDetail = async <T>(serviceId: number): Promise<T> => {
  const store = useAppContextStore();
  const companyCode = store.user?.company?.code;

  if (!companyCode) {
    throw new Error("Company code not found in session.");
  }

  const response = await http.get<T>(`/api/company/${companyCode}/services/${serviceId}`);
  return response.data;
};
