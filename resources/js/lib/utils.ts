
import { InertiaLinkProps } from "@inertiajs/vue3";
import type { Updater } from "@tanstack/vue-table";
import type { ClassValue } from "clsx";

import type { Ref } from "vue";
import { clsx } from "clsx";
import { twMerge } from "tailwind-merge";

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function valueUpdater<T extends Updater<any>>(
    updaterOrValue: T,
    ref: Ref,
) {
    ref.value =
        typeof updaterOrValue === "function"
            ? updaterOrValue(ref.value)
            : updaterOrValue;
}

export function urlIsActive(
    urlToCheck: NonNullable<InertiaLinkProps["href"]>,
    currentUrl: string,
) {
    return toUrl(urlToCheck) === currentUrl;
}

export function toUrl(href: NonNullable<InertiaLinkProps["href"]>) {
    return typeof href === "string" ? href : href?.url;
}
