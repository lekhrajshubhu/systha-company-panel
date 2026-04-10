import { http } from "@/services/http.config";
import { useAppContextStore } from "@/stores/appContext";

export const getCompanySubscriptions = async <T>(params: any = {}): Promise<T> => {
  const store = useAppContextStore();
  const companyCode = store.user?.company?.code;

  if (!companyCode) {
    throw new Error("Company code not found in session.");
  }

  const response = await http.get<T>(`/api/company/${companyCode}/subscriptions`, { params });
  return response.data;
};

export const getCompanySubscriptionDetail = async <T>(id: number | string): Promise<T> => {
  const store = useAppContextStore();
  const companyCode = store.user?.company?.code;

  if (!companyCode) {
    throw new Error("Company code not found in session.");
  }

  const response = await http.get<T>(`/api/company/${companyCode}/subscriptions/${id}`);
  return response.data;
};
