import { router, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

export function useSearchParams() {
    const page = usePage();

    /**
     * Return all current query params
     */
    const getAllParams = () => {
        return {
            ...(page.url.split("?")[1]
                ? Object.fromEntries(
                      new URLSearchParams(page.url.split("?")[1]).entries(),
                  )
                : {}),
        };
    };

    /**
     * Get one param
     */
    const getParam = (key: string): string => {
        const params = getAllParams();
        return params[key] ?? "";
    };

    /**
     * Update or add query params
     */
    const setParams = (params: Record<string, any>) => {
        const current = getAllParams();

        const updated = Object.fromEntries(
            Object.entries({ ...current, ...params }).filter(([key, val]) => {
                // Remove empty
                if (val === null || val === "" || val === undefined)
                    return false;

                return true;
            }),
        );

        router.get(page.url.split("?")[0], updated, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    };

    /**
     * Delete a single param
     */
    const deleteParam = (key: string) => {
        const current = getAllParams();
        delete current[key];

        router.get(page.url.split("?")[0], current, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    };

    /**
     * Remove all query params
     */
    const deleteAllParams = () => {
        router.get(
            page.url.split("?")[0],
            {},
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            },
        );
    };

    /**
     * Check if filters exist
     */
    const hasFilters = (keys: string[]) => {
        return computed(() => {
            const params = getAllParams();
            return keys.some((key) => {
                const v = params[key];
                return v !== undefined && v !== null && String(v).trim() !== "";
            });
        });
    };

    return {
        getParam,
        getAllParams,
        setParams,
        deleteParam,
        deleteAllParams,
        hasFilters,
    };
}
