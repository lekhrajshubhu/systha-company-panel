import { http } from "@/services/http.config";
import { useAppContextStore } from "@/stores/appContext";

export const createCompanyInquiry = async <T>(payload: any): Promise<T> => {
  const store = useAppContextStore();
  const companyCode = store.user?.company?.code;

  if (!companyCode) {
    throw new Error("Company code not found in session.");
  }

  const response = await http.post<T>(`/api/company/${companyCode}/inquiries`, payload);
  return response.data;
};
