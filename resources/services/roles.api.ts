import { http } from "@/services/http.config";

export type LookupOption = {
  value: number | string;
  label: string;
};

export const lookupCompanyRoles = async (): Promise<LookupOption[]> => {
  const response = await http.get(`/company/role-lookups`);
  const payload = (response as any)?.data ?? response;
  return (payload as any)?.data ?? payload;
};
