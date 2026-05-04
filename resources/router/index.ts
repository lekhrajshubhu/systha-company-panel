import { createRouter, createWebHistory } from "vue-router";
import { companyRoutes } from "@/router/route-companies";
import { TENANTPANEL_ACCOUNT_KEY } from "@/services/companyAuth";
import { getAuthToken } from "@/services/authToken.storage";
import { useAppContextStore } from "@/stores/appContext";
import NotFoundPage from "@/pages/shared/NotFoundPage.vue";
import { 
  AUTH_ACTIVE_CONTEXT_KEY, 
  AUTH_CONTEXTS_KEY, 
  AUTH_PENDING_CONTEXT_SELECTION_KEY,
  getCompanyProfile,
  saveActiveContext,
  type CompanyProfileData 
} from "@/services/auth.api";

// Module-level cached promise for deduplication
let profileSyncPromise: Promise<void> | null = null;

const ensureCompanyProfileSynced = async (): Promise<void> => {
  // Return existing promise if already in progress
  if (profileSyncPromise) {
    return profileSyncPromise;
  }

  profileSyncPromise = (async () => {
    try {
      const profileData: CompanyProfileData = await getCompanyProfile();
      
      // Update contexts if they exist
      if (profileData.contexts) {
        localStorage.setItem(AUTH_CONTEXTS_KEY, JSON.stringify(profileData.contexts));
        window.dispatchEvent(new Event('auth-changed'));
      }
      
      // Update active context if it exists
      if (profileData.active_context) {
        saveActiveContext(profileData.active_context);
      }
      
      // Update TENANTPANEL_ACCOUNT_KEY by merging existing stored account + response
      const existingAccountStr = localStorage.getItem(TENANTPANEL_ACCOUNT_KEY);
      let existingAccount: any = {};
      
      if (existingAccountStr) {
        try {
          existingAccount = JSON.parse(existingAccountStr);
        } catch {
          // Ignore parsing errors
        }
      }
      
      const updatedAccount = {
        id: existingAccount.id || profileData.client?.id || '',
        name: existingAccount.name || profileData.client?.first_name || profileData.client?.email || 'User',
        email: existingAccount.email || profileData.client?.email || '',
        role:
          (() => {
            const rawRole: any = (profileData.active_context as any)?.roles?.[0];
            return typeof rawRole === 'string' ? rawRole : rawRole?.label ?? rawRole?.value;
          })() || existingAccount.role,
        company: profileData.active_context?.company || existingAccount.company,
      };
      
      localStorage.setItem(TENANTPANEL_ACCOUNT_KEY, JSON.stringify(updatedAccount));
      
      // Fire auth-changed event after updating account
      window.dispatchEvent(new Event('auth-changed'));
      
    } catch (error: any) {
      // Handle auth failure (401/419) - clear session and redirect to login
      if (error?.response?.status === 401 || error?.response?.status === 419) {
        // Clear all auth-related storage
        localStorage.removeItem(AUTH_ACTIVE_CONTEXT_KEY);
        localStorage.removeItem(AUTH_CONTEXTS_KEY);
        localStorage.removeItem(AUTH_PENDING_CONTEXT_SELECTION_KEY);
        localStorage.removeItem(TENANTPANEL_ACCOUNT_KEY);
        
        // Clear auth token
        const { clearAuthToken } = await import('@/services/authToken.storage');
        clearAuthToken();
        
        // Redirect to login
        router.replace({ name: "company.login" });
        throw error; // Re-throw to stop navigation
      }

      // If a context hasn't been selected yet, some APIs may forbid profile access.
      // In that case, allow navigation to proceed so user can pick a context.
      if (error?.response?.status === 403) {
        const pending = localStorage.getItem(AUTH_PENDING_CONTEXT_SELECTION_KEY) === 'true'
        if (pending) return
      }
      
      // For other errors, log but don't break navigation
      console.error('Failed to sync company profile:', error);
    } finally {
      // Clear the cached promise after completion
      profileSyncPromise = null;
    }
  })();
  
  return profileSyncPromise;
};

export const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/",
      children: companyRoutes,
    },
    { path: "/:pathMatch(.*)*", name: "not-found", component: NotFoundPage },
  ],
});

router.beforeEach(async (to) => {
  const store = useAppContextStore();
  const token = getAuthToken();
  const isLoginRoute = to.name === "company.login";
  const isContextSelectRoute = to.name === "company.context-select";
  const requiresAuth = to.matched.some((record) => record.meta.requiresAuth);
  const pendingContextSelection = localStorage.getItem(AUTH_PENDING_CONTEXT_SELECTION_KEY) === 'true'

  // Auth Guard
  if (requiresAuth && !token) {
    return { name: "company.login" };
  }

  // Auto-sync company profile on reload for authenticated users (except login route)
  if (token && !isLoginRoute && !pendingContextSelection) {
    try {
      await ensureCompanyProfileSynced();
    } catch {
      // If profile sync fails due to auth error, the error handler in ensureCompanyProfileSynced
      // will handle the redirect to login, so we just need to stop current navigation
      return false;
    }
  }

  const hasActiveContext = (() => {
    const storedActiveContext = localStorage.getItem(AUTH_ACTIVE_CONTEXT_KEY);
    if (storedActiveContext) {
      try {
        const parsed = JSON.parse(storedActiveContext);
        if (parsed && typeof parsed === "object") return true;
      } catch {
        // ignore
      }
    }

    const storedUser = localStorage.getItem(TENANTPANEL_ACCOUNT_KEY);
    if (!storedUser) return false;
    try {
      const parsed = JSON.parse(storedUser);
      return Boolean(parsed?.company?.code);
    } catch {
      return false;
    }
  })();

  // Prevent logged in users from visiting login page
  if (isLoginRoute && token) {
    return hasActiveContext ? { name: "company.dashboard" } : { name: "company.context-select" };
  }

  // Enforce context selection on protected routes (except context selector itself).
  if (requiresAuth && token && !hasActiveContext && !isContextSelectRoute && !to.meta.skipContextCheck) {
    const storedContexts = localStorage.getItem(AUTH_CONTEXTS_KEY);
    if (!storedContexts) {
      return { name: "company.login" };
    }
    try {
      const parsed = JSON.parse(storedContexts);
      if (!Array.isArray(parsed) || parsed.length === 0) {
        return { name: "company.login" };
      }
    } catch {
      return { name: "company.login" };
    }
    return { name: "company.context-select" };
  }

  // Context Synchronization
  const context = to.meta.context as "company" | undefined;

  if (context) {
    const storedUser = localStorage.getItem(TENANTPANEL_ACCOUNT_KEY);
    let userData: any = { id: "", name: "Guest User", email: "" };

    if (storedUser) {
      try {
        const parsed = JSON.parse(storedUser);
        userData = {
          id: parsed.id || "",
          name: parsed.name || "Guest User",
          email: parsed.email || "",
          role: parsed.role,
          company: parsed.company,
        };
      } catch (e) {}
    }

    const menu = (to.meta.menu as any[]) || [];
    store.setContext("company", userData, menu);
  }

  return true;
});
