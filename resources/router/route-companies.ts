// route-companies.ts
import type { RouteRecordRaw } from "vue-router";
import CompanyLoginPage from "@/pages/company/CompanyLoginPage.vue";
import CompanyDashboardPage from "@/pages/company/CompanyDashboardPage.vue";
import VendorDetailPage from "@/pages/company/VendorDetailPage.vue";

const AppLayout = () => import("../layouts/AppLayout.vue");

const StaffsPage = () => import("../pages/company/StaffsPage.vue");
const StaffFormPage = () => import("../pages/company/StaffFormPage.vue");
const VendorsPage = () => import("../pages/company/VendorsPage.vue");
const VendorFormPage = () => import("../pages/company/VendorFormPage.vue");
const PolicyPage = () => import("../pages/company/PolicyPage.vue");
const DefaultSettingsPage = () => import("../pages/company/DefaultSettingsPage.vue");
const PackageListPage = () => import("../pages/company/PackageListPage.vue");
const PackageFormPage = () => import("../pages/company/PackageFormPage.vue");
const SubscriptionsPage = () => import("../pages/company/SubscriptionsPage.vue");
const ReportsPage = () => import("../pages/company/ReportsPage.vue");
const CommissionReportPage = () => import("../pages/company/CommissionReportPage.vue");
const PaymentReportPage = () => import("../pages/company/PaymentReportPage.vue");
const PaymentCredentialsPage = () => import("../pages/company/PaymentCredentialsPage.vue");
const PaymentCredentialFormPage = () => import("../pages/company/PaymentCredentialFormPage.vue");

export const companyMenuGroups = [
    {
        group: "Overview",
        items: [
            { title: "Dashboard", routeName: "company.dashboard", icon: "mdi-chart-box-multiple-outline" },
            { title: "Users", routeName: "company.users", icon: "mdi-account-multiple-outline" },
            { title: "Vendors", routeName: "company.vendors", icon: "mdi-storefront-outline" },
        ],
    },
    {
        group: "Memberships",
        items: [
            { title: "Packages", routeName: "company.membership.package", icon: "mdi-gift-outline" },
            { title: "Subscriptions", routeName: "company.membership.subscriptions", icon: "mdi-autorenew" },
        ],
    },
    {
        group: "Reports",
        items: [
            { title: "Commission Report", routeName: "company.report.commission", icon: "mdi-file-chart-outline" },
            { title: "Payment Report", routeName: "company.report.payment", icon: "mdi-chart-multiple" },
        ],
    },
    {
        group: "Configuration",
        items: [
            { title: "Policies", routeName: "company.policies", icon: "mdi-file-document-multiple-outline" },
            { title: "Payment Gateways", routeName: "company.config.payment", icon: "mdi-wallet-bifold-outline" },
            { title: "General", routeName: "company.config.general", icon: "mdi-cog-outline" },
        ],
    },
];

export const companyRoutes: RouteRecordRaw[] = [
    {
        path: "login",
        name: "company.login",
        component: CompanyLoginPage,
        meta: { title: "Login" }
    },
    {
        path: "",
        redirect: "dashboard",
    },
    {
        path: "",
        component: AppLayout,
        meta: { context: "company", menu: companyMenuGroups, requiresAuth: true },
        children: [
            {
                path: "dashboard",
                name: "company.dashboard",
                component: CompanyDashboardPage,
                meta: { breadcrumb: ["Overview", "Dashboard"] }
            },
            {
                path: "users",
                name: "company.users",
                component: StaffsPage,
                meta: { breadcrumb: ["Overview", "Users"] }
            },
            {
                path: "users/create",
                name: "company.staff-create",
                component: StaffFormPage,
                meta: { breadcrumb: ["Overview", "Users", "Create"] }
            },
            {
                path: "users/:id/edit",
                name: "company.staff-edit",
                component: StaffFormPage,
                meta: { breadcrumb: ["Overview", "Users", "Edit"] }
            },
            {
                path: "vendors",
                name: "company.vendors",
                component: VendorsPage,
                meta: { breadcrumb: ["Overview", "Vendors"] }
            },
            {
                path: "vendors/:id",
                name: "company.vendor.detail",
                component: VendorDetailPage,
                meta: { breadcrumb: ["Overview", "Vendor","Detail"] }
            },
            {
                path: "vendors/create",
                name: "company.vendor-create",
                component: VendorFormPage,
                meta: { breadcrumb: ["Overview", "Vendors", "Create"] }
            },
            {
                path: "policies",
                name: "company.policies",
                component: PolicyPage,
                meta: { breadcrumb: ["Configuration", "Policies"] }
            },
            {
                path: "membership/packages",
                name: "company.membership.package",
                component: PackageListPage,
                meta: { breadcrumb: ["Memberships", "Package List"] }
            },
            {
                path: "membership/packages/create",
                name: "company.membership.package-create",
                component: PackageFormPage,
                meta: { breadcrumb: ["Memberships", "Package List", "Create"] }
            },
            {
                path: "membership/subscriptions",
                name: "company.membership.subscriptions",
                component: SubscriptionsPage,
                meta: { breadcrumb: ["Memberships", "Subscriptions"] }
            },
            {
                path: "reports/commission",
                name: "company.report.commission",
                component: CommissionReportPage,
                meta: { breadcrumb: ["Reports", "Commission Report"] }
            },
            {
                path: "reports/payment",
                name: "company.report.payment",
                component: PaymentReportPage,
                meta: { breadcrumb: ["Reports", "Payment Report"] }
            },
            {
                path: "config/payment",
                name: "company.config.payment",
                component: PaymentCredentialsPage,
                meta: { breadcrumb: ["Configuration", "Payment Gateways"] }
            },
            {
                path: "config/payment/create",
                name: "company.config.payment-create",
                component: PaymentCredentialFormPage,
                meta: { breadcrumb: ["Configuration", "Payment Gateways", "Create"] }
            },
            {
                path: "config/payment/:id/edit",
                name: "company.config.payment-edit",
                component: PaymentCredentialFormPage,
                meta: { breadcrumb: ["Configuration", "Payment Gateways", "Edit"] }
            },
            {
                path: "config/general",
                name: "company.config.general",
                component: DefaultSettingsPage,
                meta: { breadcrumb: ["Configuration", "General Settings"] }
            },
        ],
    },
];
