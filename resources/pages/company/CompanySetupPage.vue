<template>
  <v-container class="py-6 px-4 px-md-8" fluid>
    <v-row>
      <v-col cols="12" md="8" lg="6" offset-lg="3" offset-md="2">
        <v-card class="pa-6" variant="flat">
          <div class="d-flex justify-space-between align-center mb-6">
            <div>
              <div class="text-h6 font-weight-bold">Company Setup</div>
              <div class="text-body-2 text-medium-emphasis">Complete these steps to access to dashboard.</div>
            </div>
            <div class="d-flex justify-space-between align-center mb-2">
              <div class="text-caption text-medium-emphasis">
                Step {{ stepIndex }} of 3
              </div>
            </div>
          </div>
          
          <v-alert v-if="errorMessage" type="error" variant="tonal" density="compact" class="mb-4">
            {{ errorMessage }}
          </v-alert>

          <div class="mb-5">
            <v-progress-linear :model-value="progressValue" height="8" rounded color="primary" />
          </div>

          <Transition name="slide-right" mode="out-in">
            <div>
              <app-card>
                <component :is="currentStepComponent" :key="currentStepKey" ref="stepRef" :current_step="stepIndex"
                  :successMessage="stepSuccessMessage" />
              </app-card>
            </div>
          </Transition>

          <v-container fluid>
            <div class="d-flex justify-space-between align-center mt-4">
              <v-btn variant="outlined" color="primary" :disabled="stepIndex === 1 || savingStep" @click="prevStep" prepend-icon="mdi-arrow-left">
                Previous
              </v-btn>
  
              <div class="d-flex ga-3">
                <v-btn v-if="stepIndex < 4" variant="flat" color="primary" :loading="savingStep" @click="saveAndNext" append-icon="mdi-arrow-right">
                  Next
                </v-btn>
                <v-btn v-else color="primary" :disabled="!allComplete" :loading="savingStep" variant="flat" @click="saveAndNext" append-icon="mdi-check">
                  Complete & Go to Dashboard
                </v-btn>
              </div>
            </div>
          </v-container>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup lang="ts">
import { computed, onMounted, reactive, ref } from "vue";
import { useRouter } from "vue-router";
import { getCompanySetupState, setCompanySetupCompleted } from "@/utils/companySetup";
import {
  setupChangePassword,
  setupMembershipPlans,
  setupPaymentCredential,
} from "@/services/company-setup.api";
import PasswordSetupStep from '@/components/company/setup/PasswordSetupStep.vue';
import PaymentCredentialsSetupStep from '@/components/company/setup/PaymentCredentialsSetupStep.vue';
import MembershipPlansSetupStep from '@/components/company/setup/MembershipPlansSetupStep.vue';
import AppCard from "@/components/AppCard.vue";

const router = useRouter();
const errorMessage = ref("");
const savingStep = ref(false);
const stepSuccessMessage = ref("");
const stepRef = ref();

const state = reactive(getCompanySetupState());
const steps = state.steps;

const allComplete = computed(() => steps.password && steps.payment && steps.plans);

const stepIndex = ref(1);
const progressValue = computed(() => (stepIndex.value / 3) * 100);

// Component mapping for dynamic rendering
const stepComponents = {
  1: PasswordSetupStep,
  2: PaymentCredentialsSetupStep,
  3: MembershipPlansSetupStep,
};

const currentStepComponent = computed(() => stepComponents[stepIndex.value as keyof typeof stepComponents]);
const currentStepKey = computed(() => `step-${stepIndex.value}`);

// Form data is now managed in individual step components

const prevStep = () => {
  stepSuccessMessage.value = "";
  stepIndex.value = Math.max(1, stepIndex.value - 1);
};

