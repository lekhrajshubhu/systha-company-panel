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

export const createCompanyPaymentCredential = async <T>(payload: any): Promise<T> => {
  const store = useAppContextStore();
  const companyCode = store.user?.company?.code;

  if (!companyCode) {
    throw new Error("Company code not found in session.");
  }

  const response = await http.post<T>(`/api/company/${companyCode}/payment-credentials`, payload);
  return response.data;
};

export const getCompanyPaymentCredentialDetail = async <T>(id: number | string): Promise<T> => {
  const store = useAppContextStore();
  const companyCode = store.user?.company?.code;

  if (!companyCode) {
    throw new Error("Company code not found in session.");
  }

  const response = await http.get<T>(`/api/company/${companyCode}/payment-credentials/${id}`);
  return response.data;
};

export const updateCompanyPaymentCredential = async <T>(id: number | string, payload: any): Promise<T> => {
  const store = useAppContextStore();
  const companyCode = store.user?.company?.code;

  if (!companyCode) {
    throw new Error("Company code not found in session.");
  }

  const response = await http.put<T>(`/api/company/${companyCode}/payment-credentials/${id}`, payload);
  return response.data;
};

export const deleteCompanyPaymentCredential = async <T>(id: number | string): Promise<T> => {
  const store = useAppContextStore();
  const companyCode = store.user?.company?.code;

  if (!companyCode) {
    throw new Error("Company code not found in session.");
  }

  const response = await http.delete<T>(`/api/company/${companyCode}/payment-credentials/${id}`);
  return response.data;
};
