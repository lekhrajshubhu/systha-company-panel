import { http } from "@/services/http.config";
import { useAppContextStore } from "@/stores/appContext";

export type CompanyPagePayload = {
  title: string;
  subtitle?: string | null;
  slug: string;
  content?: string | null;
  status: "published" | "draft";
};

export type CompanyPageResponse = {
  success: boolean;
  message: string;
  data: {
    id: number;
    company_id: number;
    title: string;
    subtitle: string | null;
    slug: string;
    content: string | null;
    status: "published" | "draft";
    is_deleted: boolean;
    deleted_at: string | null;
    created_at: string;
    updated_at: string;
  };
};

export type CompanyPageListResponse = {
  success: boolean;
  message: string;
  data: CompanyPageResponse["data"][];
};

const getCompanyCode = (): string => {
  const store = useAppContextStore();
  const companyCode = store.user?.company?.code;

  if (!companyCode) {
    throw new Error("Company code not found in session.");
  }

  return companyCode;
};

export const createCompanyPage = async (payload: CompanyPagePayload): Promise<CompanyPageResponse> => {
  const companyCode = getCompanyCode();
  const response = await http.post<CompanyPageResponse>(`/api/company/${companyCode}/pages`, payload);
  return response.data;
};

export const getCompanyPages = async (): Promise<CompanyPageListResponse> => {
  const companyCode = getCompanyCode();
  const response = await http.get<CompanyPageListResponse>(`/api/company/${companyCode}/pages`);
  return response.data;
};

export const updateCompanyPage = async (id: number, payload: CompanyPagePayload): Promise<CompanyPageResponse> => {
  const companyCode = getCompanyCode();
  const response = await http.put<CompanyPageResponse>(`/api/company/${companyCode}/pages/${id}`, payload);
  return response.data;
};
