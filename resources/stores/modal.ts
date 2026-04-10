import { defineStore } from "pinia";
import { markRaw, ref, shallowRef } from "vue";
import type { Component } from "vue";

interface ModalOptions {
	fullscreen: boolean;
	transition: string;
	persistent: boolean;
	scrollable: boolean;
	maxWidth: number | null;
	title: string;
	showHeader: boolean;
}

type ModalProps = Record<string, unknown>;
type ModalComponent = Component | null;

const DEFAULT_OPTIONS: ModalOptions = {
	fullscreen: false,
	transition: "dialog-bottom-transition",
	persistent: true,
	scrollable: true,
	maxWidth: null,
	title: "",
	showHeader: true,
};

export const useModalStore = defineStore("modal", () => {
	const isOpen = ref<boolean>(false);
	const component = shallowRef<ModalComponent>(null);
	const props = ref<ModalProps>({});
	const options = ref<ModalOptions>({ ...DEFAULT_OPTIONS });

	function open(
		modalComponent: Component | null,
		modalProps: ModalProps = {},
		modalOptions: Partial<ModalOptions> = {}
	): void {
		component.value = modalComponent ? markRaw(modalComponent) : null;
		props.value = modalProps;
		options.value = { ...DEFAULT_OPTIONS, ...modalOptions };
		isOpen.value = true;
	}

	function close(): void {
		isOpen.value = false;
	}

	function clear(): void {
		component.value = null;
		props.value = {};
		options.value = { ...DEFAULT_OPTIONS };
	}

	return {
		isOpen,
		component,
		props,
		options,
		open,
		close,
		clear,
	};
});
