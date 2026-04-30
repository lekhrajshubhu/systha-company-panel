import { clearAuthToken, getAuthToken, setAuthToken } from '@/services/authToken.storage'

export const TENANTPANEL_ACCESS_TOKEN_KEY = 'companypanel_access_token'
export const TENANTPANEL_ACCOUNT_KEY = 'companypanel_account'
const REFRESH_BUFFER_MS = 600_000

export type CompanyAccount = {
  id: number | string
  name: string
  email: string
}

export const getAccessToken = (): string | null => {
  return getAuthToken()
}

export const setAccessToken = (token: string): void => {
  setAuthToken(token)
}

export const setAuthSession = (token: string, account: CompanyAccount): void => {
  setAccessToken(token)
  localStorage.setItem(TENANTPANEL_ACCOUNT_KEY, JSON.stringify(account))
}

export const clearAuthSession = (): void => {
  clearAuthToken()
  localStorage.removeItem(TENANTPANEL_ACCOUNT_KEY)
}

let refreshTimer: number | null = null
let refreshPromise: Promise<string | null> | null = null

const decodeBase64Url = (value: string): string | null => {
  try {
    const normalized = value.replace(/-/g, '+').replace(/_/g, '/')
    const padded = normalized.padEnd(Math.ceil(normalized.length / 4) * 4, '=')
    return atob(padded)
  } catch {
    return null
  }
}

export const getTokenExpiry = (token: string): number | null => {
  const parts = token.split('.')
  if (parts.length < 2) return null
  const payload = decodeBase64Url(parts[1])
  if (!payload) return null
  try {
    const data = JSON.parse(payload) as { exp?: number }
    return typeof data.exp === 'number' ? data.exp : null
  } catch {
    return null
  }
}

export const stopTokenAutoRefresh = (): void => {
  if (refreshTimer !== null) {
    window.clearTimeout(refreshTimer)
    refreshTimer = null
  }
}

export const startTokenAutoRefresh = (): void => {
  stopTokenAutoRefresh()
  const token = getAccessToken()
  if (!token) return

  const exp = getTokenExpiry(token)
  if (!exp) return

  const delay = exp * 1000 - Date.now() - REFRESH_BUFFER_MS
  const scheduleDelay = delay <= 0 ? 0 : delay

  refreshTimer = window.setTimeout(async () => {
    let refreshed: string | null = null

    try {
      refreshed = await refreshTokenOnce()
      if (refreshed) {
        setAccessToken(refreshed)
      }
    } catch {
      refreshed = null
    }

    if (refreshed) {
      startTokenAutoRefresh()
      return
    }

    // Refresh failed or returned no token; avoid a tight retry loop.
    clearAuthSession()
    stopTokenAutoRefresh()
    redirectToLogin()
  }, scheduleDelay)
}

const redirectToLogin = async (): Promise<void> => {
  const baseUrl = import.meta.env.BASE_URL.endsWith('/')
    ? import.meta.env.BASE_URL
    : `${import.meta.env.BASE_URL}/`

  window.location.href = `${baseUrl}login`
}

const refreshTokenOnce = async (): Promise<string | null> => {
  if (refreshPromise) return refreshPromise
  const { refreshCompanyToken } = await import('./auth.api')
  refreshPromise = refreshCompanyToken()
    .then((payload) => payload.token ?? null)
    .finally(() => {
      refreshPromise = null
    })
  return refreshPromise
}
