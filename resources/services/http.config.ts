import axios, { AxiosError } from "axios";
import { getAuthToken } from "@/services/authToken.storage";

const normalizeBaseUrl = (value: string): string => {
  const trimmed = value.trim();
  if (trimmed === "" || trimmed === "/") return "/";
  const withoutTrailingSlashes = trimmed.replace(/\/+$/, "");
  return withoutTrailingSlashes === "" ? "/" : withoutTrailingSlashes;
};

const apiBaseUrl = normalizeBaseUrl(import.meta.env.VITE_API_BASE_URL || "/");
const defaultAppCode = "cb55a48b-da96-4be7-8188-0c6969606dc2";
const appCode = import.meta.env.VITE_APP_CODE || defaultAppCode;

export const http = axios.create({
  baseURL: apiBaseUrl,
  headers: {
    Accept: "application/json",
  },
});

http.interceptors.request.use((config) => {
  const token = getAuthToken();

  config.headers["App-Code"] = appCode;
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }

  return config;
});

http.interceptors.response.use(
  (response) => response.data,
  (error: AxiosError<{ message?: string }>) => {
    return Promise.reject(error);
  },
);
