import { computed } from "vue";

export function useShortPagination(results: any, maxVisible = 5) {
    return computed(() => {
        if (!results?.links) return [];

        const links = results.links;
        const current = results.current_page;
        const last = results.last_page;

        // Laravel's pagination format:
        // [0] => previous
        // [1] => page 1
        // [...]
        // [last] => next

        // If pagination is already short, return original
        if (links.length <= maxVisible + 4) {
            return links;
        }

        // Window calculation
        const half = Math.floor(maxVisible / 2);
        let start = Math.max(2, current - half);
        let end = Math.min(last - 1, start + maxVisible - 1);

        // Adjust left edge
        if (end - start < maxVisible - 1) {
            start = Math.max(2, end - (maxVisible - 1));
        }

        const condensed: any[] = [];

        // Previous button
        condensed.push(links[0]);

        // Always include "1"
        condensed.push(links[1]);

        // Left ellipsis
        if (start > 2) {
            condensed.push({ label: "...", url: null });
        }

        // Middle pages
        for (let p = start; p <= end; p++) {
            condensed.push({
                label: p,
                url: results.path + "?page=" + p,
                active: p === current,
            });
        }

        // Right ellipsis
        if (end < last - 1) {
            condensed.push({ label: "...", url: null });
        }

        // Last page
        condensed.push({
            label: last,
            url: results.path + "?page=" + last,
            active: current === last,
        });

        // Next button
        condensed.push(links[links.length - 1]);

        return condensed;
    });
}
