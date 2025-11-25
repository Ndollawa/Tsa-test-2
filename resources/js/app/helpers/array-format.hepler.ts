import { Ref } from "vue";
import { capitalizeFirstLetter } from "./string-format.helper";

/**
 * A utility function to check if the provided data is an array and has at least one element.
 *
 * @param data - The array data to be checked (can be any type)
 * @returns boolean - Returns true if the data is an array and has elements, otherwise false
 */
export const canLoopArray = (data?: Array<any>): boolean => {
    return Array.isArray(data) && data.length > 0;
};

// Generic option formatter
export const optionMap = (
    list: any[] = [],
    labelKey = "title",
    valueKey = "id",
) =>
    list.map((i) => ({
        label: capitalizeFirstLetter(i[labelKey]),
        value: i[valueKey],
    }));

type ArrayHandlingMode = "json" | "multi" | "csv";

interface BuildFormDataOptions {
    skipNulls?: boolean;
    fileFields?: string[];
    arrayHandling?: ArrayHandlingMode;
    multiValueFields?: string[]; // For legacy compatibility
}

/**
 * Builds a FormData object from a flat or nested object.
 * Supports flexible array handling and file fields (including multiple uploads).
 */
export function buildFormData<T extends Record<string, any>>(
    values: T,
    options: BuildFormDataOptions = {},
): FormData {
    const {
        skipNulls = true,
        fileFields = [],
        arrayHandling = "json",
        multiValueFields = [],
    } = options;

    const formData = new FormData();

    for (const key in values) {
        const value = values[key];

        if (
            value === undefined ||
            (skipNulls &&
                (value === null ||
                    value === "" ||
                    (Array.isArray(value) && value.length === 0)))
        ) {
            continue;
        }

        // Handle file fields
        // Handle file fields
        if (fileFields.includes(key)) {
            if (value instanceof File) {
                formData.append(key, value);
            } else if (Array.isArray(value)) {
                for (const item of value) {
                    if (item instanceof File) {
                        formData.append(key, item);
                    } else if (item?.img instanceof File) {
                        formData.append(key, item.img);
                    }
                }
            }

            continue; // prevent fallthrough
        }

        // Handle arrays
        if (Array.isArray(value)) {
            if (arrayHandling === "multi" || multiValueFields.includes(key)) {
                value.forEach((item) => {
                    formData.append(
                        `${key}[]`,
                        item instanceof Object
                            ? JSON.stringify(item)
                            : String(item),
                    );
                });
            } else if (arrayHandling === "csv") {
                formData.append(key, value.join(","));
            } else {
                formData.append(key, JSON.stringify(value));
            }
            continue;
        }

        // Handle objects
        if (typeof value === "object") {
            formData.append(key, JSON.stringify(value));
            continue;
        }

        // Handle primitives
        formData.append(key, String(value));
    }

    return formData;
}

export function omit<T extends Record<string, any>, K extends keyof T>(
    obj: T,
    keys: K[],
): Omit<T, K> {
    const clone = { ...obj };
    for (const key of keys) {
        delete clone[key];
    }
    return clone;
}

export const formatQueryArgs = (queryArgs: Ref<Record<string, any>>) => {
    const rawPage = queryArgs?.value?.params?.page;
    const parsedPage = Number(rawPage);

    const query =
        isNaN(parsedPage) || parsedPage < 1
            ? { ...omit(queryArgs?.value, ["page"]) }
            : {
                  ...queryArgs?.value,
                  params: { ...queryArgs?.value?.params, page: parsedPage },
              };

    return query;
};

export function clean(obj: Record<string, any>) {
    const cleaned: Record<string, any> = {};

    for (const key in obj) {
        const v = obj[key];

        if (
            v === undefined ||
            v === null ||
            v === "" ||
            v === "undefined" ||
            v === "null"
        ) {
            continue;
        }

        cleaned[key] = v;
    }
    return cleaned;
}
