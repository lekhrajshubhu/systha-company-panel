import { http } from "@/services/http.config";
import { useAppContextStore } from "@/stores/appContext";

export const getCompanyVendors = async <T>(params: any = {}): Promise<T> => {
  const store = useAppContextStore();
  const companyCode = store.user?.company?.code;

  if (!companyCode) {
    throw new Error("Company code not found in session.");
  }

  const response = await http.get<T>(`/api/company/${companyCode}/vendors`, { params });
  return response.data;
};

export const getCompanyVendorDetail = async <T>(id: number | string): Promise<T> => {
  const store = useAppContextStore();
  const companyCode = store.user?.company?.code;

  if (!companyCode) {
    throw new Error("Company code not found in session.");
  }

  const response = await http.get<T>(`/api/company/${companyCode}/vendors/${id}`);
  return response.data;
};
