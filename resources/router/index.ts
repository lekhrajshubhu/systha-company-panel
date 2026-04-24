import { createRouter, createWebHistory } from "vue-router";
import { companyRoutes } from "@/router/route-companies";
import { TENANTPANEL_ACCOUNT_KEY } from "@/services/companyAuth";
import { getAuthToken } from "@/services/authToken.storage";
import { useAppContextStore } from "@/stores/appContext";
import NotFoundPage from "@/pages/shared/NotFoundPage.vue";

export const router = createRouter({
  history: createWebHistory("/company-panel/"),
  routes: [
    {
      path: "/",
      children: companyRoutes,
    },
    { path: "/:pathMatch(.*)*", name: "not-found", component: NotFoundPage },
  ],
});

router.beforeEach((to) => {
  const store = useAppContextStore();
  const token = getAuthToken();
  const isLoginRoute = to.name === "company.login";
  const requiresAuth = to.matched.some((record) => record.meta.requiresAuth);

  // Auth Guard
  if (requiresAuth && !token) {
    return { name: "company.login" };
  }

  // Prevent logged in users from visiting login page
  if (isLoginRoute && token) {
    return { name: "company.dashboard" };
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
