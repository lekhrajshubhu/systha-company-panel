export const AUTH_TOKEN_KEY = "auth_token";
const LEGACY_COMPANYPANEL_TOKEN_KEY = "companypanel_access_token";

export const getAuthToken = (): string | null => {
  const token = localStorage.getItem(AUTH_TOKEN_KEY);
  if (token) return token;

  // Backward compat: migrate existing sessions.
  const legacy = localStorage.getItem(LEGACY_COMPANYPANEL_TOKEN_KEY);
  if (!legacy) return null;

  localStorage.setItem(AUTH_TOKEN_KEY, legacy);
  return legacy;
};

export const setAuthToken = (token: string): void => {
  localStorage.setItem(AUTH_TOKEN_KEY, token);
};

export const clearAuthToken = (): void => {
  localStorage.removeItem(AUTH_TOKEN_KEY);
  localStorage.removeItem(LEGACY_COMPANYPANEL_TOKEN_KEY);
};

