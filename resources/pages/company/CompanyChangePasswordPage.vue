<template>
  <v-container class="py-6 px-4 px-md-8" fluid>
    <v-row justify="center">
      <v-col cols="12" md="8" lg="6">
        <v-card class="pa-6" variant="flat">
          <div class="text-h6 font-weight-bold mb-2">Change Password</div>
          <div class="text-body-2 text-medium-emphasis mb-6">
            Update your password before continuing.
          </div>

          <v-form @submit.prevent="onSubmit">
            <v-text-field
              v-model="form.current_password"
              label="Current Password"
              :type="showCurrent ? 'text' : 'password'"
              variant="outlined"
              density="comfortable"
              :append-inner-icon="showCurrent ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
              @click:append-inner="showCurrent = !showCurrent"
              autocomplete="current-password"
              class="mb-2"
            />

            <v-text-field
              v-model="form.password"
              label="New Password"
              :type="showNew ? 'text' : 'password'"
              variant="outlined"
              density="comfortable"
              :append-inner-icon="showNew ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
              @click:append-inner="showNew = !showNew"
              autocomplete="new-password"
              class="mb-2"
            />

            <v-text-field
              v-model="form.password_confirmation"
              label="Confirm New Password"
              :type="showConfirm ? 'text' : 'password'"
              variant="outlined"
              density="comfortable"
              :append-inner-icon="showConfirm ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
              @click:append-inner="showConfirm = !showConfirm"
              autocomplete="new-password"
              class="mb-4"
            />

            <v-alert v-if="errorMessage" type="error" variant="tonal" density="compact" class="mb-3">
              {{ errorMessage }}
            </v-alert>

            <v-alert v-if="successMessage" type="success" variant="tonal" density="compact" class="mb-3">
              {{ successMessage }}
            </v-alert>

            <div class="d-flex ga-3">
              <v-btn color="primary" type="submit" :loading="submitting" :disabled="submitting">
                Save Password
              </v-btn>
              <v-btn variant="text" color="primary" :disabled="submitting" @click="goBack">
                Back to Setup
              </v-btn>
            </div>
          </v-form>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup lang="ts">
import { reactive, ref } from "vue";
import { useRouter } from "vue-router";
import { changeCompanyPassword } from "@/services/security.api";
import { setCompanySetupStep } from "@/utils/companySetup";

const router = useRouter();

const form = reactive({
  current_password: "",
  password: "",
  password_confirmation: "",
});

const showCurrent = ref(false);
const showNew = ref(false);
const showConfirm = ref(false);
const submitting = ref(false);
const errorMessage = ref("");
const successMessage = ref("");

const goBack = async () => {
  await router.push({ name: "company.setup" });
};

const onSubmit = async () => {
  if (submitting.value) return;
  errorMessage.value = "";
  successMessage.value = "";

  if (!form.current_password || !form.password || !form.password_confirmation) {
    errorMessage.value = "Please fill in all fields.";
    return;
  }

  if (form.password !== form.password_confirmation) {
    errorMessage.value = "Password confirmation does not match.";
    return;
  }

  submitting.value = true;
  try {
    await changeCompanyPassword({
      current_password: form.current_password,
      password: form.password,
      password_confirmation: form.password_confirmation,
    });
    setCompanySetupStep("password", true);
    successMessage.value = "Password updated.";
    form.current_password = "";
    form.password = "";
    form.password_confirmation = "";
  } catch (error: any) {
    errorMessage.value =
      error?.response?.data?.message ||
      (error instanceof Error ? error.message : "Unable to update password.");
  } finally {
    submitting.value = false;
  }
};
</script>

