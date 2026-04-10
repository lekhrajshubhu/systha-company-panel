import axios, { AxiosError } from "axios";
import { getAccessToken as getCompanyToken } from "@/services/companyAuth";

const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || "/";

export const http = axios.create({
  baseURL: apiBaseUrl,
  headers: {
    Accept: "application/json",
  },
});

http.interceptors.request.use((config) => {
  const token = getCompanyToken();

  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }

  return config;
});

http.interceptors.response.use(
  (response) => response,
  (error: AxiosError<{ message?: string }>) => {
    return Promise.reject(error);
  },
);
