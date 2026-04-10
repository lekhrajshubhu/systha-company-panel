<template>
    <v-card-text class="" elevation="0">
        <v-form ref="formRef" v-model="isValid" @submit.prevent="onSubmit">
            <div class="pt-6 px-6">
                <v-row dense>
                    <v-col cols="12">
                        <v-text-field
                            v-model="form.password"
                            label="Password"
                            density="comfortable"
                            :type="showPassword ? 'text' : 'password'"
                            variant="outlined"
                            :append-inner-icon="
                                showPassword
                                    ? 'mdi-eye-off-outline'
                                    : 'mdi-eye-outline'
                            "
                            @click:append-inner="showPassword = !showPassword"
                            :rules="[rules.required]"
                        />
                    </v-col>

                    <v-col cols="12">
                        <v-text-field
                            v-model="form.password_confirmation"
                            label="Password Confirmation"
                            density="comfortable"
                            :type="showConfirm ? 'text' : 'password'"
                            variant="outlined"
                            :append-inner-icon="
                                showConfirm
                                    ? 'mdi-eye-off-outline'
                                    : 'mdi-eye-outline'
                            "
                            @click:append-inner="showConfirm = !showConfirm"
                            :rules="[rules.required, rules.matchesPassword]"
                        />
                    </v-col>
                </v-row>
            </div>
        </v-form>
    </v-card-text>
    <v-divider></v-divider>
    <v-card-actions>
        <div class="d-flex justify-space-around px-4 w-100">
            <v-btn color="primary" class="px-4" type="submit" variant="flat">
                <v-icon>mdi-content-save</v-icon> &nbsp; Update
            </v-btn>
        </div>
    </v-card-actions>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { useModalStore } from "@/stores/modal";

const emit = defineEmits<{
    (e: "submit", payload: Record<string, unknown>): void;
}>();

const modal = useModalStore();
const formRef = ref();
const isValid = ref(false);

const form = ref({
    password: "",
    password_confirmation: "",
});

const showPassword = ref(false);
const showConfirm = ref(false);

const rules = {
    required: (value: string) => (!!value ? true : "Required"),
    matchesPassword: (value: string) =>
        value === form.value.password || "Passwords do not match",
};

const close = () => {
    modal.close();
};

const onSubmit = async () => {
    const formEl = formRef.value;
    const result = await formEl?.validate?.();

    if (result?.valid === false) {
        return;
    }

    emit("submit", { ...form.value });
    close();
};
</script>

<style scoped></style>
