<script setup>
import { computed } from "vue";
import Modal from "@/Components/Modal.vue";
import {
    CheckCircleIcon,
    ExclamationTriangleIcon,
    InformationCircleIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: "",
    },
    message: {
        type: String,
        default: "",
    },
    confirmText: {
        type: String,
        default: "Confirmar",
    },
    cancelText: {
        type: String,
        default: "Cancelar",
    },
    variant: {
        type: String,
        default: "primary",
        validator: (value) => ["primary", "warning", "danger"].includes(value),
    },
    processing: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["confirm", "cancel", "close"]);

const variantStyles = computed(() => {
    const styles = {
        primary: {
            icon: InformationCircleIcon,
            iconWrap: "border-blue-100 bg-blue-50 text-blue-600",
            confirm:
                "bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500",
        },
        warning: {
            icon: ExclamationTriangleIcon,
            iconWrap: "border-amber-100 bg-amber-50 text-amber-600",
            confirm:
                "bg-amber-500 text-white hover:bg-amber-600 focus:ring-amber-500",
        },
        danger: {
            icon: ExclamationTriangleIcon,
            iconWrap: "border-red-100 bg-red-50 text-red-600",
            confirm:
                "bg-red-600 text-white hover:bg-red-700 focus:ring-red-500",
        },
    };

    return styles[props.variant] || styles.primary;
});

const titleId = "confirmation-modal-title";
const messageId = "confirmation-modal-message";

const confirm = () => {
    if (props.processing) return;

    emit("confirm");
};

const cancel = () => {
    if (props.processing) return;

    emit("cancel");
};

const close = () => {
    if (props.processing) return;

    emit("close");
};
</script>

<template>
    <Modal :show="show" max-width="md" :closeable="!processing" @close="close">
        <div
            class="bg-white p-5 text-slate-900 sm:p-6"
            role="alertdialog"
            aria-modal="true"
            :aria-labelledby="titleId"
            :aria-describedby="messageId"
        >
            <div class="flex items-start gap-4">
                <div
                    class="flex h-12 w-12 flex-none items-center justify-center rounded-full border"
                    :class="variantStyles.iconWrap"
                    aria-hidden="true"
                >
                    <component :is="variantStyles.icon" class="h-6 w-6" />
                </div>

                <div class="min-w-0 flex-1">
                    <h2 :id="titleId" class="text-lg font-black text-slate-900">
                        {{ title }}
                    </h2>
                    <p
                        :id="messageId"
                        class="mt-2 text-sm font-medium leading-6 text-slate-600"
                    >
                        {{ message }}
                    </p>
                </div>
            </div>

            <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <button
                    type="button"
                    class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-300 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60"
                    :disabled="processing"
                    @click="cancel"
                >
                    {{ cancelText }}
                </button>

                <button
                    type="button"
                    class="inline-flex min-h-11 items-center justify-center rounded-xl px-4 py-2.5 text-sm font-bold shadow-sm transition focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-70"
                    :class="variantStyles.confirm"
                    :disabled="processing"
                    @click="confirm"
                >
                    {{ processing ? "Procesando..." : confirmText }}
                </button>
            </div>
        </div>
    </Modal>
</template>