const saveAndNext = async () => {
  if (savingStep.value) return;
  errorMessage.value = "";
  stepSuccessMessage.value = "";
  savingStep.value = true;

  try {
    if (stepIndex.value === 1) {
      const passwordComponent = stepRef.value as any;
      const passwordForm = passwordComponent?.passwordForm || {};

      if (!passwordForm.password || !passwordForm.password_confirmation) {
        throw new Error("Please enter and confirm your new password.");
      }
      if (passwordForm.password !== passwordForm.password_confirmation) {
        throw new Error("Password confirmation does not match.");
      }
      await setupChangePassword({
        password: passwordForm.password,
        password_confirmation: passwordForm.password_confirmation,
        current_step: stepIndex.value + 1,
      });
      steps.password = true;
      stepSuccessMessage.value = "Password updated.";
      stepIndex.value = 2;
      return;
    }

    if (stepIndex.value === 2) {
      const paymentComponent = stepRef.value as any;
      const paymentForm = paymentComponent?.paymentForm || {};

      if (!paymentForm.name?.trim()) throw new Error("Payment credential name is required.");
      if (!paymentForm.credentials?.publishable_key?.trim()) throw new Error("Publishable key is required.");
      if (!paymentForm.credentials?.secret_key?.trim()) throw new Error("Secret key is required.");

      await setupPaymentCredential({
        name: paymentForm.name.trim(),
        mode: paymentForm.mode === "live" ? "live" : "test",
        credentials: {
          publishable_key: paymentForm.credentials.publishable_key.trim(),
          secret_key: paymentForm.credentials.secret_key.trim(),
          webhook_secret: paymentForm.credentials.webhook_secret?.trim() || null,
        },
        is_active: true,
        current_step: stepIndex.value + 1,
      });
      steps.payment = true;
      stepIndex.value = 3;
      return;
    }

    if (stepIndex.value === 3) {
      const planComponent = stepRef.value as any;
      const planForm = planComponent?.planForm || {};

      if (!planForm.name?.trim()) throw new Error("Plan name is required.");
      const monthly = Number(planForm.monthly_amount);
      const yearly = Number(planForm.yearly_amount);
      if (Number.isNaN(monthly) || monthly < 0) throw new Error("Monthly price must be a valid number.");
      if (Number.isNaN(yearly) || yearly < 0) throw new Error("Yearly price must be a valid number.");

      await setupMembershipPlans({
        current_step: stepIndex.value + 1,
        name: planForm.name.trim(),
        description: planForm.features?.trim() || null,
        highlight: planForm.description?.trim() || null,
        status: planForm.status,
        plan_prices: [
          { recurring_type: "month", amount: monthly || 0, currency: "USD" },
          { recurring_type: "year", amount: yearly || 0, currency: "USD" },
        ],
      });
      steps.plans = true;
      setCompanySetupCompleted(true);
      await router.push({ name: "company.dashboard" });
      return;
    }
  } catch (e: any) {
    errorMessage.value = e?.response?.data?.message || (e instanceof Error ? e.message : "Unable to save this step.");
  } finally {
    savingStep.value = false;
  }
};

onMounted(() => {
  // Get current step from active_context profile_setup
  try {
    const activeContextRaw = localStorage.getItem('auth_active_context')
    if (activeContextRaw) {
      try {
        const activeContext = JSON.parse(activeContextRaw)
        const currentStep = activeContext?.profile_setup?.current_step
        if (currentStep !== undefined && currentStep !== null) {
          // support numeric (1..3) or step-name values
          if (typeof currentStep === 'number') {
            stepIndex.value = Math.min(Math.max(1, Number(currentStep)), 3)
          } else if (typeof currentStep === 'string') {
            const mapping: Record<string, number> = {
              password: 1,
              payment: 2,
              plans: 3,
              '1': 1,
              '2': 2,
              '3': 3,
            }
            const mapped = mapping[currentStep.toLowerCase()] ?? mapping[String(currentStep)]
            if (mapped) stepIndex.value = mapped
          }
        }
      } catch (e) {
        // ignore parse errors
      }
    }
  } catch (e) {
    // ignore
  }

  const refreshed = getCompanySetupState();
  state.completed = refreshed.completed;
  state.steps.password = refreshed.steps.password;
  state.steps.payment = refreshed.steps.payment;
  state.steps.plans = refreshed.steps.plans;

  steps.password = refreshed.steps.password;
  steps.payment = refreshed.steps.payment;
  steps.plans = refreshed.steps.plans;
});
</script>

<style scoped></style>
