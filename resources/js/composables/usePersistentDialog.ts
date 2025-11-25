import { persistedDialogConst } from "@/app/constants";
import { computed, ref, onMounted } from "vue";
import { useSearchParams } from "./useSearchParams";

/**
 * Generates a short SHA-256 hash from a given string (first 10 hex chars).
 */
const generateHash = async (paramName: string): Promise<string> => {
    const encoder = new TextEncoder();
    const data = encoder.encode(paramName);
    const hashBuffer = await crypto.subtle.digest("SHA-256", data);
    return Array.from(new Uint8Array(hashBuffer))
        .map((b) => b.toString(16).padStart(2, "0"))
        .join("")
        .substring(0, 10);
};

/**
 * Manages a dialog state persisted in URL query params.
 * Supports open, close, toggle and custom URL sync.
 */
export const usePersistentDialog = (
    paramName: keyof typeof persistedDialogConst,
) => {
    const { getParam, setParams, deleteParam } = useSearchParams();
    const defaultOpenValue = ref<string>("");

    // Compute hash once
    onMounted(async () => {
        if (!defaultOpenValue.value) {
            defaultOpenValue.value = await generateHash(paramName);
        }
    });

    const paramKey = persistedDialogConst[paramName];

    const open = computed({
        get: () => Boolean(getParam(paramKey)),
        set: (val) => {
            if (!val) deleteParam(paramKey);
        },
    });

    const paramValue = computed(() => getParam(paramKey));

    /** Open dialog and sync to URL */
    const openWith = (urlParamValue?: string) => {
        setParams({
            [paramKey]: urlParamValue ?? defaultOpenValue.value,
        });
    };

    /** Close dialog and remove query param */
    const closeModal = () => deleteParam(paramKey);

    /** Toggle between open/close states */
    const toggleOpen = () => (open.value ? closeModal() : openWith());

    return {
        open, // reactive boolean (read/write)
        paramValue, // actual query param value
        openWith,
        closeModal,
        toggleOpen,
        defaultOpenValue, // precomputed hash for default
    };
};
