export const fromCamelCase = (
	str: string,
	capitalizeFirst: boolean = false,
): string => {
	if (!str) return "";
	let result = str
		.replace(/([a-z])([A-Z])/g, "$1 $2") // Add space before uppercase letters
		.toLowerCase(); // Convert the entire string to lowercase

	// If capitalizeFirst is true, capitalize the first letter of each word
	if (capitalizeFirst) {
		result = result
			.split(" ")
			.map((word: string) => capitalizeFirstLetter(word))
			.join(" ");
	}

	return result;
};
export function snakeToTitle(str: string): string {
	if(!str) return "";
	return str?.replace(/_/g, " ")?.replace(/\b\w/g, (char) => char?.toUpperCase());
}
export const toCamelCase = (str: string): string => {
	if (!str) return "";
	return str
		.split(" ")
		.map((word: string, index: number) =>
			index === 0 ? word.toLowerCase() : capitalizeFirstLetter(word),
		)
		.join("");
};

export function capitalizeFirstLetter(word: string): string {
	if (!word) return "";
	if (!word || word.length === 1) {
		return word;
	}
	return word.charAt(0).toUpperCase() + word.slice(1);
}

/**
 * Recursively convert all object keys to camelCase
 */
export const keysToCamelCase = (obj: any): any => {
	if (Array.isArray(obj)) {
		return obj.map((item) => keysToCamelCase(item));
	}

	if (obj !== null && typeof obj === "object") {
		return Object.fromEntries(
			Object.entries(obj).map(([key, value]) => {
				const camelKey = toCamelCase(key.replace(/[_-]/g, " "));
				return [camelKey, keysToCamelCase(value)];
			}),
		);
	}

	return obj;
};
// Regex for digits only (country codes and numbers)
export const dialingCodeRegex = /^\+\d{1,4}$/;
export const phoneRegex = /^[1-9]\d{9}$/;
export const NG_PREFIX = /^(70|80|81|90|91)\d{8}$/;
export const emailRegex =
	/^[a-zA-Z0-9](?!.*[-_.]{2})[a-zA-Z0-9._-]*[a-zA-Z0-9]@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
// Define the schema for each step
