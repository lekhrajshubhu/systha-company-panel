import { http } from "@/services/http.config";

export type CompanySetupChangePasswordPayload = {
  password: string;
  password_confirmation: string;
  current_step: number;
};

export type CompanySetupPaymentCredentialPayload = {
  name: string;
  mode: "live" | "test";
  credentials: {
    publishable_key: string;
    secret_key: string;
    webhook_secret?: string | null;
  };
  is_active: boolean;
  current_step: number;
};

export type CompanySetupMembershipPlanPayload = {
  name: string;
  description?: string | null;
  highlight?: string | null;
  status: "active" | "inactive";
  plan_prices: {
    recurring_type: "month" | "year";
    amount: number;
    currency: string;
  }[];
  current_step: number;
};

export const setupChangePassword = async <T>(
  payload: CompanySetupChangePasswordPayload,
): Promise<T> => {
  const response = await http.post<T>(`/company/profile-setup/change-password`, payload);
  return response.data;
};

export const setupPaymentCredential = async <T>(
  payload: CompanySetupPaymentCredentialPayload,
): Promise<T> => {
  const response = await http.post<T>(`/company/profile-setup/payment-credential`, payload);
  return response.data;
};

export const setupMembershipPlans = async <T>(
  payload: CompanySetupMembershipPlanPayload,
): Promise<T> => {
  const response = await http.post<T>(`/company/profile-setup/membership-plans`, payload);
  return response.data;
};
