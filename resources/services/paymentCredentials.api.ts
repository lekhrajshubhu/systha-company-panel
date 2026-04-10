import { http } from "@/services/http.config";
import { useAppContextStore } from "@/stores/appContext";

export const getCompanyPaymentCredentials = async <T>(params: any = {}): Promise<T> => {
  const store = useAppContextStore();
  const companyCode = store.user?.company?.code;

  if (!companyCode) {
    throw new Error("Company code not found in session.");
  }

  const response = await http.get<T>(`/api/company/${companyCode}/payment-credentials`, { params });
  return response.data;
};
