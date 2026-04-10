<template>
    <v-card-text class="" elevation="0">
        <v-form ref="formRef" v-model="isValid" @submit.prevent="onSubmit">
            <div class="pt-6 px-6">
                <v-row dense>
                    <v-col cols="12">
                        <div class="avatar-uploader mb-4">
                            <div class="avatar-preview">
                                <v-img
                                    v-if="avatarPreview"
                                    :src="avatarPreview"
                                    cover
                                />
                                <div v-else class="avatar-placeholder">
                                    <v-icon size="28">mdi-account</v-icon>
                                   
                                </div>
                                <v-btn
                                    icon
                                    size="x-small"
                                    variant="tonal"
                                    class="avatar-edit"
                                    @click="triggerAvatarPicker"
                                >
                                    <v-icon size="16">mdi-pencil</v-icon>
                                </v-btn>
                            </div>
                            <input
                                ref="avatarInput"
                                type="file"
                                accept="image/*"
                                class="d-none"
                                @change="onAvatarSelected"
                            />
                        </div>
                    </v-col>
                    <v-col cols="12" md="12">
                        <v-text-field
                            v-model="form.first_name"
                            label="First Name"
                            density="comfortable"
                            variant="outlined"
                            :rules="[rules.required]"
                        />
                    </v-col>
                    <v-col cols="12" md="12">
                        <v-text-field
                            v-model="form.last_name"
                            label="Last Name"
                            density="comfortable"
                            variant="outlined"
                            :rules="[rules.required]"
                        />
                    </v-col>
                    <v-col cols="12" md="12">
                        <v-text-field
                            v-model="form.username"
                            label="Username"
                            density="comfortable"
                            variant="outlined"
                            :rules="[rules.required]"
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

const props = defineProps<{
    initialValues?: {
        first_name?: string;
        last_name?: string;
        username?: string;
        email?: string;
        avatar_url?: string;
    };
}>();

const emit = defineEmits<{
    (e: "submit", payload: Record<string, unknown>): void;
}>();

const modal = useModalStore();
const formRef = ref();
const isValid = ref(false);

const form = ref({
    avatar: null as File | null,
    first_name: props.initialValues?.first_name ?? "",
    last_name: props.initialValues?.last_name ?? "",
    username: props.initialValues?.username ?? "",
    email: props.initialValues?.email ?? "",
});

const rules = {
    required: (value: string) => (!!value ? true : "Required"),
};

const avatarInput = ref<HTMLInputElement | null>(null);
const avatarPreview = ref<string | null>(
    props.initialValues?.avatar_url ?? null
);

const triggerAvatarPicker = () => {
    avatarInput.value?.click();
};

const onAvatarSelected = (event: Event) => {
    const target = event.target as HTMLInputElement | null;
    const file = target?.files?.[0] ?? null;
    form.value.avatar = file;
    if (file) {
        avatarPreview.value = URL.createObjectURL(file);
    } else {
        avatarPreview.value = null;
    }
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

<style scoped>
.avatar-uploader {
    display: flex;
    align-items: flex-start;
    justify-content: center;
}

.avatar-preview {
    width: 96px;
    height: 96px;
    border-radius: 12px;
    background: #f3f4f6;
    position: relative;
    overflow: visible;
    display: flex;
    align-items: center;
    justify-content: center;
}

.avatar-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    color: rgba(15, 23, 42, 0.6);
}

.avatar-edit {
    position: absolute;
    top: -8px;
    right: -8px;
}
</style>
