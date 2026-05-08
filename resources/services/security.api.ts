import { http } from "@/services/http.config";

export type ChangePasswordPayload = {
  current_password?: string;
  password: string;
  password_confirmation: string;
};

export const changeCompanyPassword = async <T>(payload: ChangePasswordPayload): Promise<T> => {
  const response = await http.post<T>(`/company/profile-setup/change-password`, payload);
  return response.data;
};
