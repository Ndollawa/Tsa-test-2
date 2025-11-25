<template>
    <component :is="dialogRoot" v-model:open="isDialogOpen" v-bind="$attrs">
        <!-- Trigger -->
        <component
            :is="triggerComponent"
            as-child
            v-if="dialogConfig?.useTrigger"
            @click="open()"
        >
            <slot name="dialog-trigger" />
        </component>

        <!-- Dialog Content -->
        <component
            :is="contentComponent"
            v-bind="dialogContent?.attributes"
        >
            <!-- Header -->
            <component
                :is="headerComponent"
                v-bind="dialogHeader?.attributes"
                v-if="dialogHeader?.show !== false"
            >
                <slot name="dialog-header" />

                <component
                    :is="titleComponent"
                    v-if="dialogHeader?.title?.content"
                    v-bind="dialogHeader?.title?.attributes"
                    class="px-3 py-1.5"
                >
                    <template
                        v-if="typeof dialogHeader.title.content === 'string'"
                    >
                        {{ dialogHeader.title.content }}
                    </template>
                    <component
                        v-else
                        :is="dialogHeader.title.content"
                        v-bind="dialogHeader.title.attributes"
                    />
                </component>

                <component
                    :is="descriptionComponent"
                    v-if="dialogHeader?.description?.content"
                    v-bind="dialogHeader?.description?.attributes"
                >
                    <template
                        v-if="
                            typeof dialogHeader.description.content === 'string'
                        "
                    >
                        {{ dialogHeader.description.content }}
                    </template>
                    <component
                        v-else
                        :is="dialogHeader.description.content"
                        v-bind="dialogHeader.description.attributes"
                    />
                </component>
            </component>

            <!-- Body -->
            <div
                :class="
                    cn(
                        'grid mx-auto w-full max-h-[80vh] overflow-auto',
                        dialogBody?.attributes?.class,
                    )
                "
            >
                <ScrollArea class="pl-2 pr-4 scroll-smooth">
                    <slot name="dialog-body" />
                    <ScrollBar orientation="vertical" />
                </ScrollArea>
            </div>

            <!-- Footer -->
            <component
                :is="footerComponent"
                v-if="dialogFooter?.show"
                v-bind="dialogFooter?.attributes"
            >
                <slot name="dialog-footer" />

                <component
                    :is="closeComponent"
                    v-if="dialogClose?.show"
                    v-bind="dialogClose?.attributes"
                    @click="close()"
                >
                    <slot name="dialog-close">{{ dialogClose?.content }}</slot>
                </component>
            </component>
        </component>
    </component>
</template>

<script setup lang="ts">
import { computed, ref } from "vue";
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from "@/components/ui/alert-dialog";
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogClose,
    DialogTitle,
    DialogTrigger,
} from "@/components/ui/dialog";
import { DialogInterface } from "@/app/types/dialog-type";
import { usePersistentDialog } from "@/composables/usePersistentDialog";
import { cn } from "@/lib/utils";

const props = withDefaults(defineProps<DialogInterface>(), {
    variant: "dialog",
});

// ========================
// Persistent dialog setup
// ========================
const hasPersistentKey = computed(() => !!props.dialogConfig?.key);

const {
    open: triggeredOpen,
    openWith,
    closeModal,
} = hasPersistentKey.value
    ? usePersistentDialog(props.dialogConfig.key)
    : { open: ref(false), openWith: () => {}, closeModal: () => {} };

// ========================
// Local open state fallback
// ========================
const openDialog = ref(false);

const isDialogOpen = computed({
    get: () =>
        !props?.dialogConfig?.useTrigger && hasPersistentKey.value
            ? triggeredOpen.value
            : openDialog.value,
    set: (val) => {
        if (hasPersistentKey.value) {
            if (!val) closeModal();
            else openWith();
        } else {
            openDialog.value = val;
        }
    },
});

// ========================
// Control methods
// ========================
const open = () => {
    if (hasPersistentKey.value) openWith();
    else openDialog.value = true;
};

const close = () => {
    if (hasPersistentKey.value) closeModal();
    else openDialog.value = false;
};

defineExpose({ open, close, isDialogOpen });

// ========================
// Variant handling
// ========================
const isAlert = computed(() => props.variant === "alert");

const dialogRoot = computed(() => (isAlert.value ? AlertDialog : Dialog));
const contentComponent = computed(() =>
    isAlert.value ? AlertDialogContent : DialogContent,
);
const headerComponent = computed(() =>
    isAlert.value ? AlertDialogHeader : DialogHeader,
);
const titleComponent = computed(() =>
    isAlert.value ? AlertDialogTitle : DialogTitle,
);
const descriptionComponent = computed(() =>
    isAlert.value ? AlertDialogDescription : DialogDescription,
);
const footerComponent = computed(() =>
    isAlert.value ? AlertDialogFooter : DialogFooter,
);
const closeComponent = computed(() =>
    isAlert.value ? AlertDialogCancel : DialogClose,
);
const triggerComponent = computed(() =>
    isAlert.value ? AlertDialogTrigger : DialogTrigger,
);
</script>
