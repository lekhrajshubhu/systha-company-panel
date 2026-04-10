<template>
    <v-dialog
        v-if="modal.component"
        v-model="modal.isOpen"
        :fullscreen="isFullscreen"
        :transition="modal.options.transition"
        :persistent="modal.options.persistent"
        :scrollable="modal.options.scrollable"
        :max-width="modal.options.maxWidth ?? undefined"
        content-class="global-modal-content"
        @after-leave="modal.clear"
    >
        <v-card
            :class="[
                'global-modal-card',
                { 'global-modal-card--fullscreen': isFullscreen },
            ]"
        >
            <v-card-title
                v-if="modal.options.showHeader"
                class="global-modal-title"
            >
                <span>{{ modal.options.title }}</span>
                <v-btn
                    icon
                    variant="text"
                    aria-label="Close modal"
                    @click="modal.close()"
                >
                    <v-icon>mdi-close</v-icon>
                </v-btn>
            </v-card-title>
            <v-divider></v-divider>
            <component :is="modal.component" v-bind="modal.props" />
        </v-card>
    </v-dialog>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { useModalStore } from "@/stores/modal";

const modal = useModalStore();
const isFullscreen = computed(() => Boolean(modal.options.fullscreen));
</script>

<style scoped lang="scss">
.global-modal-card {
    border-radius: 16px;
    .v-card-title {
        // font-size: 0.8rem;
        padding: 0 0.5rem 0 1.5rem !important;
        text-transform: uppercase;
        font-weight: 700;
        color: #717171;
    }
}

.global-modal-card--fullscreen {
    min-height: 100dvh;
    border-radius: 0;
}

.global-modal-card--fullscreen :deep(.v-card) {
    border-radius: 0;
}

.global-modal-card--fullscreen :deep(.v-card-text) {
    padding-bottom: calc(env(safe-area-inset-bottom, 0px) + 16px);
}

.global-modal-title {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.search-input {
    background: #e9edf2 !important;
    border-radius: var(--radius-lg) !important;
}

:global(.search-input .v-field__outline) {
    --v-field-border-width: 0px;
    --v-field-border-opacity: 0;
}
</style>

<style>
.global-modal-content {
    padding: 0 !important;
}

.v-overlay__content {
    pointer-events: auto;
}
.v-dialog > .v-overlay__content > .v-card,
.v-dialog > .v-overlay__content > .v-sheet,
.v-dialog > .v-overlay__content > form > .v-card,
.v-dialog > .v-overlay__content > form > .v-sheet {
    box-shadow: none !important;
    border: 0 !important;
    border-radius: 3px !important;
}
</style>
