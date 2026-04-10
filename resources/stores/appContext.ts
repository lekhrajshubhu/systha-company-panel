import { defineStore } from "pinia";
import { computed, ref } from "vue";
import { TENANTPANEL_ACCOUNT_KEY, clearAuthSession as clearCompanyAuth, stopTokenAutoRefresh } from "@/services/companyAuth";
import { logoutCompany } from "@/services/auth.api";
import { useRouter } from "vue-router";

export interface User {
  id: number | string;
  name: string;
  email: string;
  role?: string;
  company?: {
    name: string;
    code: string;
  };
}

export interface MenuItem {
  title: string;
  routeName: string;
  icon: string;
}

export interface MenuGroup {
  group: string;
  items: MenuItem[];
}

export const useAppContextStore = defineStore("appContext", () => {
  const router = useRouter();
  const user = ref<User | null>(null);
  const menuGroups = ref<MenuGroup[]>([]);
  const context = ref<"company" | null>(null);

  const userName = computed(() => {
    if (user.value?.company?.name) {
      return user.value.company.name;
    }
    return user.value?.name || "Guest User";
  });

  const userEmail = computed(() => user.value?.email || "");

  const userInitials = computed(() => {
    const parts = userName.value.split(" ").filter(Boolean);
    if (parts.length === 0) return "GU";
    if (parts.length === 1) return parts[0].slice(0, 2).toUpperCase();
    return (parts[0][0] + parts[1][0]).toUpperCase();
  });

  function setContext(newContext: "company", userData: User, menu: MenuGroup[]) {
    context.value = newContext;
    user.value = userData;
    menuGroups.value = menu;
  }

  function syncFromLocalStorage() {
    const storedUser = localStorage.getItem(TENANTPANEL_ACCOUNT_KEY);
    if (!storedUser) {
      return;
    }

    try {
      const parsed = JSON.parse(storedUser);
      user.value = {
        id: parsed.id || "",
        name: parsed.name || "Guest User",
        email: parsed.email || "",
        role: parsed.role,
        company: parsed.company,
      };
    } catch (e) {
      console.error("Failed to parse stored user", e);
    }
  }

  const clearLocalContext = async () => {
    stopTokenAutoRefresh();
    clearCompanyAuth();
    await router.push({ name: "company.login" });
    user.value = null;
    context.value = null;
    menuGroups.value = [];
  };

  const logout = async () => {
    try {
      await logoutCompany();
    } catch (error) {
      console.error("Failed to logout from server:", error);
    } finally {
      await clearLocalContext();
    }
  };

  return {
    user,
    menuGroups,
    context,
    userName,
    userEmail,
    userInitials,
    setContext,
    syncFromLocalStorage,
    logout,
  };
});
